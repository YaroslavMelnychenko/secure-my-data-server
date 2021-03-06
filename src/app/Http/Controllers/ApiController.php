<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Response;
use App\Mail\TestMail;
use Illuminate\Support\Facades\Mail;

class ApiController extends Controller
{
    public function index() {
        $apiInfo = [
            'description' => 'Secure My Data API for the safe storage of confidential data on the Internet',
            'version' => '1.0',
            'session_lifetime' => config('passport.session_lifetime'),
            'rules' => [
                'attachment' => [
                    'max_size' => config('secured_data.attachment.max_size'),
                    'mime_types' => config('secured_data.attachment.mimetypes')
                ],
                'plain_data' => [
                    'max_size' => config('secured_data.plain_data.max_size')
                ]
            ]
        ];

        return Response::send([
            'error' => false,
            'message' => $apiInfo
        ], 'SUCCESS');
    }
}
