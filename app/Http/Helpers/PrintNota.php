<?php
namespace App\Http\Helpers;

use Exception;
use Mike42\Escpos\CapabilityProfile;
use Mike42\Escpos\PrintConnectors\DummyPrintConnector;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;

class PrintNota
{
    protected $data;
    protected $OS_APP;
    protected $CONNECTION;
    protected $NAME_PRINTER;
    public function __construct(Array $data) {
        $this->data = $data;
        $this->OS_APP = config('app.os');
        $this->CONNECTION = config('app.connection');
        $this->NAME_PRINTER = config('app.printer_name');
    }

    protected function printer()
    {
        $os = $this->OS_APP ?? env('OS', 'linux');
        $PRINTER_DEVICE = $this->NAME_PRINTER;
        $connector = null;
        if($this->CONNECTION == "USB") {
            if($os == "windows") {
                $connector = new WindowsPrintConnector($PRINTER_DEVICE); // ini untuk windows. ambil nama printer sharingnya
            } elseif($os == "linux") {
                $connector = new FilePrintConnector($PRINTER_DEVICE);
            } 
        } elseif($this->CONNECTION == "ethernet") {
            $connector = new NetworkPrintConnector(env('IP_PRINTER_SHARING', "10.x.x.x"), env('PORT_PRINTER_SHARING', "9100"));
        } elseif($this->CONNECTION = "bluetooth") {
            $connector = new DummyPrintConnector();
            $this->profile = CapabilityProfile::load($this->NAME_PRINTER);
            $connector->finalize();
            $this->connector = $connector;
        }
        $printer = new Printer($connector);
        return $printer;
    }

    public function printing()
    {
        try {
            $printer = $this->printer();
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
            $printer->text("NOTA PENJUALAN : ". $this->data['no_nota'] ."\n");
            $printer->text("MEMBER ID : ". $this->data['member_id'] ."\n");
            $printer->text("\n");

            $printer->initialize();
            $printer->text("DEPOSIT(Rp) \n");
            $printer->text("Rp. " . number_format($this->data['newNominal']) . "\n");
            $printer->text("----------------------------------------\n");
            $printer->feed(5);
            $printer->close();
            return "success";
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}