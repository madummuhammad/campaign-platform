<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Crypt;
use App\Models\EmailVerification;
use App\Models\PasswordReset;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class RegisterEmail extends Mailable
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
        $token=EmailVerification::where('email',request('email'))->where('type','register')->where('expired_at','>',date('Y-m-d H:i:s'))->where('deleted_at',null)->first();
        return $this->from('support@crevtech.id')
        ->view('registeremail')
        ->with(
            [
                'email' => request('email'),
                'token'=>$token->token
            ]);
    }
}