<?php

namespace App\Services\Implement;

use App\Models\ParentUser;
use App\Models\StudentPortal;
use App\Services\ParentsStudentService;
use Illuminate\Support\Facades\DB;

class ParentsStudentImplement implements ParentsStudentService
{
    public function get($request)
    {
        $parent = ParentUser::where('username', $request['username'])
            ->where('pass', $request['password'])
            ->first();

        if (!$parent) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid username or password'
            ], 404);
        }

        // Ambil RFID dari pivot
        $rawRfid = DB::connection('mysql_school')
            ->table('parents_students')
            ->where('parents_id', $parent->parents_id)
            ->pluck('rfidid')
            ->toArray();

        // Format RFID menjadi 8 digit
        $formattedRfid = array_map(function ($r) {
            return str_pad($r, 8, '0', STR_PAD_LEFT);
        }, $rawRfid);

        // Ambil data student
        $students = StudentPortal::whereIn('rfidid', $formattedRfid)->get();

        $levelNames = [
            1 => 'Preschool',
            2 => 'Primary',
            3 => 'Secondary',
            4 => 'DC'
        ];

        return response()->json([
            'id' => $parent->parents_id,
            'fullname' => $parent->fullname,
            'username' => $parent->username,
            'children' => $students->map(function ($s) use ($levelNames) {

                $cleanNis = preg_replace('/-(A|B)$/', '', $s->nis);

                return [
                    'id' => $s->id,
                    'rfidid' => $s->rfidid,
                    'nis' => $cleanNis,
                    'student_name' => $s->student_name,
                    'grade' => $s->grade,
                    'level' => $levelNames[$s->level] ?? 'Unknown'
                ];
            })
        ]);
    }
}
