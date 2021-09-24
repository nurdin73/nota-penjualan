<?php

namespace App\Jobs;

use App\Http\Helpers\PrintNota;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ImportNotaNewJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    protected $excel;
    protected $dates;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Array $excel, String $dates)
    {
        $this->excel = $excel;
        $this->dates = $dates;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Excel::import(new ImportNotaNew($this->dates), $this->excel);
        $cacheNumber = Cache::get($this->dates, 0);
        $urut = (int)$cacheNumber;
        foreach($this->excel as $excel) {
            $memberId = explode(' ', $excel[0]);
            $memberId = $memberId[0] . " " . $memberId[1];
            $newNominal = $excel[2];
            $urut++;
            $no_nota = sprintf("%05s", $urut);
            $no_nota = $this->dates . '/' . $no_nota;
            $data = [
                'no_nota' => $no_nota,
                'member_id' => $memberId,
                'newNominal' => $newNominal
            ];
            $print = new PrintNota($data);
            $printing = $print->printing();
            Log::info($printing);
        }
        $update = Cache::put($this->dates, $cacheNumber + count($this->excel));
        $cacheNumber = Cache::get($this->dates);
    } 
}
