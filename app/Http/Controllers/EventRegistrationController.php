<?php

namespace App\Http\Controllers;

use App\Mail\AdmissionEmail;
use App\Models\EmailSetting;
use App\Models\Event;
use App\Models\EventFieldAnswer;
use App\Models\EventPriceOption;
use App\Models\EventRegistration;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

use function App\Helpers\codeGenerator;
use function App\Helpers\setupMail;

class EventRegistrationController extends Controller
{
    public function show(Event $event)
    {
        if (! $this->isPubliclyAvailable($event)) {
            if ($event->status === 'DRAFT' || $event->status === 'CLOSED') {
                return view('events.registration.closed', [
                    'event' => $event,
                    'title' => 'Registration Not Available',
                    'message' => 'This event is not yet open for registration.',
                ]);
            }

            if($event->availability_type === 'LIMITED') {
                if ($event->active_until && now()->greaterThan($event->active_until)) {
                    return view('events.registration.closed', [
                        'event' => $event,
                        'title' => 'Registration Closed',
                        'message' => 'Registration for this event has ended.',
                    ]);
                }
            }

            return view('events.registration.closed', [
                'event' => $event,
                'title' => 'Registration Closed',
                'message' => $event->status === 'CLOSED'
                    ? 'This event is no longer accepting registrations.'
                    : 'Registration for this event has ended.',
            ]);
        }

        $fields = $event->fields()
            ->where('is_active', true)
            ->orderBy('order_index', 'asc')
            ->get();

        $priceOptions = $event->priceOptions()
            ->where('is_active', true)
            ->orderBy('order_index', 'asc')
            ->get()
            ->filter(function ($priceOption) {
                if ($priceOption->sales_start_at && now()->lt($priceOption->sales_start_at)) {
                    return false;
                }

                if ($priceOption->sales_end_at && now()->gt($priceOption->sales_end_at)) {
                    return false;
                }

                return true;
            });

        return view('events.registration.form', compact('event', 'fields', 'priceOptions'));
    }

