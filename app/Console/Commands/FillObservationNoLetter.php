<?php

namespace App\Console\Commands;

use App\Models\Observation;
use Carbon\Carbon;
use Illuminate\Console\Command;

class FillObservationNoLetter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'observation:fill-no-letter 
                            {--start=68 : Nomor awal untuk Primary}
                            {--principal="Wilda Liona Suri, S.Si., M.Pd." : Nama principal}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'generate null no_letter';

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
        $start = (int) $this->option('start');
        $principal = $this->option('principal');

        $romanMonths = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'];

        $now = Carbon::now();
        $romanMonth = $romanMonths[$now->month];
        $year = $now->year;

        $levelCode = 'SD - MH'; // khusus Primary
        $prefix = 'PSB';

        // Ambil data observation yang masih null
        $observations = Observation::whereNull('no_letter')
            ->whereIn('level', ['Primary', 'Primary Development Class'])
            ->orderBy('created_at', 'asc')
            ->get();

        if ($observations->isEmpty()) {
            $this->info('Tidak ada observation tanpa no_letter.');
            return;
        }

        $this->info("Mulai mengisi no_letter dari nomor {$start} untuk level Primary...");

        foreach ($observations as $obs) {
            $no = $start++;
            $noLetter = sprintf("%d/%s/%s/B/%s/%d", $no, $prefix, $levelCode, $romanMonth, $year);

            $obs->update([
                'no_letter' => $noLetter,
                'principal' => $principal, // hanya jika kolom ada
            ]);

            $this->line("✅ {$obs->child_name} ({$obs->level}) → {$noLetter}");
        }

        $this->info('Semua no_letter berhasil diisi.');
    }
}
