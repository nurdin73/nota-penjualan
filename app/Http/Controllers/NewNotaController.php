<?php

namespace App\Http\Controllers;

use App\Jobs\ImportNotaNewJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;

class NewNotaController extends Controller
{
    public function print(Request $request)
    {
        $this->validate($request, [
            'date' => 'required',
            'excel' => 'required'
        ]);
        if($request->hasFile('excel')) {
            $tanggal = $request->input('date');
            $file = file($request->file('excel'));
            $chunks = array_chunk($file, 100);
            foreach ($chunks as $chunk => $value) {
                $data = array_map("str_getcsv", $value);
                if($chunk == 0) {
                    unset($data[0]);
                }
                Queue::push(new ImportNotaNewJob($data, $tanggal));
            }
            return redirect()->back()->with('message', 'Import nota berhasil.');
        }
    }
}
