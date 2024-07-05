<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Party;
use App\Models\Province;
use App\Models\Subdistrict;
use App\Models\Dapil;
use App\Models\City;
use App\Models\DapilDprdprovince;
use App\Models\DapilTransaction;
use App\Models\DapilDprdprovinceTransaction;
use DB;

class DapilController extends Controller
{

  function __construct()
  {
  }

  public function curl_dprri()
  {
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://www.electionhouse.org/api/dprri?tahun_pemilu=2019',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => array(
        'x-api-key: sss0ccg4kcck8w48owwsw40w8ss4w48w44k8cw8g'
      ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    return $decode = json_decode($response);
  }

  public function curl_dprdprovinsi()
  {
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://www.electionhouse.org/api/dprdprovinsi?tahun_pemilu=2019',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => array(
        'x-api-key: sss0ccg4kcck8w48owwsw40w8ss4w48w44k8cw8g'
      ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    return $decode = json_decode($response);
  }

  public function dprri()
  {
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://www.electionhouse.org/api/dprri?tahun_pemilu=2019',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => array(
        'x-api-key: sss0ccg4kcck8w48owwsw40w8ss4w48w44k8cw8g'
      ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    $decode = json_decode($response);
    return response()->json($decode);
  }

  public function dprdprovinsi()
  {
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://electionhouse.org/api/dprdprovinsi?tahun_pemilu=2019&limit=20',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => array(
        'x-api-key: sss0ccg4kcck8w48owwsw40w8ss4w48w44k8cw8g',
        'Content-Type:application/json'
      ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    return $decode = json_decode($response);
  }

  public function getDapilDprri()
  {
    $province_id=request('province_id');
    $dapil=Dapil::where('province_id',$province_id)->where('level','DPR RI')->get();

    return response()->json(['status'=>'success','data'=>$dapil]);
  }

  public function getCityByDapilDprri()
  {
    $dapil_id=request('dapil_id');
    $dapil=DapilTransaction::where('dapil_id',$dapil_id)->with('city')->get();
    return $dapil;
  }

  public function make_dprri()
  {
    $a=$this->curl_dprri();
    foreach ($a as $key => $value) {
      $province=Province::where('name','LIKE','%'.$value->NamaProvinsi.'%')->first();
      $data=[
        'id'=>$value->IDDapil,
        'name'=>$value->namaDapil,
        'level'=>$value->TingkatDapil,
        'province_id'=>$province->id,
        'jml_kursi'=>$value->JumlahKursi,
        'min_kursi'=>$value->MinimumKursi,
        'alokasi'=>$value->AlokasiKursi,
      ];

          // return $data;
      Dapil::updateOrCreate(['id'=>$value->IDDapil],$data);
    }

    return response()->json(['status'=>'success']);
  }

  // public function make_dprdprovinsi()
  // {
  // $file = fopen('dprdprovinsi.csv', 'r');
    // $data = array();

    // while (($row = fgetcsv($file)) !== FALSE) {
    //   $data[] = $row;
    // }

// fclose($file); 
    // $a=$this->curl_dprdprovinsi();
    // foreach ($a as $key => $value) {
    //   $province=Province::where('name','LIKE','%'.$value->NamaProvinsi.'%')->first();
    //   $data=[
    //     'id'=>$value->IDDapil,
    //     'name'=>$value->namaDapil,
    //     'level'=>$value->TingkatDapil,
    //     'province_id'=>$province->id,
    //     'jml_kursi'=>$value->JumlahKursi,
    //     'min_kursi'=>$value->MinimumKursi,
    //     'alokasi'=>$value->AlokasiKursi,
    //   ];

    //       // return $data;
    //   Dapil::updateOrCreate(['id'=>$value->IDDapil],$data);
    // }

    // return response()->json(['status'=>'success']);
  // }

