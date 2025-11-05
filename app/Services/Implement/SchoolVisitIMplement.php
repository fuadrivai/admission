<?php

namespace App\Services\Implement;

use App\Mail\AdmissionEmail;
use App\Models\SchoolVisit;
use App\Services\SchoolVisitService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

use function App\Helpers\generateUniqueCode;

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
            $code = generateUniqueCode();
            $schoolVisit = SchoolVisit::create([
                'code' => "SVC-$code",
                'parent_name' => $data['parent_name'],
                'phone_number' => $data['phone_number'],
                'email' => $data['email'],
                'zipcode' => $data['zipcode'],
                'child_name' => $data['child_name'],
                'branch_id' => $data['branch_id'],
                'branch_name' => $data['branch_name'],
                'level_id' => $data['level_id'],
                'level_name' => $data['level_name'],
                'grade_id' => $data['grade_id'],
                'grade_name' => $data['grade_name'],
                'academic_year' => $data['academic_year'],
                'current_school' => $data['current_school'],
                'info_from' => $data['info_from'],
                'info_from_message' => $data['info_from_message'],
                'date' => $data['date'],
                'time' => $data['time'],
                'number_visitor' => $data['number_visitor'],
                'already_enrol' => $data['already_enrol'],
                'roles' => $data['roles'],
                'status' => "registered",
            ]);

            $schoolVisit['dateDate'] = Carbon::parse($schoolVisit['date'])->format('l, F j, Y');
            $schoolVisit['subject'] = "MHIS Visit Confirmation";
            $schoolVisit['template'] = 'email-template.schoolvisit';
            Mail::to($schoolVisit->email)->send(new AdmissionEmail($schoolVisit));
            return $schoolVisit;
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
