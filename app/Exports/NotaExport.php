<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class NotaExport implements FromView
{
    protected $data;

    public function __construct($data = null) {
        $this->data = $data;
    }

    public function view() : View
    {
        return view('exports.nota', ['nota' => $this->data]);
    }
}
