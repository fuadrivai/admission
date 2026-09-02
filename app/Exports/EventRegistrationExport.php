<?php

namespace App\Exports;

use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EventRegistrationExport implements FromCollection, WithHeadings, WithMapping
{
    private $event;

    private $registrations;

    private $fields;

    private $rowNumber = 0;

    public function __construct($event)
    {
        $this->event = $event;

        $this->fields = $event->fields()
            ->where('is_active', true)
            ->orderBy('order_index')
            ->orderBy('id')
            ->get();

        $this->registrations = $event->registrations()
            ->with('fieldAnswers.eventField')
            ->orderByDesc('created_at')
            ->get();
    }

    public function collection()
    {
        return $this->registrations;
    }

    public function map($registration): array
    {
        $row = [
            ++$this->rowNumber,
            $registration->code ?? '',
        ];

        $answers = $registration->fieldAnswers->keyBy('event_field_id');

        foreach ($this->fields as $field) {
            $answer = $answers->get($field->id);
            $value = $answer ? $answer->value : null;

            if (is_string($value) && $this->isJsonArray($value)) {
                $value = implode(', ', json_decode($value, true));
            }

            if ($field->type === 'attachment' && is_string($value) && $value !== '') {
                $value = $this->resolveAttachmentExportValue($registration, $value);
            }

            $row[] = $value !== null && $value !== '' ? $value : '';
        }

        $row[] = $registration->amount !== null
            ? number_format((float) $registration->amount, 2, '.', '')
            : '';
        $row[] = $registration->status ?? '';

        return $row;
    }

    public function headings(): array
    {
        $headings = [
            'No.',
            'Registration Code',
        ];

        foreach ($this->fields as $field) {
            $headings[] = $field->label ?: $field->field_key;
        }

        $headings[] = 'Amount';
        $headings[] = 'Status';

        return $headings;
    }

    private function isJsonArray($value): bool
    {
        $decoded = json_decode($value, true);

        return json_last_error() === JSON_ERROR_NONE && is_array($decoded);
    }

    private function resolveAttachmentExportValue($registration, string $value): string
    {
        $disk = Storage::disk('event');

        if (! $disk->exists($value)) {
            return $value;
        }

        $route = route('event.registration.attachment', [$this->event, $registration]);

        return $route . '?file=' . urlencode($value);
    }
}
