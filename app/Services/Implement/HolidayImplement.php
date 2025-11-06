<?php

namespace App\Services\Implement;

use App\Models\Holiday;
use App\Services\HolidayService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class HolidayImplement implements HolidayService
{
    public function get()
    {
        return Holiday::orderBy('date','asc')->get();
    }
    public function nextHoliday()
    {
        $today = Carbon::today();
        return Holiday::where('date', '>=', $today)->orderBy('date', 'asc')->get();
    }

    public function show($id)
    {
        return Holiday::findOrFail($id);
    }

    public function isHoliday($date)
    {
        $holiday =  Holiday::where('date', $date)->first();
        return $holiday;
    }

    public function post($data)
    {
        $enddate = !empty($data['end-date'])?$data['end-date']:null;
        if (is_null($enddate)) {
            $holiday = Holiday::create([
                'date'          => $data['date'],
                'description'   => $data['description'],
            ]);
            return $holiday;
        }
        $period = CarbonPeriod::create($data['date'], $data['end-date']);
        foreach ($period as $date) {
            if (!Holiday::where('date', $date)->exists()){
                $holiday = Holiday::create([
                    'date'          => $date,
                    'description'   => $data['description'],
                ]);
            }
        }
        return response()->json('success');
    }

    public function put($data)
    {
        $holiday = Holiday::findOrFail($data['id']);

        $holiday->update([
            'date'     => $data['date'],
            'description'     => $data['description'],
        ]);

        return $holiday;
    }

    public function delete($id)
    {
        $holiday = Holiday::findOrFail($id);
        return $holiday->delete();
    }
}
