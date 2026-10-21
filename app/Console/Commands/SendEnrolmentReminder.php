<?php

namespace App\Console\Commands;

use App\Mail\AdmissionEmail;
use App\Models\EmailSetting;
use App\Models\EnrolmentTransaction;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

class SendEnrolmentReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:enrolment';
    protected $description = 'Send enrolment payment reminders';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $today = Carbon::today();
        $this->processReminder($today, 3, 'H+3');
        $this->processReminder($today, 6, 'H+6');
        $this->processExpiryReminder($today);
    }

    private function processReminder($today, $daysAfterCreate, $type)
    {
        $targetDate = $today->copy()->subDays($daysAfterCreate);

        $transactions = EnrolmentTransaction::with('enrolment')
            ->whereDate('create_va_date', $targetDate)
            ->whereRaw('UPPER(payment_status) = ?', ['PENDING'])
            ->whereHas('details', function ($query) {
                $query->whereIn('type', ['enrolment', 'streaming_test']);
            })
            ->get();

        foreach ($transactions as $transaction) {
            $enrolment = $transaction->enrolment;
            if (!$enrolment) {
                Log::warning('Skipping enrolment reminder for a transaction without an enrolment.', [
                    'transaction_id' => $transaction->id,
                ]);
                continue;
            }

            if ($this->alreadySent($enrolment->id, 'enrolments', $type)) {
                continue;
            }

            $this->sendNotification($enrolment, $transaction, $type);
            $this->logReminder($enrolment->id, 'enrolments', $type);
        }
    }

    private function processExpiryReminder($today)
    {
        $transactions = EnrolmentTransaction::with('enrolment')
            ->whereDate('expiry_va_date', $today)
            ->whereRaw('UPPER(payment_status) = ?', ['PENDING'])
            ->whereHas('details', function ($query) {
                $query->whereIn('type', ['enrolment', 'streaming_test']);
            })
            ->get();

        foreach ($transactions as $transaction) {
            $enrolment = $transaction->enrolment;
            if (!$enrolment) {
                Log::warning('Skipping enrolment expiry reminder for a transaction without an enrolment.', [
                    'transaction_id' => $transaction->id,
                ]);
                continue;
            }

            if ($this->alreadySent($enrolment->id, 'enrolments', 'H+7')) {
                continue;
            }

            $this->sendNotification($enrolment, $transaction, 'H+7');
            $this->logReminder($enrolment->id, 'enrolments', 'H+7');
        }
    }

    private function alreadySent($id, $source, $type)
    {
        return DB::table('reminders')
            ->where('table_data_id', $id)
            ->where('source', $source)
            ->where('reminder_type', $type)
            ->exists();
    }

    private function logReminder($id, $source, $type)
    {
        DB::table('reminders')->insert([
            'table_data_id' => $id,
            'source' => $source,
            'reminder_type' => $type,
            'sent_date' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function sendNotification($enrolment, EnrolmentTransaction $transaction, $type)
    {
        $enrolment['payment_url'] = $transaction->payment_url;
        $enrolment['invoice_id'] = $transaction->invoice_id;
        $enrolment['payment_status'] = $transaction->payment_status;

        $enrolment['subject'] = "Enrolment Payment of $enrolment->child_name - Mutiara Harapan Islamic School";
        $enrolment['template'] = 'email-template.enrolment-reminder';
        $setting = EmailSetting::where('branch_id',$enrolment->branch_id)->first();
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

        Mail::to($enrolment->email)->send(new AdmissionEmail($enrolment));
        Log::info("Send {$type} reminder to enrolment {$enrolment->id}");
    }
}
