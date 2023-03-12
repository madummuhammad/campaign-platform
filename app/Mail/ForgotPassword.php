<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Crypt;
use App\Models\User;
use App\Models\PasswordReset;
use App\Models\EmailVerification;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ForgotPassword extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $user=User::where('email',request('email'))->first();
        $token=EmailVerification::where('email',request('email'))->where('type','forgot_password')->where('expired_at','>',date('Y-m-d H:i:s'))->where('deleted_at',null)->first();
         return $this->from('support@crevtech.id')
                   ->view('forgotpassword')
                   ->with(
                    [
                        // 'nama' => $user->name,
                        'token'=>$token->token
                    ]);
    }
}