<?php

namespace App\Services\Implement;

use App\Google\GoogleSheetService;
use App\Mail\AdmissionEmail;
use App\Models\Observation;
use App\Models\ObservationDate;
use App\Models\ObservationTime;
use App\Services\ObservationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

use function App\Helpers\codeGenerator;
use function App\Helpers\generateNoLetter;

class ObservationImplement implements ObservationService
{
    public function get()
    {
        return Observation::with('times')->get();
    }

    public function show($id)
    {
        return Observation::with('times')->findOrFail($id);
    }

    public function post($data)
    {
        return DB::transaction(function () use ($data) {
            $observationTime = ObservationTime::find($data['observation_time_id']);
            $observationDate = ObservationDate::find($observationTime->observation_date_id);
            if ($observationDate->is_active != 1) {
                throw ValidationException::withMessages([
                    'date' => 'Tanggal observasi tidak aktif, silahkan hubungi admission'
                ]);
            }
            $observation = Observation::create([
                'code' => codeGenerator('OBV'),
                'no_letter' => generateNoLetter($data['level']),
                'principal' => $data['principal'],
                'child_name' => $data['child_name'],
                'gender' => $data['gender'],
                'level' => $data['level'],
                'phone' => $data['phone'],
                'email' => $data['email'],
                'relationship' => $data['relationship'],
                'parent_name' => $data['parent_name'],
                'date' => $data['date'],
                'time' => $data['time'],
                'observation_time_id' => $data['observation_time_id'],
                'level_id' => $data['level_id'],
                'level' => $data['level'],
                'grade_id' => $data['grade_id'],
                'grade' => $data['grade'],
                'status' => "Registered"
            ]);

            $observation->time = Carbon::parse($observation->time)->format('H:i');
            $observation['subject'] = "Observation Schedule for $observation->child_name - $observation->level MHIS";
            $observation['day'] = Carbon::parse($observation->date)->locale('en')->translatedFormat('l, d F Y');
            $observation['template'] = 'email-template.observation';
            Mail::to($observation->email)->send(new AdmissionEmail($observation));
            $this->sendToGsheet($observation);
            return $observation;
        });
    }

    public function put($data)
    {
        return DB::transaction(function () use ($data) {
            $observation = Observation::findOrFail($data['id']);

            $observation->update([
                'child_name' => $data['child_name'],
                'gender' => $data['gender'],
                'level' => $data['level'],
                'phone' => $data['phone'],
                'email' => $data['email'],
                'relationship' => $data['relationship'],
                'parent_name' => $data['parent_name'],
                'date' => $data['date'],
                'time' => $data['time'],
                'observation_time_id' => $data['observation_time_id'],
            ]);


            return $observation->load('times');
        });
    }

    public function fetch($data)
    {
        return DB::transaction(function () use ($data) {
            $observation = Observation::findOrFail($data['id']);

            $observation->update([
                'date' => $data['date'],
                'time' => $data['time'],
                'observation_time_id' => $data['observation_time_id'],
            ]);

            $observation->refresh()->load('time');

            $observation->time = Carbon::createFromFormat('H:i:s', $observation->time)->format('H:i');
            $observation['subject'] = "Observation Schedule for $observation->child_name - $observation->level MHIS";
            $observation['day'] = Carbon::parse($observation->date)
                ->locale('en')
                ->translatedFormat('l, d F Y');
            Mail::to($observation->email)->send(new AdmissionEmail($observation));

            $sheetService = new GoogleSheetService();
            $sheetService->updateDateTimeByCode($observation->code, $data['date'], $data['time']);
            return $observation;
        });
    }

    public function delete($id)
    {
        $observationDate = ObservationDate::findOrFail($id);
        $observationDate->delete();
        return true;
    }
    public function getByDate($date)
    {
        return ObservationDate::with('times')->where('date', $date)->first();
    }

    private function sendToGsheet($observation)
    {
        $sheetService = new GoogleSheetService();

        // Data dummy untuk testing
        $data = [
            '',                                         // ID dummy
            (string) $observation->code,                // Observation Code
            (string) $observation->child_name,          // Nama anak
            (string) $observation->gender,              // Gender
            (string) $observation->level,               // Level
            (string) $observation->grade,               // Grade
            (string) $observation->parent_name,         // Nama orang tua
            (string) $observation->relationship,        // Relationship
            (string) $observation->phone,               // Phone
            (string) $observation->email,               // Email
            (string) $observation->date,                // Date
            (string) $observation->time,                // Time
            (string) $observation->status,              // Status
            (string) $observation->no_letter,              // Status
            (string) $observation->principal,              // Status
            (string) $observation->created_at->toDateTimeString(), //
        ];

        $data = array_map('strval', $data);

        $sheetService->appendRow($data);
    }
}