  public function make_dprdprovinsi()
  {
    $dummy=DB::table('dumies')->orderBy('id','ASC')->get();

    foreach ($dummy as $key => $value) {
      $keyword=rtrim(preg_replace("/[0-9]/", "", $value->name));
      $province=Province::where('name','LIKE','%'.$keyword.'%')->first();
      if($province){        
        $data[$key]=[
          'id'=>$value->id,
          'name'=>$value->name,
          'level'=>'DPRD Provinsi',
          'province_id'=>$province->id
        ];
      } else {
        $data[$key]=[
          'id'=>$value->id,
          'name'=>$value->name,
          'level'=>'DPRD Provinsi',
          'province_id'=>NULL
        ];
      }

      DapilDprdprovince::updateOrCreate(['id'=>$value->id],$data[$key]);
    }

    return response()->json(['status'=>'success','message'=>'Berhasil membuat data DPRD Provinsi']);
  }

  public function make_city_dprdprovinsi()
  {
    $dumies=DB::table('dumies')->get();
    $data=[];
    $a=[];
    $city=[];
    $subdistrict=[];
    $no=1;
    foreach ($dumies as $key => $value) {
    //   if($value->city!==null){
    //     $explode_city=explode(',', $value->city);
    //     for ($i=0; $i < count($explode_city); $i++) {
    //      $city[$i]=City::where('name','LIKE','%'.$explode_city[$i])->first();
    //      if($city[$i]){
    //       $data_city=$city[$i]->name.','.$explode_city[$i];
    //       $city_id=$city[$i]->id;
    //     }else {
    //       $data_city=NULL.','.$explode_city[$i];
    //       $city_id=NULL;
    //     }
    //     $data=[
    //       'id'=>$no++,
    //       'dapil_id'=>$value->id,
    //       'city_id'=>$city_id,
    //       'city'=>$data_city,
    //       'subdistrict_id'=>NULL,
    //     ];

    //     DapilDprdprovinceTransaction::create($data);
    //   }
    // }
      $count=DapilDprdprovinceTransaction::count();
      $no=$count+1;
      if($value->subdistrict!==null){        
        $explode_subdistrict=explode(',',$value->subdistrict);
        for ($j=0; $j <count($explode_subdistrict); $j++) {
          $subdistrict[$j]=Subdistrict::where('name','LIKE',$explode_subdistrict[$j].'%')->first();
          if($subdistrict[$j]){
            $data_subdistrict=$subdistrict[$j]->name.','.$explode_subdistrict[$j];
            $subdistrict_id=$subdistrict[$j]->id;
          } else {
            $data_subdistrict='NULL'.','.$explode_subdistrict[$j];
            $subdistrict_id='NULL';
          }
          $data=[
            'id'=>$no++,
            'dapil_id'=>$value->id,
            'city_id'=>NULL,
            'subdistrict_id'=>$subdistrict_id,
            'subdistrict'=>$data_subdistrict,
          ];
          DapilDprdprovinceTransaction::create($data);
        }
      }
    }

    // return $city;
  }

  public function make_city_dapil()
  {
    $a=$this->curl_dprri();
    $data=[];
    foreach ($a as $key => $value) {
      $data[$key]=[
        'dapil_id'=>$value->IDDapil,
        'wilayah'=>$value->wilayah
      ];
    }

    // return $data;

    $city=[];
    $idd=[];
    $insert=[];
    for ($k=0; $k < count($data); $k++) {
      $b[$k]=explode(', ', $data[$k]['wilayah']);
      for ($j=0; $j < count($b[$k]); $j++) { 
        $idd[$k][$j]=$data[$k]['id_dapil'];
        $city[$k][$j]=City::where('name','LIKE','%'.$b[$k][$j].'%')->first();
        if($city[$k][$j]!==null){          
          $insert[$k][$j]=[
            'dapil_id'=>$idd[$k][$j],
            'city_id'=>$city[$k][$j]['id']
          ];
          DapilTransaction::updateOrCreate(['city_id'=>$city[$k][$j]['id']],$insert[$k][$j]);
        }
      }
    }
    return response()->json(['status'=>'success']);
    return $insert;
    // return $idd;
    // return $city;

  }

  public function partai()
  {
    $partai=Party::get();
    return response()->json(['status'=>'success','data'=>$partai]);
  }
}
