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
        $checkNotaCode = DB::table('items')->max('no_nota');
        $urut = (int)$checkNotaCode;
        foreach ($rows as $row) {
            $nama_barang = "";
            $qyt = 0;
            $nilai = 0;
            if($row['golongan'] != null) {
                $golongan = $row['golongan'];
                $urut++;
                $no_nota = sprintf("%05s", $urut);
            }
            if($row['nama_barang'] != null) {
                if($row['nama_barang'] != "nama barang") {
                    $nama_barang = $row['nama_barang'];
                }
            }
            if($row['qyt'] != null) {
                if($row['qyt'] != "qyt") {
                    $qyt = $row['qyt'];
                }
            }
            if($row['nilai'] != null) {
                if($row['nilai'] != "nilai") {
                    $nilai = $row['nilai'];
                }
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

