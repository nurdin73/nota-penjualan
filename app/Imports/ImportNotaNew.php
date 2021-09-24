<?php

namespace App\Imports;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportNotaNew implements ToCollection, WithHeadingRow, WithChunkReading, ShouldQueue
{

    protected $nameNota;

    public function __construct($nameNota) {
        $this->nameNota = $nameNota;
    }

    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        Log::debug(json_encode($collection));
        return true;    
    }

    public function chunkSize() : int
    {
        return 300;
    }
}
