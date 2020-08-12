<?php

namespace App\Http\Controllers;

use App\Exports\NotaExport;
use App\Exports\NotaMultipleExport;
use App\Http\Helpers\Functions;
use App\Import\ItemsImport;
use App\Jobs\ImportNotaJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\TemplateProcessor;

class NotaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function getAll()
    {
        $results = DB::table('items')->get();
        if($results) {
            $data = [];
            foreach ($results as $row) {
                $sub_array = [];
                $sub_array['member_id'] = $row->member_id;
                $sub_array['no_nota'] = $row->no_nota;
                $sub_array['items'] = [];
                $items = DB::table('items')->where(['member_id' => $row->member_id, 'no_nota' => $row->no_nota])->get();
                foreach ($items as $i) {
                    $dataItems = [];
                    $dataItems['id'] = $i->id;
                    $dataItems['nama_barang'] = $i->nama_barang;
                    $dataItems['qyt'] = $i->qyt;
                    $dataItems['nilai'] = $i->nilai;
                    array_push($sub_array['items'], $dataItems);
                }
                array_push($data, $sub_array);
                // array_push($data['items'], $items);
            }
            $data = Functions::ArrayDuplicateRemove($data, false);
            // return response($data);
            return datatables()->of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row){
                    $row = json_encode($row);
                    $row = json_decode($row);
                    $id = $row->member_id;
                    $btn = "<div class='btn-group' role='group' aria-label='Basic example'><button type='button' data-id='$id' class='btn btn-info btn-sm update' data-toggle='modal' data-target='#modalUpdate'>Update</button><button type='button' data-id='$id' data-nota='$row->no_nota' class='btn btn-danger btn-sm delete'>Delete</button><button type='button' data-id='$id' data-nota='$row->no_nota' class='btn btn-success btn-sm export'>Excel</button><button type='button' data-id='$id' data-nota='$row->no_nota' class='btn btn-primary btn-sm export-word'>Word</button></div>";
                    return $btn;
                })
                ->addColumn('checkbox', function ($row){
                    $row = json_encode($row);
                    $row = json_decode($row);
                    $id = $row->member_id;
                    $checkbox = "<input type='checkbox' class='check' name='check' id='check' data-id='$id'>";
                    return $checkbox;
                })
                ->addColumn('total_items', function ($row){
                    $row = json_encode($row);
                    $row = json_decode($row);
                    $items = count($row->items);
                    return $items;
                })
                ->addColumn('total', function ($row){
                    $row = json_encode($row);
                    $row = json_decode($row);
                    $totalHarga = 0;
                    $result = DB::table('items')->where(['member_id' => $row->member_id, 'no_nota' => $row->no_nota])->get();
                    foreach ($result as $value) {
                        $totalHarga += $value->nilai;
                    }
                    return "Rp. ".number_format($totalHarga, 0, ',', '.');
                })
                ->rawColumns(['checkbox', 'action', 'total_items', 'total'])
                ->make(true);
        } else {
            return datatables()->of($results)->make(true);
        }
    }
    
    public function get($member_id)
    {
        $result = DB::table('items')->where('member_id', $member_id)->get();
        $data = [];
        $data['member_id'] = "";
        $data['no_nota'] = "";
        $data['items'] = [];
        foreach ($result as $row) {
            $sub_array = [];
            $data['member_id'] = $row->member_id;
            $data['no_nota'] = $row->no_nota;
            $sub_array['id'] = $row->id;
            $sub_array['nama_barang'] = $row->nama_barang;
            $sub_array['qyt'] = $row->qyt;
            $sub_array['nilai'] = $row->nilai;
            array_push($data['items'], $sub_array);
        }
        return response($data);
    }

    public function add(Request $request)
    {
        $member_id = $request->input('member_id');
        $no_nota = $request->input('no_nota');
        $nama_barang = $request->input('nama_barang');
        $qyt = $request->input('qyt');
        $nilai = $request->input('nilai');
        $result = false;
        for ($i=0; $i < count($nama_barang); $i++) { 
            $result = DB::table('items')->insert([
                'member_id' => htmlspecialchars($member_id),
                'no_nota' => htmlspecialchars($no_nota),
                'nama_barang' => htmlspecialchars($nama_barang[$i]),
                'qyt' => htmlspecialchars($qyt[$i]),
                'nilai' => htmlspecialchars($nilai[$i]),
            ]);
        }
        if($result) {
            $message = Functions::message('Nota berhasil ditambahkan', true);
            return response($message);
        } else {
            $message = Functions::message('Nota gagal ditambahkan', false);
            return response($message);
        }
    }

    public function destroyAll(Request $request)
    {
        $data = $request->listId;
        $deleteAll = DB::table('items')->whereIn('member_id', explode(',', $data))->delete();
        if($deleteAll) {
            $message = Functions::message('Nota berhasil dihapus', true);
            return response($message);
        } else {
            $message = Functions::message('Nota gagal dihapus', false);
            return response($message);
        }
    }

    public function destroy($member_id)
    {
        $delete = DB::table('items')->where('member_id', $member_id)->delete();
        if($delete) {
            $message = Functions::message('Nota berhasil dihapus', true);
            return response($message);
        } else {
            $message = Functions::message('Nota gagal dihapus', false);
            return response($message);
        }
    }

    public function update(Request $request)
    {
        $memberId = $request->input('member_id');
        $noNota = $request->input('no_nota');
        $idItem = $request->input('id');
        $nama_barang = $request->input('nama_barang');
        $nilai = $request->input('nilai');
        $qyt = $request->input('qyt');
        $result = false;
        $message = "";
        for ($i=0; $i < count($idItem); $i++) { 
            // $check = DB::table('items')->where('id', $idItem[$i])->first();
            if($idItem[$i] != null) { // kalo ada
                $update = DB::table('items')->where('id', $idItem[$i])->update([
                    'member_id' => $memberId,
                    'no_nota' => $noNota,
                    'nama_barang' => $nama_barang[$i],
                    'qyt' => $qyt[$i],
                    'nilai' => $nilai[$i],
                ]);
                $result = true;
                $message = "Update berhasil";
            } else { // kalo ga ada
                if($nama_barang[$i] != null && $qyt[$i] != null && $nilai[$i] != null) {
                    $create = DB::table('items')->insert([
                        'member_id' => $memberId,
                        'no_nota' => $noNota,
                        'nama_barang' => $nama_barang[$i],
                        'qyt' => $qyt[$i],
                        'nilai' => $nilai[$i],
                    ]);
                    $result = $create;
                    $message = "Update dan tambah item berhasil";
                }
            }
        }
        $message = Functions::message($message, $result);
        return response($message);
    }

    public function import(Request $request)
    {
        $this->validate($request, [
            'file_excel' => 'required|mimes:xls,xlsx'
        ]);
        if($request->hasFile('file_excel')) {
            $path = $request->file('file_excel');
            $filename = time() . "." . $path->getClientOriginalExtension();
            $path->storeAs(
                'public', $filename
            );
            $result = ImportNotaJob::dispatch($filename);
            if(!$result) {
                $message = Functions::message('Import nota gagal', true);
                return response($message);
            } else {
                $message = Functions::message('Import nota berhasil', true);
                return response($message);
            }
        }
        
    }

    public function exportAll(Request $request)
    {
        $member_id = $request->memberId;
        $result = DB::table('items')->whereIn('member_id', explode(',', $member_id))->get();
        $data = [];
        foreach ($result as $row) {
            $sub_array = [];
            $sub_array['member_id'] = $row->member_id;
            $sub_array['no_nota'] = $row->no_nota;
            $getTotal = DB::table('items')->where(['member_id' => $row->member_id, 'no_nota' => $row->no_nota])->sum('nilai');
            $sub_array['total'] = "Rp. ".number_format($getTotal, 0, ',', '.');
            $sub_array['items'] = [];
            $items = DB::table('items')->where(['member_id' => $row->member_id, 'no_nota' => $row->no_nota])->get();
            foreach ($items as $i) {
                $dataItems = [];
                $dataItems['id'] = $i->id;
                $dataItems['nama_barang'] = $i->nama_barang;
                $dataItems['qyt'] = $i->qyt;
                $dataItems['nilai'] = $i->nilai;
                array_push($sub_array['items'], $dataItems);
            }
            array_push($data, $sub_array);
            // array_push($data['items'], $items);
        }
        $data = Functions::ArrayDuplicateRemove($data, false);
        $data = json_encode($data);
        $data = json_decode($data);
        $filename = "export-nota-".count($data).".xlsx";
        return Excel::download(new NotaMultipleExport($data), $filename);
    }

    public function export($member_id)
    {
        $result = DB::table('items')->where('member_id', $member_id)->get();
        $data = [];
        $data['member_id'] = "";
        $data['no_nota'] = "";
        $data['total'] = "";
        $data['items'] = [];
        foreach ($result as $row) {
            $sub_array = [];
            $data['member_id'] = $row->member_id;
            $data['no_nota'] = $row->no_nota;
            $sub_array['id'] = $row->id;
            $sub_array['nama_barang'] = $row->nama_barang;
            $sub_array['qyt'] = $row->qyt;
            $sub_array['nilai'] = $row->nilai;
            $getTotal = DB::table('items')->where(['member_id' => $row->member_id, 'no_nota' => $row->no_nota])->sum('nilai');
            $data['total'] = "Rp. ".number_format($getTotal, 0, ',', '.');
            array_push($data['items'], $sub_array);
        }
        $data = json_encode($data);
        $data = json_decode($data);
        $filename = "nota-".$data->no_nota."-".time().".xlsx";
        return Excel::download(new NotaExport($data), $filename);
    }

    public function exportWord($member_id)
    {
        $result = DB::table('items')->where('member_id', $member_id)->get();
        $data = [];
        $data['member_id'] = "";
        $data['no_nota'] = "";
        $data['total'] = "";
        $data['items'] = [];
        foreach ($result as $row) {
            $sub_array = [];
            $data['member_id'] = $row->member_id;
            $data['no_nota'] = $row->no_nota;
            $sub_array['id'] = $row->id;
            $sub_array['nama_barang'] = $row->nama_barang;
            $sub_array['qyt'] = $row->qyt;
            $sub_array['nilai'] = $row->nilai;
            $getTotal = DB::table('items')->where(['member_id' => $row->member_id, 'no_nota' => $row->no_nota])->sum('nilai');
            $data['total'] = number_format($getTotal, 0, ',', '.');
            array_push($data['items'], $sub_array);
        }
        $data = json_encode($data);
        $data = json_decode($data);
        $template = new TemplateProcessor(storage_path('template.docx'));
        $template->setValue('nota', $data->no_nota);
        $template->setValue('member_id', $data->member_id);
        $template->setValue('total', $data->total);
        $template->cloneBlock('items', 0, true, false, $data->items);
        $filename = $data->no_nota . ".". time() .".docx";

        header("Content-Type: aplication/octet-stream");
        header("Content-Disposition: attachment; filename=$filename");
        $template->saveAs("php://output");
    }

    public function exportWordMultiple(Request $request)
    {
        $member_id = $request->memberId;
        $result = DB::table('items')->whereIn('member_id', explode(',', $member_id))->get();
        $data = [];
        foreach ($result as $row) {
            $sub_array = [];
            $sub_array['member_id'] = $row->member_id;
            $sub_array['nota'] = $row->no_nota;
            $getTotal = DB::table('items')->where(['member_id' => $row->member_id, 'no_nota' => $row->no_nota])->sum('nilai');
            $sub_array['total'] = number_format($getTotal, 0, ',', '.');
            $sub_array['items'] = [];
            $items = DB::table('items')->where(['member_id' => $row->member_id, 'no_nota' => $row->no_nota])->get();
            foreach ($items as $i) {
                $dataItems = [];
                $dataItems['id'] = $i->id;
                $dataItems['nama_barang'] = $i->nama_barang;
                $dataItems['qyt'] = $i->qyt;
                $dataItems['nilai'] = $i->nilai;
                array_push($sub_array['items'], $dataItems);
            }
            array_push($data, $sub_array);
        }
        $data = Functions::ArrayDuplicateRemove($data, false);
        $data = json_encode($data);
        $data = json_decode($data);
        $template = new TemplateProcessor(storage_path('template_multiple.docx'));
        $filename = "";
        $template->cloneBlock('clone', count($data), true, true);
        $i = 1;
        foreach ($data as $value) {
            $template->setValue('total#'.$i, $value->total);
            $template->setValue('nota#'.$i, $value->nota);
            $template->setValue('member_id#'.$i, $value->member_id);
            $template->cloneBlock('items#'.$i, count($value->items), true, true);
            $z = 1;
            foreach ($value->items as $items) {
                $template->setValue('nama_barang#'.$i.'#'.$z, $items->nama_barang);
                $template->setValue('qyt#'.$i.'#'.$z, $items->qyt);
                $template->setValue('nilai#'.$i.'#'.$z, $items->nilai);
                $z++;
            }
            $template->setValue('pageBreak#'.$i, '</w:t></w:r>'.'<w:r><w:br w:type="page"/></w:r>'
            . '<w:r><w:t>');
            $i++;
        }
        $filename = count($data) . ".". time() .".docx";
        
        header("Content-Type: aplication/octet-stream");
        header("Content-Disposition: attachment; filename=$filename");
        $template->saveAs("php://output");
    }

    public function getDataMultiple(Request $request)
    {
        $member_id = $request->memberId;
        $result = DB::table('items')->whereIn('member_id', explode(',', $member_id))->get();
        $data = [];
        foreach ($result as $row) {
            $sub_array = [];
            $sub_array['member_id'] = $row->member_id;
            $sub_array['nota'] = $row->no_nota;
            $getTotal = DB::table('items')->where(['member_id' => $row->member_id, 'no_nota' => $row->no_nota])->sum('nilai');
            $sub_array['items'] = [];
            $items = DB::table('items')->where(['member_id' => $row->member_id, 'no_nota' => $row->no_nota])->get();
            foreach ($items as $i) {
                $dataItems = [];
                $dataItems['id'] = $i->id;
                $dataItems['nama_barang'] = $i->nama_barang;
                $dataItems['qyt'] = $i->qyt;
                $dataItems['nilai'] = $i->nilai;
                array_push($sub_array['items'], $dataItems);
            }
            array_push($data, $sub_array);
        }
        $data = Functions::ArrayDuplicateRemove($data, false);
        return response($data);
    }
}
