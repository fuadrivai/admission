<?php

namespace App\Services\Implement;

use App\Mail\AdmissionEmail;
use App\Models\EmailSetting;
use App\Models\Enrolment;
use App\Models\Grade;
use App\Models\Level;
use App\Models\UniformOrder;
use App\Services\XenditCallBackService;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

use function App\Helpers\imageToBase64;
use function App\Helpers\setupMail;

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
            'UORD' => 'uniform_orders',
        ];

        $table = $this->resolveTable($externalId, $transactionMap);

        if (!$table) {
            Log::warning('Unknown transaction type', $data);
            return response()->json(['message' => 'Unknown transaction'], 404);
        }

        if ($table == "enrolments") {
            $this->enrolment($table, $externalId, $data);
        }
        if ($table == "uniform_orders") {
            $this->uniform($table, $externalId, $data);
        }

        return response()->json(['message' => 'Callback processed'], 200);
    }

    private function enrolment($table, $externalId, $data){

        $paidDate     = $data['paid_at'] ?? null;
        $enrolment = Enrolment::where('invoice_id', $externalId)->first();
        $enrolment->update([
            'payment_status' => $this->mapStatus($data['status']),
            'payment_date'   => $paidDate
                ? Carbon::parse($paidDate)
                    ->setTimezone('Asia/Jakarta')
                    ->format('Y-m-d H:i:s')
                : null,
        ]);
        $enrolment->activities()->create([
            'prospects_id' => $enrolment->prospects_id,
            'note'        => "Payment status updated to " . $this->mapStatus($data['status']) . " Via Xendit",
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

            setupMail($enrolment['branch_id']);

            Mail::to($enrolment['email'])
            ->send(
                (new AdmissionEmail($enrolment))
                    ->attach($pdfPath, [
                        'as'   => 'Receipt-'.$enrolment['invoice_id'].'.pdf',
                        'mime' => 'application/pdf',
                    ])
            );
        }
    }
    private function uniform($table, $externalId, $data){
        $paidDate     = $data['paid_at'] ?? null;
        $orderModel   = UniformOrder::with('details')->where('code', $externalId)->first();

        if (!$orderModel) {
            Log::warning("UniformOrder not found for code: {$externalId}");
            return;
        }

        $mappedStatus = $this->mapStatus($data['status']);

        $orderModel->update([
            'payment_status' => $mappedStatus,
            'payment_date'   => $paidDate
                ? Carbon::parse($paidDate)
                    ->setTimezone('Asia/Jakarta')
                    ->format('Y-m-d H:i:s')
                : null,
        ]);

        if (in_array(strtoupper($data['status']), ['PAID', 'SETTLED'])) { 
            $level_name = Level::find($orderModel->level_id)->name ?? $orderModel->level_name ?? '-';
            $grade_name = Grade::find($orderModel->grade_id)->name ?? $orderModel->grade_name ?? '-';

            $order = $orderModel->toArray();
            $order['level_name'] = $level_name;
            $order['grade_name'] = $grade_name;
            $order['items']      = $orderModel->details ? $orderModel->details->toArray() : [];

            $pdfPath = $this->generateUniformInvoicePdf($order, $data['description'] ?? null);
            $order['subject']  = "Uniform Payment Confirmation - Mutiara Harapan Islamic School";
            $order['template'] = 'email-template.uniform-confirmation';

            if (!empty($order['branch_id'])) {
                setupMail($order['branch_id']);
            }

            if (!empty($order['parent_email'])) {
                Mail::to($order['parent_email'])
                    ->send(
                        (new AdmissionEmail($order))
                            ->attach($pdfPath, [
                                'as'   => 'Receipt-' . $order['code'] . '.pdf',
                                'mime' => 'application/pdf',
                            ])
                    );
            }
        }
    }

    private function generateUniformInvoicePdf(array $order, $description = null)
    {
        $logoPath = public_path('assets/images/Logo-all-branch.png');
        $imageBase64 = imageToBase64($logoPath);
        $html = view('pdf.uniform-invoice', [
            'invoice_no'        => $order['code'],
            'payment_date'      => isset($order['payment_date']) && $order['payment_date'] 
                                    ? Carbon::parse($order['payment_date'])->format('d M Y') 
                                    : Carbon::now()->format('d M Y'),
            'student_name'      => $order['student_name'] ?? '-',
            'bank_charger'      => number_format($order['bank_charger'] ?? 0, 0, ',', '.'),
            'total'             => number_format($order['total_amount'] ?? 0, 0, ',', '.'),
            'level_name'        => $order['level_name'] ?? '-',
            'grade_name'        => $order['grade_name'] ?? '-',
            'description'       => $description ?? 'Uniform Purchase Payment',
            'logo'              => $imageBase64,
            'items'             => $order['items'] ?? [],
        ])->render();

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4');
        $dompdf->render();

        $path = $order['code'] . '/receipt-' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $order['student_name'] ?? 'student') . '-' . $order['code'] . '.pdf';

        Storage::disk('admission')->put($path, $dompdf->output());

        return Storage::disk('admission')->path($path);
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

        $path = $enrolment['code']. '/receipt-'.$enrolment['child_name'].'-'. $enrolment['invoice_id']. '.pdf';

        Storage::disk('admission')->put($path, $dompdf->output());

        return Storage::disk('admission')->path($path);
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
