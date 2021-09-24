<?php
namespace App\Http\Helpers;

use Illuminate\Support\Facades\Log;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;

class PrintTrx
{
    protected $data;
    public function __construct($data) {
        $this->data = $data;
    }
    
    protected function create4Column($column1, $column2, $column3)
    {

        // lebar column
        $sizeColumn1 = 20; 
        $sizeColumn2 = 6; 
        $sizeColumn3 = 10;
        
        // wordwrap
        $column1 = wordwrap($column1, $sizeColumn1, "\n", true);
        $column2 = wordwrap($column2, $sizeColumn2, "\n", true);
        $column3 = wordwrap($column3, $sizeColumn3, "\n", true);

        $column1Arr = explode("\n", $column1);
        $column2Arr = explode("\n", $column2);
        $column3Arr = explode("\n", $column3);

        $countRowMax = max(count($column1Arr), count($column2Arr), count($column3Arr));

        $resultRow = [];

        for ($i=0; $i < $countRowMax; $i++) { 
            
            // add space
            $resultColumn1 = str_pad((isset($column1Arr[$i]) ? $column1Arr[$i] : ""), $sizeColumn1, " ");
            $resultColumn2 = str_pad((isset($column2Arr[$i]) ? $column2Arr[$i] : ""), $sizeColumn2, " ");

            // align right
            $resultColumn3 = str_pad((isset($column3Arr[$i]) ? $column3Arr[$i] : ""), $sizeColumn3, " ", STR_PAD_LEFT);

            // push to array result
            $resultRow[] = $resultColumn1 . " " . $resultColumn2 . " " . $resultColumn3;

        }

        return implode($resultRow, "\n") . "\n";
    }

    function invoice()
    {
        try {
            $PRINTER_DEVICE = env('PRINTER_DEVICE', "EPSON TM-U220 Receipt");
            $connector = "";
            // $connector = new WindowsPrintConnector($PRINTER_DEVICE); // ini untuk windows. ambil nama printer sharingnya
            $connector = new FilePrintConnector($PRINTER_DEVICE);
            $printer = new Printer($connector);
            $trx = $this->data;

            foreach ($trx as $t) {
                // heading
                $printer->initialize();
                $printer->selectPrintMode(Printer::MODE_DOUBLE_HEIGHT); // perbesar huruf
                $printer->setJustification(Printer::JUSTIFY_CENTER); // Setting teks menjadi rata tengah
                $printer->text("PT. TELE RING DISTRINDO\n");
                $printer->text("\n");

                // data transactions
                $printer->initialize();
                $printer->text("Jl. Wahid Hasyim pertokoan Sindanglaut\n");
                $printer->text("Blok A4 Dusun 01 RT005 RW001\n");
                $printer->text("Cipeujeuh Wetan Lemah Abang\n");
                $printer->text("Kab Cirebon Jawa Barat\n");
                $printer->text("NPWP 74.454.732.4-426.000\n");
                $printer->text("\n");

                // isi
                $printer->initialize();
                $printer->text("NOTA PENJUALAN : ". $t->no_nota ."\n");
                $printer->text("MEMBER ID : ". $t->member_id ."\n");
                $printer->text("\n");
                
                // create table product
                $printer->initialize();
                $printer->text("----------------------------------------\n");
                $printer->text($this->create4Column("NAMA BARANG", "QYT", "NILAI"));
                $printer->text("----------------------------------------\n");
                foreach ($t->items as $item) {
                    $printer->text($this->create4Column($item->nama_barang, $item->qyt, $item->nilai));
                }
                $printer->text("----------------------------------------\n");
                $printer->text($this->create4Column('', "Total", $t->total));
                $printer->feed(5); // mencetak 5 baris kosong agar terangkat (pemotong kertas saya memiliki jarak 5 baris dari toner)
            }
            $printer->close();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}