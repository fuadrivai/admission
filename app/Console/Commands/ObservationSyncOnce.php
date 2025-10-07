<?php

namespace App\Console\Commands;

use App\Google\GoogleSheetService;
use Illuminate\Console\Command;
use App\Models\Observation;
use Carbon\Carbon;
use Google\Client;
use Google\Service\Sheets;

class ObservationSyncOnce extends Command
{
    /**
     * Nama command yang digunakan di terminal
     */
    protected $signature = 'observations:sync-once';

    /**
     * Deskripsi command
     */
    protected $description = 'Generate null codes and sync all observations to Google Sheets (one-time)';

    /**
     * Jalankan command
     */
    public function handle()
    {
        $this->info("=== Memulai proses sinkronisasi observation ===");

        // Langkah 1: Update field code yang masih null
        $this->updateMissingCodes();

        // Langkah 2: Sinkronisasi semua data ke Google Sheets
        $this->syncToGoogleSheet();

        $this->info("=== Proses selesai ===");
        return Command::SUCCESS;
    }

    /**
     * Generate kode untuk data observation yang belum memiliki code
     */
    protected function updateMissingCodes()
    {
        $this->info("Mengupdate kode untuk observation yang belum memiliki code...");

        $observations = Observation::whereNull('code')
            ->orderBy('created_at')
            ->get();

        $groupedByDate = [];

        foreach ($observations as $observation) {
            $dateKey = Carbon::parse($observation->created_at)->format('ymd');

            if (!isset($groupedByDate[$dateKey])) {
                $groupedByDate[$dateKey] = 1;
            } else {
                $groupedByDate[$dateKey]++;
            }

            $order = str_pad($groupedByDate[$dateKey], 4, '0', STR_PAD_LEFT);
            $observation->code = "OBV{$dateKey}{$order}";
            $observation->save();

            $this->line("Updated ID {$observation->id} => {$observation->code}");
        }

        $this->info("Selesai update kode observation.");
    }

    /**
     * Sinkronisasi semua data ke Google Sheets
     */
    protected function syncToGoogleSheet()
    {
        $sheetService = new GoogleSheetService();
        $this->info("Mengirim data observation ke Google Sheet...");

        $observations = Observation::orderBy('created_at')->get();

        foreach ($observations as $o) {
            $dateValue = (string) $o->date;
            $timeValue = (string) $o->time;

            try {
                $dateFormatted = $dateValue ? Carbon::parse($dateValue)->format('d M Y') : '';
            } catch (\Exception $e) {
                $dateFormatted = '';
            }

            try {
                $timeFormatted = $timeValue ? Carbon::parse($timeValue)->format('H:i') : '';
            } catch (\Exception $e) {
                $timeFormatted = '';
            }

            $value = [
                '',                               // ID dummy
                (string) $o->code,
                (string) $o->child_name,
                (string) $o->gender,
                (string) $o->level,
                (string) $o->grade,
                (string) $o->parent_name,
                (string) $o->relationship,
                (string) $o->phone,
                (string) $o->email,
                $dateFormatted,
                $timeFormatted,
                (string) $o->status,
                (string) $o->created_at->toDateTimeString(),
            ];

            $data = array_map('strval', $value);
            $sheetService->appendRow($data);
        }
    }
}
