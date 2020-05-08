<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Response;
use App\Mail\TestMail;
use Illuminate\Support\Facades\Mail;

class ApiController extends Controller
{
    protected $apiInfo = [
        'description' => 'Secure My Data API for the safe storage of confidential data on the Internet',
        'version' => '1.0'
    ];

    public function index() {
        return Response::send([
            'error' => false,
            'message' => $this->apiInfo
        ], 'SUCCESS');
    }
}
