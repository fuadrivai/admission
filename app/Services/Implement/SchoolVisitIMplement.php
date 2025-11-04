<?php

namespace App\Services\Implement;

use App\Mail\AdmissionEmail;
use App\Models\SchoolVisit;
use App\Services\SchoolVisitService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

use function App\Helpers\codeGenerator;

class SchoolVisitIMplement implements SchoolVisitService
{
    public function get()
    {
        return SchoolVisit::all();
    }

    public function show($id)
    {
        $visit = SchoolVisit::find($id);

        if (!$visit) {
            return response()->json(['message' => 'Data not found'], 404);
        }
        return $visit;

    }

    public function post($data)
    {
        return DB::transaction(function () use ($data) {
            
            $observation = SchoolVisit::create([
                'code' => codeGenerator('SVC'),
                'status' => "registered",
                ...$data->validate(),
            ]);

            $observation->time = Carbon::parse($observation->time)->format('H:i');
            $observation['subject'] = "Observation Schedule for $observation->child_name - $observation->level MHIS";
            $observation['day'] = Carbon::parse($observation->date)->locale('en')->translatedFormat('l, d F Y');
            Mail::to($observation->email)->send(new AdmissionEmail($observation));
            return $observation;
        });
    }

    public function put($data)
    {
        $visit = SchoolVisit::find($data['id']);
        if (!$visit) {
            return response()->json(['message' => 'Data not found'], 404);
        }
        $visit->update($data->all());
        return $visit;
    }

    public function fetch($data)
    {
        
    }

    public function delete($id)
    {
        $visit = SchoolVisit::find($id);
        if (!$visit) {
            return response()->json(['message' => 'Data not found'], 404);
        }
        $visit->delete();
    }
    
}
