<?php

namespace App\Http\Controllers;

use App\Http\Response;

class UserController extends Controller
{
    public function details() {
        return Response::send('test', 'SUCCESS');
    }
}
