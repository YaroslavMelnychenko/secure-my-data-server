<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthLoginRequest;
use App\Http\Requests\AuthRegisterRequest;
use App\Http\Requests\AuthRefreshRequest;
use App\Http\Response;

use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Client as OClient; 
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    private function issueTokens($request, $refresh = false) {
        $oClient = OClient::where('password_client', 1)->first();

        $params = [
            'client_id' => $oClient->id,
            'client_secret' => $oClient->secret,
            'scope' => '*',
        ];

        if($refresh) {
            $params = array_merge($params, [
                'grant_type' => 'refresh_token',
                'refresh_token' => $request->refresh_token
            ]);
        } else {
            $params = array_merge($params, [
                'grant_type' => 'password',
                'username' => $request->email,
                'password' => $request->password,
            ]);
        };

        $jsonResponse = Http::asForm()->post(config('app.url').'/oauth/token', $params);

        $response = json_decode((string) $jsonResponse->getBody(), true);
        
        if(array_key_exists('message', $response)) {
            return Response::send($response, 'UNAUTHORIZED');
        } else {
            return Response::send([
                'error' => false,
                'message' => $response
            ], 'SUCCESS');
        }
    }

    public function login(AuthLoginRequest $request) {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            
            return $this->issueTokens($request);

        } else {
            return Response::send([
                'error' => true,
                'message' => 'Wrong credentials'
            ], 'UNAUTHORIZED');
        }        
    }

    public function refresh(AuthRefreshRequest $request) {
        return $this->issueTokens($request, true); 
    }

    public function register(AuthRegisterRequest $request) {
        return Response::send('test', 'SUCCESS');
    }
}
