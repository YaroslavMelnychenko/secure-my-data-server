<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\RefreshRequest;
use App\Http\Response;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Laravel\Passport\Client as OauthClient; 
use Laravel\Passport\RefreshToken;

use App\Models\User;
use App\Models\Encryption\Asymmetric;
use App\Models\Session;

class AuthController extends Controller
{
    private function ouathRequest($params) {
        $oauthClient = OauthClient::where('password_client', 1)->first();

        $secretParams = [
            'client_id' => $oauthClient->id,
            'client_secret' => $oauthClient->secret,
            'scope' => '*',
        ];

        $params = array_merge($params, $secretParams);
        $proxy = Request::create('/oauth/token','POST', $params);
        $response = app()->handle($proxy);

        return json_decode((string) $response->getContent(), true);        
    }

    private function issueTokens($request) {
        return $this->ouathRequest([
            'grant_type' => 'password',
            'username' => $request->email,
            'password' => $request->password,
        ]);
    }

    private function refreshTokens($request) {
        return $this->ouathRequest([
            'grant_type' => 'refresh_token',
            'refresh_token' => $request->refresh_token
        ]);
    }

    public function login(LoginRequest $request) {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])) {

            $user = Auth::user();

            $keyPair = Asymmetric::restoreKeyPair($request->private_key);

            if(!$keyPair || !$keyPair->test()) {
                return Response::send([
                    'error' => true,
                    'message' => 'Bad or damaged key'
                ], 'BAD_REQUEST');
            }

            if(!$user->asymmetricChallenge($keyPair)) {
                return Response::send([
                    'error' => true,
                    'message' => 'Wrong key'
                ], 'UNAUTHORIZED');
            }

            $oauthResponse = $this->issueTokens($request);

            $session = Session::createInstance($user, $oauthResponse['refresh_token']);
            $session->storeKeyPair($keyPair);
        
            return Response::send([
                'error' => false,
                'message' => $oauthResponse
            ], 'SUCCESS');

        } else {

            return Response::send([
                'error' => true,
                'message' => 'Wrong credentials'
            ], 'UNAUTHORIZED');

        }        
    }

    public function refresh(RefreshRequest $request) {
        $oauthResponse = $this->refreshTokens($request);
        
        if(array_key_exists('message', $oauthResponse)) {
            return Response::send($oauthResponse, 'UNAUTHORIZED');
        } else {
            
            Session::refreshInstance($request->refresh_token, $oauthResponse['refresh_token']);

            return Response::send([
                'error' => false,
                'message' => $oauthResponse
            ], 'SUCCESS');
        }
    }

    public function register(RegisterRequest $request) {
        if(User::exists($request->email)) {
            return Response::send([
                'error' => true,
                'message' => "User with email {$request->email} already exists"
            ], 'ALREADY_EXISTS');
        } 

        $keyPair = Asymmetric::createKeyPair($request->seed);

        $user = User::create($request, $keyPair);

        return response()->streamDownload(function () use ($keyPair, $request) {
            echo $keyPair->exportPrivateKey([ 'email' => $request->email ]);
        }, Str::random(32).'.key', [
            'Content-Type' => 'text/plain'
        ]);
    }
}
