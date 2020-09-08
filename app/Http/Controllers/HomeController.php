<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $checkNotaCode = DB::table('items')->max('no_nota');
        $urutan = (int)$checkNotaCode;
        $urutan++;
        $data['no_nota'] = sprintf("%05s", $urutan);
        return view('home', $data);
    }
}
