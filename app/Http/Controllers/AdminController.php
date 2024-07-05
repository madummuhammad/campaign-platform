<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\Position;
use App\Models\AccessMenu;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    function __construct()
    {

    }

    public function list()
    {
        $keyword=request('keyword');
        $position=request('position');
        $created_at=request('created_at');
        $caleg_id=auth()->user()->caleg_id;

        $query=User::where('caleg_id',$caleg_id)->with('position');
        if($keyword==null OR $keyword=='null'){
        } else {
            $query->where('name','LIKE','%'.$keyword.'%');
        }

        if($position==null OR $position=='null'){
        } else {
            $query->where('position_id',$position);
        }

        // if($created_at==null OR $created_at=='null'){
        // } else {
        //     $query->where('created_at',$created_at);
        // }
        $user=$query->orderBy('created_at','ASC')->get();
        return response()->json(['status'=>'success','data'=>$user,'position'=>request('position'),'created_at'=>request('created_at')]);
    }

    public function get_position(){
        $user=auth()->user();
        $position=Position::where('caleg_id',$user->caleg_id)->get();
        return response()->json(['status'=>'success','data'=>$position]);
    }

    public function add()
    {
        $caleg_id=auth()->user()->caleg_id;
        $data=[
            'name'=>request('name'),
            'no_hp'=>request('no_hp'),
            'email'=>request('email'),
            'province'=>request('province'),
            'city'=>request('city'),
            'rt'=>request('rt'),
            'rw'=>request('rw'),
            'role'=>request('role'),
            'password'=>request('password'),
            'confirm_password'=>request('confirm_password'),
            'position_id'=>request('position_id'),
            'position_description'=>request('position_description')
        ];

        $validation=Validator::make($data,[
            'name'=>'required',
            'no_hp'=>'required|numeric',
            'email'=>'required|email',
            'province'=>'required',
            'city'=>'required',
            'rt'=>'required',
            'rw'=>'required',
            'role'=>'required',
            'password'=>'required',
            'confirm_password'=>'required|same:password'
        ]);

        if($validation->fails()){
            return response()->json(['status'=>'error','message'=>'Isi data dengan benar','error'=>$validation->errors()]);
        }

        $user=User::create([
            'name'=>request('name'),
            'caleg_id'=>$caleg_id,
            'no_hp'=>request('no_hp'),
            'email'=>request('email'),
            'province'=>request('province'),
            'city'=>request('city'),
            'rt'=>request('rt'),
            'rw'=>request('rw'),
            'role'=>request('role'),
            'password'=>Hash::make(request('password')),
            'position_id'=>request('position_id'),
            'area_province'=>request('area_province'),
            'area_city'=>json_encode(request('area_city')),
            'area_subdistrict'=>json_encode(request('area_subdistrict')),
            'area_village'=>request('area_village'),
        ]);

        foreach (request('access_menu') as $key => $value) {
            AccessMenu::create([
                'user_id'=>$user['id'],
                'menu'=>$value['menu'],
                'create'=>$value['create'],
                'read'=>$value['read'],
                'update'=>$value['update'],
                'delete'=>$value['delete']
            ]);
        }

        return response()->json(['status'=>'success','message'=>'Berhasil menambahkan admin']);
    }

    public function delete()
    {
        // $id='asdfasdf';
        $id=request('id');

        $user=User::where('id',$id)->delete();

        if($user>0){
            return response()->json(['status'=>'success','message'=>'Berhasil menghapus admin']);
        }else{
            return response()->json(['status'=>'error','message'=>'Tidak ada data yang terhapus']);
        }

    }

    public function detail()
    {
        $id=request('id');
        $user=User::where('id',$id)->with('access_menu')->first();

        return response()->json(['status'=>'success','data'=>$user]);
    }
}
