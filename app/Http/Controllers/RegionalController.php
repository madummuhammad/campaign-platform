<?php

namespace App\Http\Controllers;
use App\Models\Province;
use App\Models\City;
use App\Models\Subdistrict;
use App\Models\Village;

use Illuminate\Http\Request;

class RegionalController extends Controller
{
    function __construct()
    {

    }

    public function get_province()
    {
        $province=Province::orderBy('id','ASC')->get();

        return response()->json(['status'=>'success','data'=>$province]);
    }

    public function get_city()
    {
        $province_id=request('province_id');
        $city=City::where('province_id',$province_id)->get();
        return response()->json(['status'=>'success','data'=>$city]);
    }

    public function get_subdistrict()
    {
        $city_id=request('city_id');
        $subdistrict=Subdistrict::where('city_id',$city_id)->get();
        return response()->json(['status'=>'success','data'=>$subdistrict]);
    }

    public function get_village()
    {
        $subdistrict_id=request('subdistrict_id');
        $village=Village::where('subdistrict_id',$subdistrict_id)->get();
        return response()->json(['status'=>'success','data'=>$village]);
    }

    public function make_province()
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://api.goapi.id/v1/regional/provinsi?api_key=2uuvUylCWTxpnGmo1OdN07shnlYnKo',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
      ));

        $response = curl_exec($curl);

        curl_close($curl);
        $decode = json_decode($response);
        foreach ($decode->data as $key => $value) {
            $data=[
                'id'=>$value->id,
                'name'=>$value->name
            ];

            Province::updateOrCreate(['id'=>$value->id],$data);
        }

        return response()->json(['status'=>'success','message'=>'Sukses membuat provinsi']);
    }

    public function make_city()
    {
        $province=Province::get();

        foreach ($province as $key => $value) {
            $this->curl_city($value->id);
        }
        return response()->json(['status'=>'success','message'=>'Berhasil membuat city']);
    }

    public function make_subdistrict()
    {
        // $city=City::paginate(50);

        // foreach ($city as $key => $value) {
        $this->curl_subdistrict('12.10');
        // }
        return response()->json(['status'=>'success','message'=>'Berhasil membuat Subdistrict']);
    }

    private function curl_city($province_id)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://api.goapi.id/v1/regional/kota?provinsi_id='.$province_id.'&api_key=2uuvUylCWTxpnGmo1OdN07shnlYnKo',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
      ));

        $response = curl_exec($curl);

        curl_close($curl);
        $decode = json_decode($response);
        foreach ($decode->data as $key => $value) {
            $data=[
                'id'=>$value->id,
                'name'=>$value->name,
                'province_id'=>$province_id
            ];

            City::updateOrCreate(['id'=>$value->id],$data);
        }
    }

    private function curl_subdistrict($city_id)
    {
        // return $city_id;
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://api.goapi.id/v1/regional/kecamatan?kota_id='.$city_id.'&api_key=2uuvUylCWTxpnGmo1OdN07shnlYnKo',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
      ));

        $response = curl_exec($curl);

        curl_close($curl);
        $decode = json_decode($response);
    // return response()->json($decode);
        foreach ($decode->data as $key => $value) {      
            $data=[
                'id'=>$value->id,
                'name'=>$value->name,
                'city_id'=>$city_id
            ];
            Subdistrict::updateOrCreate(['id'=>$value->id],$data);
        }
    }

    // public function make_Village(){
    //     $village=Village::where('subdistrict_id','>=','91.15.07')->where('subdistrict_id','<=','91.25.21')->get();
    //     return $village;
    //     $city_id=request('city_id');
    //     $jml=request('jml');
    //     $data=[];
    //     for ($i=1; $i <= $jml; $i++) {
    //         $id=$city_id.'.'.str_pad($i, 2, "0", STR_PAD_LEFT);
    //         $this->curl_village($id);
    //     }
    //     return response()->json(['status'=>'success','message'=>'Berhasil membuat kelurahan','count'=>Village::count()]);
    // }

    public function make_Village(){
     $subdistrict=Subdistrict::where('id','>=','92.71.10')->where('id','<=','92.71.10')->get();
     foreach ($subdistrict as $key => $value) {
        $this->curl_village($value->id);
    }
    return response()->json(['status'=>'success','message'=>'Berhasil membuat kelurahan','count'=>Village::count()]);
}

private function curl_village($subdistrict_id){
        // return $city_id;
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.goapi.id/v1/regional/kelurahan?kecamatan_id='.$subdistrict_id.'&api_key=2uuvUylCWTxpnGmo1OdN07shnlYnKo',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    $decode = json_decode($response);
        // return response()->json($decode);
    foreach ($decode->data as $key => $value) {      
        $data=[
            'id'=>$value->id,
            'name'=>$value->name,
            'subdistrict_id'=>$subdistrict_id
        ];
        Village::updateOrCreate(['id'=>$value->id],$data);
    }
}
}
