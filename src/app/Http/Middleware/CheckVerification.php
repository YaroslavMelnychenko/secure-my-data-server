<?php

namespace App\Http\Middleware;

use Closure;

use Illuminate\Support\Facades\Auth;
use App\Http\Response;

class CheckVerification
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $verified)
    {
        $user = Auth::user();

        $verifiedError = Response::send([
            'error' => true,
            'message' => 'User is already verified'
        ], 'ALREADY_EXISTS');

        $notVerifiedError = Response::send([
            'error' => true,
            'message' => 'User is not verified'
        ], 'UNAUTHORIZED');

        if($verified == 'yes') {

            if(!$user->verified) return $notVerifiedError;

        } else if($verified == 'no') {

            if($user->verified) return $verifiedError;

        }     

        return $next($request);
    }
}
