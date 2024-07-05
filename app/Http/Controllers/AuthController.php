<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Caleg;
use App\Models\Position;
use App\Models\AccessMenu;
use App\Models\EmailVerification;
use App\Mail\RegisterEmail;
use App\Mail\ForgotPassword;
use Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(){
      $this->middleware('auth:api',['except' => ['register','register_verification','access_menu','forgot_password','login','check_token_forgot','change_forgot_password']]);
  }

  public function register()
  {
    $dataValidation=[
        'name'=>request('name'),
        'email'=>request('email'),
        'user_status'=>request('user_status'),
        'election_province'=>request('election_province'),
        'election_region'=>request('election_region'),
        'political_party'=>request('political_party'),
        'password'=>request('password'),
        'wa'=>request('wa'),
        'no_urut'=>request('no_urut'),
        'confirm_password'=>request('confirm_password'),
        'privacy_policy_agrement'=>request('privacy_policy_agrement')

    ];
    $validation=Validator::make($dataValidation,[
        'email'=>'required|email',
        'name'=>'required',
        'user_status'=>'required',
        'election_province'=>'required',
        'election_region'=>'required',
        'political_party'=>'required',
        'wa'=>'required|numeric',
        'password'=>'required',
        'no_urut'=>'required|numeric',
        'confirm_password'=>'required|same:password',
        'privacy_policy_agrement'=>'required'
    ]);

        // Validasi data
    if($validation->fails()){
        return response()->json(['status'=>'error','message'=>$validation->errors()]);
    }

    if(request('privacy_policy_agrement')!==true){
        return response()->json(['status'=>'error','message'=>['privacy_policy_agrement'=>['Anda harus menyetujui ketentuan dan kebijakan privasi']]]);
    }

    $activeemail=Caleg::where('email',request('email'));


        // Verifkiasi apakah email sudah aktif terdaftar?

    if($activeemail->where('status','active')->exists()){
        return response()->json(['status'=>'error','message'=>['email'=>['Email telah terdaftar']]]);
    }

    $data=[
        'name'=>request('name'),
        'email'=>request('email'),
        'user_status'=>request('user_status'),
        'election_province'=>request('election_province'),
        'election_region'=>request('election_region'),
        'political_party'=>request('political_party'),
        'password'=>Hash::make(request('password')),
        'whatsapp'=>request('wa'),
        'no_urut'=>request('no_urut'),
        'status'=>'pending'
    ];

        // Verifkiasi apakah email pernah mendaftar dan belum verifikasi?
    $pendingemail=Caleg::where('email',request('email'));
    date_default_timezone_set("Asia/Jakarta");
    $email=request('email');
    $token=Str::uuid(64);
    $date = date_create(date('Y-m-d H:i:s'));
    date_add($date, date_interval_create_from_date_string('1 hour'));
    $expired_at=date_format($date, 'Y-m-d H:i:s');
    $emailverification=[
        'email'=>$email,
        'token'=>$token,
        'type'=>'register',
        'expired_at'=>$expired_at
    ];
    if($pendingemail->where('status','pending')->exists()){
        Caleg::where('email',request('email'))->update($data);
            // Admin::where('email',request('email'))->udate($data);

            // Cek apakah email expired?
        $cekexpired=EmailVerification::where('email',request('email'))->where('type','register')->where('expired_at','<',date('Y-m-d H:i:s'));

        if($cekexpired->exists()){
                // Delete email expired
            EmailVerification::where('email',request('email'))->where('type','register')->where('expired_at','<',date('Y-m-d H:i:s'))->delete();

                // Create row baru
            EmailVerification::create($emailverification);

                // Kirim link
            Mail::to(request('email'))->send(new RegisterEmail());
        } else {
                // Cek apakah number of request sudah melebihi?
            $ceknumberofrequest=EmailVerification::where('email',request('email'))->where('type','register')->where('expired_at','>',date('Y-m-d H:i:s'))->first();
            if($ceknumberofrequest->number_of_request>=3){
                return response()->json(['status'=>'error','message'=>'Percobaan register sudah melebihi 3 kali, silahkan coba 30 menit']);
            } else {
                EmailVerification::where('email',request('email'))->where('type','register')->where('expired_at','>',date('Y-m-d H:i:s'))->update(['number_of_request'=>$ceknumberofrequest->number_of_request+1,'token'=>$token]);

                    // Kirim link
                Mail::to(request('email'))->send(new RegisterEmail());
            }

        }
    } else {
            // Create row baru users
        $caleg=Caleg::create($data);

        $position=[
            [
                "caleg_id"=>$caleg->id,
                'name'=>'Sekretaris'
            ],
            [
                "caleg_id"=>$caleg->id,
                'name'=>'Bendahara'
            ],
            [
                "caleg_id"=>$caleg->id,
                'name'=>'Koordinator Kota/Kabupaten'
            ],
            [
                "caleg_id"=>$caleg->id,
                'name'=>'Koordinator Kelurahan'
            ],
            [
                "caleg_id"=>$caleg->id,
                'name'=>'Koordinator TPS'
            ],
        ];

        foreach ($position as $key => $value) {
            Position::create($value);
        }

            // $admin=[
            //     'name'=>request('name'),
            //     'wa'=>request('no_hp'),
            //     // ''
            // ];
            // Admin::create($admin);
            // Create row baru email verification
        EmailVerification::create($emailverification);

            // Kirim Link
        Mail::to(request('email'))->send(new RegisterEmail());
    }


    return response()->json(['status'=>'success','message'=>'Registrasi berhasil. Silakan konfirmasi email anda']);
}

