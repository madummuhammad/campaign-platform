<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DapildprdProvince;

class DapildprdprovinceController extends Controller
{
    public function __construct()
    {

    }

    public function get()
    {
        $data=DapildprdProvince::get();
        return response()->json(['status'=>'success','data'=>$data]);
    }

    public function get_by_province()
    {
        $province_id=request('province_id');
        $data=DapildprdProvince::where('province_id',$province_id)->get();
        return response()->json(['status'=>'success','data'=>$data]);
    }
}
