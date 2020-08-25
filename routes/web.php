<?php

use App\Import\ItemsImport;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes([
    'register' => true,
    'verify' => false,
    'reset' => false
]);

Route::get('/home', 'HomeController@index')->name('home');
// Route::get('/offline', function (){
//     return view('offline');
// });

Route::get('detail/{member_id}', function ($member_id){
    $result = DB::table('items')->where('member_id', $member_id)->get();
    $data = [];
    $data['member_id'] = "";
    $data['no_nota'] = "";
    $data['items'] = [];
    foreach ($result as $row) {
        $sub_array = [];
        $data['member_id'] = $row->member_id;
        $data['no_nota'] = $row->no_nota;
        $sub_array['nama_barang'] = $row->nama_barang;
        $sub_array['qyt'] = $row->qyt;
        $sub_array['nilai'] = $row->nilai;
        array_push($data['items'], $sub_array);
    }
    return response($data);
});


Route::group(['prefix' => 'nota'], function () {
    Route::get('/get-all', 'NotaController@getAll');
    Route::get('/get-nota-multiple', 'NotaController@getDataMultiple');
    Route::get('/get/{member_id}', 'NotaController@get');
    Route::delete('/delete-all', 'NotaController@destroyAll');
    Route::delete('/delete/{member_id}', 'NotaController@destroy');
    Route::post('/add', 'NotaController@add');
    Route::put('/update', 'NotaController@update');
    Route::post('/import', 'NotaController@import');
    Route::get('/export-all', 'NotaController@exportAll');
    Route::get('/export/{member_id}', 'NotaController@export');
    Route::get('/export-word/{member_id}', 'NotaController@exportWord');
    Route::get('/export-word-multiple', 'NotaController@exportWordMultiple');
});