public function register_verification()
{
    $token=request('token');
    $emailverification=EmailVerification::where('token',$token)->where('type','register')->first();

        // Cek Apakah token tidak terdelete?
    if($emailverification){            
        // Cek expired token
        if($emailverification->expired_at<date('Y-m-d H:i:s')){
            return response()->json(['status'=>'error','message'=>'Token expired']);
        } else {
            Caleg::where('email',$emailverification->email)->update(['status'=>'active']);
            $caleg=Caleg::where('email',$emailverification->email)->first();

            $data=[
                'caleg_id'=>$caleg->id,
                'name'=>$caleg->name,
                'no_hp'=>$caleg->whatsapp,
                'email'=>$caleg->email,
                'role'=>'Owner',
                'password'=>$caleg->password
            ];

            $user=User::create($data);
            $this->access_menu($user);
            EmailVerification::where('token',$token)->where('type','register')->delete();
            return response()->json(['status'=>'success','message'=>'Verifikasi berhasil']);
        }
    } else {
        return response()->json(['status'=>'error','message'=>'Token expired']);
    }
}

public function caleg_profile()
{
    $user=User::where('id',auth()->user()->id)->first();
    $data=Caleg::where('id',$user->caleg_id)->with('party')->first();
    return response()->json(['status'=>'success','data'=>$data]);
}

private function access_menu($user){
 $access_menu=[
    [
        "menu"=> "admin",
        "user_id"=>$user->id,
        "create"=> 1,
        "read"=> 1,
        "delete"=> 1,
        "update"=> 1
    ],
    [
        "menu"=> "tim",
        "user_id"=>$user->id,
        "create"=> 1,
        "read"=> 1,
        "delete"=> 1,
        "update"=> 1
    ],
    [
        "menu"=> "scoreboard",
        "user_id"=>$user->id,
        "create"=> 1,
        "read"=> 1,
        "delete"=> 1,
        "update"=> 1
    ],
    [
        "menu"=> "activity",
        "user_id"=>$user->id,
        "create"=> 1,
        "read"=> 1,
        "delete"=> 1,
        "update"=> 1
    ],
    [
        "menu"=> "financial",
        "user_id"=>$user->id,
        "create"=> 1,
        "read"=> 1,
        "delete"=> 1,
        "update"=> 1
    ],
    [
        "menu"=> "social_media",
        "user_id"=>$user->id,
        "create"=> 1,
        "read"=> 1,
        "delete"=> 1,
        "update"=> 1
    ],
    [
        "menu"=> "database_management",
        "user_id"=>$user->id,
        "create"=> 1,
        "read"=> 1,
        "delete"=> 1,
        "update"=> 1
    ],
    [
        "menu"=> "notes",
        "user_id"=>$user->id,
        "create"=> 1,
        "read"=> 1,
        "delete"=> 1,
        "update"=> 1
    ],
    [
        "menu"=> "galery",
        "user_id"=>$user->id,
        "create"=> 1,
        "read"=> 1,
        "delete"=> 1,
        "update"=> 1
    ],
    [
        "menu"=> "virtual_meeting",
        "user_id"=>$user->id,
        "create"=> 1,
        "read"=> 1,
        "delete"=> 1,
        "update"=> 1
    ],
    [
        "menu"=> "survey_online",
        "user_id"=>$user->id,
        "create"=> 1,
        "read"=> 1,
        "delete"=> 1,
        "update"=> 1
    ],
    [
        "menu"=> "feed",
        "user_id"=>$user->id,
        "create"=> 1,
        "read"=> 1,
        "delete"=> 1,
        "update"=> 1
    ]
];

foreach ($access_menu as $key => $value) {
    AccessMenu::create($value);
}
}