    public function store(Request $request, Event $event)
    {
        $fields = $event->fields()
            ->where('is_active', true)
            ->orderBy('order_index', 'asc')
            ->get();

        $rules = [];

        foreach ($fields as $field) {
            $rules[$field->field_key] = $this->fieldRule($field);

            if ($field->type === 'checkbox') {
                $allowed = $this->allowedOptionValues($field);
                if ($field->allow_other) {
                    $allowed[] = '__OTHER__';
                }
                $rules[$field->field_key . '.*'] = [Rule::in($allowed)];
            }
        }

        $validator = Validator::make($request->all(), $rules);

        $validator->after(function ($validator) use ($fields, $request) {
            foreach ($fields as $field) {
                if (! $field->allow_other || ! in_array($field->type, ['select', 'radio', 'checkbox'], true)) {
                    continue;
                }

                $value = $request->input($field->field_key);
                $hasOther = $field->type === 'checkbox'
                    ? is_array($value) && in_array('__OTHER__', $value, true)
                    : $value === '__OTHER__';

                if ($hasOther && $this->otherValue($field) === '' && $field->is_required) {
                    $validator->errors()->add($field->field_key, 'Please specify the Other value.');
                }
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        foreach ($fields as $field) {
            $request->merge([
                $field->field_key => $this->normalizeSubmittedValue($field, $request->input($field->field_key)),
            ]);
        }

        // $registrationCode = EventRegistration::generateUniqueCode();
        $registrationCode = codeGenerator('event_registrations','code','ECODE-');

        $registration = $event->registrations()->create([
            'code' => $registrationCode,
            'status' => 'SUBMITTED',
            'amount' => 0,
            'registered_at' => now(),
        ]);

        foreach ($fields as $field) {
            $fieldKey = $field->field_key;
            $value = $request->input($fieldKey);

            if ($field->type === 'checkbox') {
                $value = $request->input($fieldKey, []);
                $value = is_array($value) ? $value : [$value];
            }

            if ($field->type === 'attachment') {
                $file = $request->file($fieldKey);
                if (! $file instanceof UploadedFile) {
                    continue;
                }

                $storedValue = $this->storeAttachment($registration, $field, $file);
                $registration->fieldAnswers()->create([
                    'event_field_id' => $field->id,
                    'value' => $storedValue,
                ]);

                continue;
            }

            if ($value === null || $value === '') {
                continue;
            }

            $storedValue = $field->type === 'checkbox' ? json_encode($value) : $value;

            $registration->fieldAnswers()->create([
                'event_field_id' => $field->id,
                'value' => $storedValue,
            ]);
        }

        $this->sendPrimaryRegistrationEmail($event, $registration, $fields, $request);

        return redirect()->route('events.success', ['event' => $event, 'registration_code' => $registration->code])
            ->with('success', 'Your registration has been submitted successfully.');
    }

    protected function sendPrimaryRegistrationEmail(Event $event, EventRegistration $registration, $fields, Request $request)
    {
        $primaryEmailField = $fields->first(function ($field) {
            return $field->type === 'email' && (bool) $field->is_primary_email;
        });

        if (! $primaryEmailField) {
            return;
        }

        $email = trim((string) $request->input($primaryEmailField->field_key, ''));

        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        $fieldValues = [];
        foreach ($fields as $field) {
            $fieldKey = $field->field_key;
            if ($field->type === 'attachment') {
                continue;
            }

            $rawValue = $request->input($fieldKey);

            if ($field->type === 'checkbox') {
                $rawValue = $request->input($fieldKey, []);
            }

            if ($rawValue === null || $rawValue === '') {
                continue;
            }

            $normalizedValue = $this->normalizeEmailTemplateValue($rawValue);
            $fieldValues[$fieldKey] = $normalizedValue;
            $fieldValues['{{ ' . $fieldKey . ' }}'] = $normalizedValue;
            $fieldValues['{{ ' . $fieldKey . ' }}'] = $normalizedValue;
            $fieldValues['{' . $fieldKey . '}'] = $normalizedValue;
            $fieldValues['{{ ' . $fieldKey . ' }}'] = $normalizedValue;
            $fieldValues['{{ ' . $fieldKey . ' }}'] = $normalizedValue;
        }

        $fieldValues['event_title'] = $event->title;
        $fieldValues['event_name'] = $event->title;
        $fieldValues['registration_code'] = $registration->code;
        $fieldValues['{{ event_title }}'] = $event->title;
        $fieldValues['{{ event_title }}'] = $event->title;
        $fieldValues['{event_title}'] = $event->title;
        $fieldValues['{{ registration_code }}'] = $registration->code;
        $fieldValues['{{ registration_code }}'] = $registration->code;
        $fieldValues['{registration_code}'] = $registration->code;

        $template = $event->emailTemplates()->first();

        $subject = $template && ! empty($template->subject)
            ? $template->subject
            : 'Registration Confirmation - ' . $event->title;

        $body = $template && ! empty($template->body)
            ? $template->body
            : '<p>Thank you for registering for <strong>{{ event_title }}</strong>.</p><p>Your registration code is <strong>{{ registration_code }}</strong>.</p>';

        $content = $this->replaceEmailTemplateVariables($body, $fieldValues);

        $branchSetting = EmailSetting::where('branch_id', $event->branch_id)->first();
        if ($branchSetting) {
            setupMail($event->branch_id);
        }

        Mail::to($email)->send(new AdmissionEmail([
            'subject' => $subject,
            'template' => 'email-template.event-registration-template',
            'title' => $event->title,
            'content' => $content,
            'body' => $content,
        ]));
    }

    protected function normalizeEmailTemplateValue($value)
    {
        if (is_array($value)) {
            $value = array_filter(array_map(function ($item) {
                return is_scalar($item) ? trim((string) $item) : null;
            }, $value), function ($item) {
                return $item !== null && $item !== '';
            });

            return implode(', ', $value);
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                $decoded = array_filter(array_map(function ($item) {
                    return is_scalar($item) ? trim((string) $item) : null;
                }, $decoded), function ($item) {
                    return $item !== null && $item !== '';
                });

                return implode(', ', $decoded);
            }
        }

        return trim((string) $value);
    }

    protected function replaceEmailTemplateVariables($body, array $values)
    {
        $body = preg_replace_callback('/\{\{\s*([A-Za-z0-9_\-]+)\s*\}\}|\{([A-Za-z0-9_\-]+)\}/', function ($matches) use ($values) {
            $key = $matches[1] ?? $matches[2] ?? null;

            if ($key === null) {
                return '';
            }

            return array_key_exists($key, $values)
                ? (string) $values[$key]
                : '';
        }, $body);

        return $body ?? '';
    }

    public function success(Event $event, $registration_code)
    {
        return view('events.registration.success', [
            'event' => $event,
            'registration_code' => $registration_code,
        ]);
    }

    protected function isPubliclyAvailable(Event $event)
    {
        if ($event->status !== 'PUBLISHED') {
            return false;
        }

        if ($event->active_until && now()->greaterThan($event->active_until)) {
            return false;
        }

        return true;
    }

    protected function fieldRule($field)
    {
        $base = $field->is_required ? ['required'] : ['nullable'];

        switch ($field->type) {
            case 'text':
                return array_merge($base, ['string', 'max:255']);
            case 'textarea':
                return array_merge($base, ['string']);
            case 'email':
                return array_merge($base, ['email', 'max:255']);
            case 'phone':
                return array_merge($base, ['string', 'max:50']);
            case 'number':
                return array_merge($base, ['numeric']);
            case 'date':
                return array_merge($base, ['date']);
            case 'attachment':
                return array_merge($base, ['file', 'mimes:pdf,jpg,jpeg,png', 'max:10240']);
            case 'select':
            case 'radio':
                $allowed = $this->allowedOptionValues($field);
                if ($field->allow_other) {
                    $allowed[] = '__OTHER__';
                }

                return array_merge($base, ['string', Rule::in($allowed)]);
            case 'checkbox':
                return $field->is_required ? ['required', 'array'] : ['nullable', 'array'];
            default:
                return array_merge($base, ['string']);
        }
    }

    protected function allowedOptionValues($field)
    {
        if ($field->depends_on_field_id && $field->type === 'select') {
            $parent = $field->dependsOnField;
            $parentValue = $parent ? request()->input($parent->field_key) : null;
            $mapping = is_array($field->options_json) ? $field->options_json : [];

            return $this->optionValues($mapping[$parentValue] ?? []);
        }

        $options = is_array($field->options_json) ? $field->options_json : [];
        return $this->optionValues($options);
    }

    protected function optionValues($options): array
    {
        $values = [];

        foreach ((array) $options as $option) {
            if (is_array($option)) {
                $option = $option['value'] ?? '';
            }

            if (is_scalar($option) && trim((string) $option) !== '') {
                $values[] = (string) $option;
            }
        }

        return array_values(array_unique($values));
    }

    protected function normalizeSubmittedValue($field, $value)
    {
        if ($field->type === 'attachment') {
            return null;
        }

        $otherValue = $this->otherValue($field);

        if ($field->type === 'checkbox') {
            $values = is_array($value) ? $value : [];
            $values = array_values(array_filter($values, function ($item) {
                return $item !== null && $item !== '';
            }));

            if (in_array('__OTHER__', $values, true)) {
                $values = array_values(array_filter($values, function ($item) {
                    return $item !== '__OTHER__';
                }));

                if ($otherValue !== '') {
                    $values[] = $otherValue;
                }
            }

            return $values;
        }

        if ($value === '__OTHER__') {
            return $otherValue !== '' ? $otherValue : null;
        }

        return $value;
    }

    protected function otherValue($field): string
    {
        $value = request()->input($field->field_key . '__other', '');

        return is_scalar($value) ? trim((string) $value) : '';
    }

    protected function resolveAvailablePriceOption(Event $event, $priceOptionId)
    {
        if (empty($priceOptionId)) {
            return null;
        }

        $option = EventPriceOption::where('event_id', $event->id)
            ->where('id', $priceOptionId)
            ->where('is_active', true)
            ->first();

        if (! $option) {
            return null;
        }

        if ($option->sales_start_at && now()->lt($option->sales_start_at)) {
            return null;
        }

        if ($option->sales_end_at && now()->gt($option->sales_end_at)) {
            return null;
        }

        if ($option->quota !== null && ((int) $option->sold_count >= (int) $option->quota)) {
            return null;
        }

        return $option;
    }

    protected function extractFirstMatch(array $values, array $keys)
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $values) && $values[$key] !== null && $values[$key] !== '') {
                return $values[$key];
            }
        }

        return null;
    }

    protected function storeAttachment(EventRegistration $registration, $field, UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'bin');
        $safeFieldKey = preg_replace('/[^A-Za-z0-9_\-]/', '_', $field->field_key ?? 'attachment');
        $filename = sprintf('%s_%s.%s', $safeFieldKey, time(), $extension);

        Storage::disk('event')->putFileAs($registration->code, $file, $filename);

        return $registration->code . '/' . $filename;
    }

    protected function generateRegistrationCode()
    {}
}
