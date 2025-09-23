<?php

namespace App\Services\Implement;

use App\Models\ObservationDate;
use App\Services\ObservationDateService;
use Illuminate\Support\Facades\DB;

class ObservationDateImplement implements ObservationDateService
{
    public function get()
    {
        return ObservationDate::with('times')->get();
    }

    public function show($id)
    {
        return ObservationDate::with('times')->findOrFail($id);
    }

    public function post($data)
    {
        return DB::transaction(function () use ($data) {
            $existing = ObservationDate::where('date', $data['date'])->first();

            if ($existing) {
                throw new \Exception("Observation date {$data['date']} already exists.");
            }

            $observationDate = ObservationDate::create([
                'date' => $data['date'],
                'type' => 1
            ]);

            $times = collect($data['times'])->map(function ($time) use ($observationDate) {
                return [
                    'observation_date_id' => $observationDate->id,
                    'time'   => $time['time'],
                    'max_quota'  => $time['max_quota'],
                ];
            })->toArray();
            $observationDate->times()->createMany($times);
            return $observationDate->load('times');
        });
    }

    public function put($data)
    {
        return DB::transaction(function () use ($data) {
            $observationDate = ObservationDate::findOrFail($data['id']);

            $observationDate->update([
                'date' => $data['date'],
            ]);

            $existingTimeIds = $observationDate->times()->pluck('id')->toArray();
            $newTimeIds = collect($data['times'])->pluck('id')->filter()->toArray();

            $toDelete = array_diff($existingTimeIds, $newTimeIds);
            if (!empty($toDelete)) {
                $observationDate->times()->whereIn('id', $toDelete)->delete();
            }

            foreach ($data['times'] as $time) {
                if (!empty($time['id'])) {
                    $observationDate->times()
                        ->where('id', $time['id'])
                        ->update([
                            'time' => $time['time'],
                            'max_quota'  => $time['max_quota'],
                        ]);
                } else {
                    $observationDate->times()->create([
                        'time' => $time['time'],
                        'max_quota'  => $time['max_quota'],
                    ]);
                }
            }
            return $observationDate->load('times');
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
        return ObservationDate::with('times')->where('date', $date)->where('type', 1)->first();
    }
}
