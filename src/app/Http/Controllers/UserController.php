<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

use App\Http\Response;
use App\Http\Requests\User\VerifyRequest;
use App\Http\Requests\User\FaultReportRequest;

use App\Models\Fault;

use App\Events\UserRegistered;

class UserController extends Controller
{
    protected $user;

    public function __construct() {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user(); 
            return $next($request);
        });
    }

    public function verify(VerifyRequest $request) {
        if($this->user->verification_code == $request->code) {

            $this->user->verification_code = null;
            $this->user->verified = true;
            $this->user->save();

            return Response::send([
                'error' => false,
                'message' => 'User successfully verified'
            ], 'SUCCESS');

        } else {
            return Response::send([
                'error' => true,
                'message' => 'Wrong verification code'
            ], 'UNAUTHORIZED');
        }
    }

    public function resendVerification() {
        event(new UserRegistered($this->user));

        return Response::send([
            'error' => false,
            'message' => 'Verification code has been sent'
        ], 'SUCCESS');
    }

    public function details() {
        $details = $this->user;
        $details['session'] = $this->user->session;
        $details['faults'] = $this->user->faults;

        return Response::send([
            'error' => false,
            'message' => $details
        ], 'SUCCESS');
    }

    public function report(FaultReportRequest $request) {
        $fault = Fault::report($this->user, $request);

        return Response::send([
            'error' => false,
            'message' => $fault
        ], 'SUCCESS');
    }
}