public function forgot_password()
{
    date_default_timezone_set("Asia/Jakarta");
    $email=request('email');
    $token=Str::uuid(64);
    $date = date_create(date('Y-m-d H:i:s'));
    date_add($date, date_interval_create_from_date_string('1 hour'));
    $expired_at=date_format($date, 'Y-m-d H:i:s');

    $validationMessage = [
        'email.required' => 'Email tidak boleh kosong',
        'email.email' => 'Format email salah',
    ];
    $validation=Validator::make(['email'=>$email],['email'=>'required|email'],$validationMessage);


    if ($validation->fails()) {
        return response()->json(['status'=>'error','message'=>$validation->errors()]);
    }

    if (User::where('email', request('email'))->exists()==false) {
        return response()->json(['status'=>'error','message'=>'Email tidak terdaftar']);
    }
    $email_verification=EmailVerification::where('email',$email)->where('type','forgot_password')->where('expired_at','>',date('Y-m-d H:i:s'));

    if ($email_verification->where('deleted_at',null)->exists()) {

        $request=$email_verification->where('deleted_at',null)->first();

        $date_can_request=date_create($request->updated_at);
        date_add($date_can_request,date_interval_create_from_date_string('30 minutes'));
        $can_request=date_format($date_can_request,'Y-m-d H:i:s');

        $can_request;
        if($request->number_of_request>=3 AND date('Y-m-d H:i:s')<$can_request)
        {
            return response()->json(['status'=>'error','message'=>'Anda telah mencapai batas maksimum pengiriman email verifikasi, silahkan coba 30 menit kembali']);
        } else {
            $number_of_request=$email_verification->where('deleted_at',null)->first()->number_of_request;
            if($number_of_request>=3)
            {
                $number_of_request=1;
            } else {
                $number_of_request=$number_of_request+1;
            }

            $data=[
                'token'=>$token,
                'expired_at'=>$expired_at,
                'number_of_request'=>$number_of_request
            ];

            EmailVerification::where('email',request('email'))->update($data);
            Mail::to(request('email'))->send(new ForgotPassword());
            return response()->json(['status'=>'success','message'=>'Permintaan perubahan password telah dikirimkan']);
        }

    } else {
        $data=[
            'email'=>$email,
            'token'=>$token,
            'type'=>'forgot_password',
            'expired_at'=>$expired_at
        ];

        EmailVerification::create($data);
        Mail::to(request('email'))->send(new ForgotPassword());
        return response()->json(['status'=>'success','message'=>'Permintaan perubahan password telah dikirimkan']);
    }
}

public function login()
{
    $credentials = request(['email', 'password']);

    if (!$token = auth()->attempt($credentials)) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    return $this->respondWithToken($token);
}

public function me()
{
    if(auth()->user()){
        $caleg_id=auth()->user()->caleg_id;
        $user=User::where('id',auth()->user()->id)->with('access_menu','position')->first();
        $caleg=Caleg::where('id',$caleg_id)->first();
        return response()->json(['status'=>'success','data'=>$user,'caleg'=>$caleg]);
    } else {
        return response()->json(['status'=>'error','message'=>'Unauthorized'],401);
    }
        // $data=[
        //     'is_login'=>true
        //     auth()->user();]
        // return response()->json('$data);
}

public function check_token_forgot()
{
    $token = request('token');
    $existtoken = EmailVerification::where('token', $token)->where('type','forgot_password')->where('expired_at','>',date('Y-m-d H:i:s'));

    if ($existtoken->exists() ==null) {
        return response()->json(['status' => 'expired','message'=>'Token telah expired','email'=>null]);
    } else {
        return response()->json(['status' => "active",'message'=>'Token active','email'=>$existtoken->first()->email]);
    }
}

public function change_forgot_password()
{
        //Retrieving password_resets data
    $token=request('token');
    $existtoken = EmailVerification::where('token', $token);
    if ($existtoken->exists() == null) {
        return response()->json(['status' => 'error', 'message' => 'Page expired'], 419);
    }

    $data = [
        'password' => request('password'),
        'confirm_password' => request('confirm_password'),
    ];

    $validationMessage = [
        'password.required' => "Password tidak boleh kosong",
    ];

    $validator = Validator::make($data, [
        'password' => ['required'],
        'confirm_password' => ['required', 'same:password'],
    ], $validationMessage);

    if ($validator->fails()) {
        return response()->json(['status' => 'error', 'message' => $validator->errors()]);
    }

    if ($existtoken->exists()) {
        $email = $existtoken->first()->email;
        $hash = Hash::make(request('password'));
        User::where('email', $email)->update(['password' => $hash]);

            // Deleting password_resets data
        $existtoken->delete();

        return response()->json(['status' => 'success', 'message' => 'Password berhasil diubah']);
    } else {
        return response()->json(['status' => 'error', 'message' => 'Page expired'], 419);
    }
}

protected function respondWithToken($token)
{
    return response()->json([
        'access_token' => $token,
        'token_type' => 'bearer',
        'expires_in' => auth()->factory()->getTTL() * 60,
    ]);
}
}
