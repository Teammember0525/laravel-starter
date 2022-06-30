<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class BackendController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $total = DB::table('home_infos')->count();
        $sell = DB::table('home_infos')->where('statusType', 'FOR_SALE')->count();
        $rent = DB::table('home_infos')->where('statusType','FOR_RENT')->count();
        $sold = DB::table('home_infos')->where('statusType','SOLD')->count();
        return view('backend.index',['total'=>$total, 'sell'=> $sell, 'rent'=>$rent, 'sold'=>$sold]);
    }
}
