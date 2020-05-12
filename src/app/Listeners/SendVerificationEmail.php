<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

use App\Mail\VerificationMail;

class SendVerificationEmail
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UserRegistered  $event
     * @return void
     */
    public function handle(UserRegistered $event)
    {
        $user = $event->user;
        $verificationCode = $this->generateCode();

        $user->verification_code = $verificationCode;
        $user->save();

        Mail::to($user->email)->queue(new VerificationMail($verificationCode));
    }

    private function generateCode() {
        $code = '';

        do {
            $code .= rand(0, 9);
        } while(strlen($code) < 6);

        return $code;
    }
}
