<?php

namespace App\Services\Implement;

use App\Mail\AdmissionEmail;
use App\Models\EmailSetting;
use App\Models\Enrolment;
use App\Models\Grade;
use App\Models\Level;
use App\Services\XenditCallBackService;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

use function App\Helpers\imageToBase64;

class XenditCallBackImplement implements XenditCallBackService
{
    public function post($data)
    {
        $externalId = $data['external_id'] ?? null;
        $status     = $data['status'] ?? null;

        if (!$externalId || !$status) {
            return response()->json(['message' => 'Invalid payload'], 400);
        }

        $transactionMap = [
            'INV-ENROL'   => 'enrolments',
        ];

        $table = $this->resolveTable($externalId, $transactionMap);

        if (!$table) {
            Log::warning('Unknown transaction type', $data);
            return response()->json(['message' => 'Unknown transaction'], 404);
        }

        if ($table == "enrolments") {
            $this->enrolment($table, $externalId, $data);
        }

        return response()->json(['message' => 'Callback processed'], 200);
    }

    private function enrolment($table, $externalId, $data){

        $paidDate     = $data['paid_at'] ?? null;
        Enrolment::where('invoice_id', $externalId)
            ->update([
                'payment_status'     => $this->mapStatus($data['status']),
                'payment_date'    => Carbon::parse($paidDate)->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s'),
            ]);
        $enrolment = (array) DB::table($table)
            ->where('invoice_id', $externalId)
            ->first();

        if ($data['status']=="PAID") { 
            $level_name = Level::find($enrolment['level_id'])->name;
            $grade_name = Grade::find($enrolment['grade_id'])->name;
            $enrolment['level_name'] = $level_name;
            $enrolment['grade_name'] = $grade_name;

            $pdfPath = $this->generateEnrolmentInvoicePdf($enrolment,$data['description'] ?? null);
            $enrolment['subject'] = "Enrolment Documents - Mutiara Harapan Islamic School";
            $enrolment['template'] = 'email-template.enrolment-confirmation';
            $enrolment['link'] = 'https://admission.mhis.link/enrolment/student?code='.$enrolment['code'];

            $this->setupMail($enrolment['branch_id']);

            Mail::to($enrolment['email'])
            ->send(
                (new AdmissionEmail($enrolment))
                    ->attach($pdfPath, [
                        'as'   => 'Invoice-'.$enrolment['invoice_id'].'.pdf',
                        'mime' => 'application/pdf',
                    ])
            );
        }
    }

    private function generateEnrolmentInvoicePdf(array $enrolment, $description = null)
    {
        $logoPath = public_path('assets/images/Logo-all-branch.png');
        $imageBase64 = imageToBase64($logoPath);
        $html = view('pdf.invoice', [
            'invoice_no'        => $enrolment['invoice_id'],
            'payment_date'      => Carbon::parse($enrolment['payment_date'])->format('d M Y'),
            'student_name'      => $enrolment['child_name'],
            'registration_fee'  => number_format($enrolment['registration_fee'], 0, ',', '.'),
            'bank_charger'       =>  number_format($enrolment['bank_charger'], 0, ',', '.'),
            'total'             => number_format($enrolment['amount_paid'], 0, ',', '.'),
            'academic_year'     => $enrolment['academic_year'],
            'level_name'        => $enrolment['level_name'],
            'grade_name'        => $enrolment['grade_name'],
            'description'       => $description ?? 'Enrolment Payment',
            'logo'              => $imageBase64,
        ])->render();

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4');
        $dompdf->render();

        $path = 'invoices/enrolment/'.$enrolment['invoice_id'].'.pdf';

        Storage::put($path, $dompdf->output());

        return storage_path('app/'.$path);
    }

    private function setupMail($branchId)
    {
        $setting = EmailSetting::where('branch_id', $branchId)->first();

        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp', [
            'transport' => $setting->mailer,
            'host'      => $setting->host,
            'port'      => $setting->port,
            'encryption'=> $setting->encryption,
            'username'  => $setting->username,
            'password'  => $setting->app_password,
        ]);

        Config::set('mail.from', [
            'address' => $setting->from_address,
            'name'    => $setting->from_name,
        ]);
    }

    private function resolveTable(string $externalId, array $map): ?string
    {
        foreach ($map as $prefix => $table) {
            if (str_starts_with($externalId, $prefix)) {
                return $table;
            }
        }
        return null;
    }

    private function mapStatus(string $xenditStatus): string
    {
        $map = [
            'PAID'    => 'PAID',
            'SETTLED' => 'PAID',
            'EXPIRED' => 'EXPIRED',
            'FAILED'  => 'FAILED',
        ];

        return $map[$xenditStatus] ?? 'PENDING';
    }
}
