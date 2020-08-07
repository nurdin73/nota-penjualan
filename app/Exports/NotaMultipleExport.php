<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class NotaMultipleExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $data;

    public function __construct($data = null) {
        $this->data = $data;
    }

    public function view() : View
    {
        return view('exports.notaMultiple', ['data_nota' => $this->data]);
    }
}
