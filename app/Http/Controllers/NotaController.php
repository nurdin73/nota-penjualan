<?php

namespace App\Http\Controllers;

use App\Http\Helpers\Functions;
use App\Import\ItemsImport;
use App\Jobs\ImportJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

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
                    $btn = "<div class='btn-group' role='group' aria-label='Basic example'><button type='button' data-id='$id' class='btn btn-primary btn-sm update' data-toggle='modal' data-target='#modalUpdate'>Update</button><button type='button' data-id='$id' data-nota='$row->no_nota' class='btn btn-danger btn-sm delete'>Delete</button><button type='button' data-id='$id' data-nota='$row->no_nota' class='btn btn-success btn-sm export'>Export</button></div>";
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
                    $result = DB::table('items')->where(['member_id' => $row->member_id, 'no_nota' => $row->no_nota])->sum('nilai');
                    return "Rp. ".number_format($result, 0, ',', '.');
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
        # code...
    }

    public function import(Request $request)
    {
        $this->validate($request, [
            'file_excel' => 'required|mimes:xls,xlsx'
        ]);
        $path = $request->file('file_excel');
        $result = Excel::import(new ItemsImport, $path);
        if(!$result) {
            $message = Functions::message('Import nota gagal', true);
            return response($message);
        } else {
            $message = Functions::message('Import nota berhasil', true);
            return response($message);
        }
    }

    public function exportAll(Request $request)
    {
        # code...
    }

    public function export($member_id)
    {
        # code...
    }
}
