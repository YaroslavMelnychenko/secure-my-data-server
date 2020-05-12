<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

use App\Http\Response;
use App\Http\Requests\UserVerifyRequest;

class UserController extends Controller
{
    public function verify(UserVerifyRequest $request) {
        $user = Auth::user();

        if($user->verified) {
            return Response::send([
                'error' => false,
                'message' => 'User is already verified'
            ], 'ALREADY_EXISTS');
        } else {

            if($user->verification_code == $request->code) {

                $user->verification_code = null;
                $user->verified = true;
                $user->save();

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
    }

    public function details() {
        return Response::send([
            'error' => false,
            'message' => Auth::user()
        ], 'SUCCESS');
    }
}
