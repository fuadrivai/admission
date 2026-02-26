<?php

namespace App\Services\Implement;

use App\Mail\AdmissionEmail;
use App\Models\EmailSetting;
use App\Models\Level;
use App\Models\MaxCapacity;
use App\Models\SchoolVisit;
use App\Models\WhatsappCode;
use App\Services\ProspectService;
use App\Services\SchoolVisitService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

use function App\Helpers\generate;
use function App\Helpers\normalizePhoneNumber;
use function App\Helpers\sendWhatsapp;

class SchoolVisitIMplement implements SchoolVisitService
{


    protected $prospectService;

    public function __construct(ProspectService $prospectService)
    {
        $this->prospectService = $prospectService;
    }
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
    public function showByCode($code)
    {
        $visit = SchoolVisit::with(['prospect','branch','level','grade'])-> where('code', $code)->first();

        if (!$visit) {
            throw new \Exception('Data not found');
        }
        return $visit;
    }

    public function post($data)
    {
        return DB::transaction(function () use ($data) {
            $isMax = $this-> getByDateTime($data);

            if ($isMax) {
                throw new \Exception("The selected time slot for the school visit is already full. Kindly choose a different time.");
            }

            // $code = generateUniqueCode();
            $phone = normalizePhoneNumber($data['phone_number']);
            $value = [
                'code' => null,
                'prospects_id' => null,
                'already_enrol' => $data['already_enrol'],
                'parent_name' => $data['parent_name'],
                'phone_number' => $phone,
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
            ];

            if ($value['already_enrol']!="yes") {
                $level = Level::find($value['level_id']);
                $value['code'] = generate($level->branch_code);
                $prospects = $this->saveProspects($value);
                $value['prospects_id'] = $prospects->id;
            }else{
                $value['code'] = $data['code'];
                if(!isset($data['prospects_id']) || is_null($data['prospects_id']) || empty($data['prospects_id'])||$data['prospects_id'] == ''){
                    $prospects = $this->saveProspects($value);   
                    $value['prospects_id'] = $prospects->id;
                }else{
                    $value['prospects_id'] = $data['prospects_id'];
                }
            }

            $schoolVisit = SchoolVisit::create($value);

            $schoolVisit->activities()->create([
                'prospect_id' =>  $value['prospects_id'],
                'note'=>"Registered for school visit on " . Carbon::parse($schoolVisit->date)->format('F j, Y') . " at " . Carbon::parse($schoolVisit->time)->format('g:i A'),
            ]);

            $schoolVisit['dateDate'] = Carbon::parse($schoolVisit['date'])->format('l, F j, Y');
            $schoolVisit['subject'] = "MHIS Visit Confirmation";
            $schoolVisit['template'] = 'email-template.schoolvisit';

            $setting = EmailSetting::where('branch_id',$value['branch_id'])->first();
            Config::set('mail.default', 'smtp');
            Config::set('mail.mailers.smtp', [
                'transport' => $setting->mailer,
                'host' => $setting->host,
                'port' => $setting->port,
                'encryption' => $setting->encryption,
                'username' => $setting->username,
                'password' => $setting->app_password,
                'timeout' => null,
            ]);
            Config::set('mail.from', [
                'address' => $setting->from_address,
                'name' => $setting->from_name,
            ]);

            Mail::to($schoolVisit->email)->send(new AdmissionEmail($schoolVisit));

            $sender = WhatsappCode::where('branch_id',$data['branch_id'])->first();
            sendWhatsapp($phone,$sender->code, $this->bintaroMessage($data['parent_name'],$schoolVisit['dateDate'],$data['time'],$data['level_name']));
            return $schoolVisit;
        });
    }

    public function put($visit, $data)
    {
        $visit->update($data);

        $visit->activities()->create([
            'prospects_id' => $visit->prospects_id,
            'note'=>"Update for school visit status to " . $data['status'] . " on " . Carbon::now()->format('F j, Y'),
        ]);
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
    
    public function maxCapacity()
    {
        return MaxCapacity::get()->first();
    }

    public function postMax($data)
    {
        $max = MaxCapacity::find($data['id']);
        if (!$max) {
            return response()->json(['message' => 'Data not found'], 404);
        }
        $max->update($data);
        return $max;
    }
    public function getByDateTime($data)
    {
        $visitorsCount = SchoolVisit::where('date', $data['date'])
            ->where('time', $data['time'])
            ->count();
        $max = MaxCapacity::first();

        return ($visitorsCount >= $max->max);
    }

    public function changeStatusViaCron() {
        $today = Carbon::today()->toDateString();
        $visits = SchoolVisit::where('date', '<', $today)
            ->where('status', 'registered')
            ->get();

        foreach ($visits as $visit) {
            $visit->status = 'absent';
            $visit->save();

            $visit->activities()->create([
                'prospects_id' => $visit->prospects_id,
                'note'=>'Status change by System: Marked as absent due to no-show on ' . Carbon::parse($visit->date)->format('F j, Y'),
            ]);
        }
    }

    function bintaroMessage($parentName,$date,$time,$level){
        return "Assalamualaikum Warahmatullahi Wabarakatuh\n
Dear Mr./Mrs. $parentName, warm greetings from Mutiara Harapan Islamic School. Your visit to our $level MHIS is scheduled on:\n
Date: $date
Time: $time\n
By confirming your visit, you agree to:
- Comply with school regulations.
- Refrain from taking photos/videos of students and staff.
- Dress modestly (men: long trousers; women: long trousers or ankle-length skirts/dresses; min. short sleeves).
- Wear a mask if feeling unwell.\n
We look forward to welcoming you to our warm and friendly school environment.\n
Wassalamualaikum Warahmatullahi Wabarakatuh
Mutiara Harapan Islamic School";
    }

    function saveProspects($data)
    {
        $prospectData = [
            'code' => $data['code'],
            'child_name' => $data['child_name'],
            'current_school' => $data['current_school'],
            'parent_name' => $data['parent_name'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'zipcode' => $data['zipcode'],
            'address' => null,
            'relationship' => null,
            'date_of_birth' => null,
            'place_of_birth' => null,
            'source_module' => 'schoolvisit',
        ];

        return $this->prospectService->post($prospectData);
    }
    
}
