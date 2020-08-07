<?php
namespace App\Import;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
// use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMappedCells;

class ItemsImport implements ToCollection, WithHeadingRow, WithChunkReading, ShouldQueue
{
    public function collection(Collection $rows)
    {
        Log::info('data '. json_encode($rows));
        $golongan = "";
        $no_nota = "";
        foreach ($rows as $row) {
            $nama_barang = "";
            $qyt = 0;
            $nilai = 0;
            if($row['golongan'] != null) {
                $golongan = $row['golongan'];
            }
            if($row['no_nota'] != null) {
                $no_nota = $row['no_nota'];
            }
            if($row['nama_barang'] != null) {
                $nama_barang = $row['nama_barang'];
            }
            if($row['qyt'] != null) {
                $qyt = $row['qyt'];
            }
            if($row['nilai'] != null) {
                $nilai = $row['nilai'];
            }
            if($nama_barang != "" && $qyt != 0 && $nilai != 0) {
                DB::table('items')->insert([
                    'member_id' => $golongan,
                    'no_nota' => $no_nota,
                    'nama_barang' => $nama_barang,
                    'qyt' => $qyt,
                    'nilai' => $nilai,
                ]);
            }
        }
        return true;
    }

    public function chunkSize() : int
    {
        return 1000;
    }
}

