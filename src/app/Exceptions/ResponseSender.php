<?php

namespace App\Exceptions;

use App\Http\Response;
use Carbon\Carbon;

class ResponseSender
{
    public static function __callStatic($name, $arguments) {
        if(config('app.env') === 'local') {
            return Response::error($arguments[1]->getMessage(), 'INTERNAL_ERROR');
        } else {
            return Response::error('Internal server error', 'INTERNAL_ERROR');
        }
    }

    public static function NotFoundHttpException($request, $exception) {
        return Response::error('Not round', 'NOT_FOUND');
    }

    public static function ThrottleRequestsException($request, $exception) {
        $headers = $exception->getHeaders();
        $retryAfter = Carbon::now()->diffInSeconds(new Carbon($headers['X-RateLimit-Reset']));

        $message = "You sent too many requests, try again in $retryAfter seconds";
        return Response::send([
            'error' => true,
            'message' => $message
        ], 'TOO_MANY_ATTEMPTS', $headers);
    }

    public static function QueryException($request, $exception) {
        return Response::error('Database error', 'INTERNAL_ERROR');
    }
}