<?php

namespace App\Exceptions;

use App\Http\Response;
use Carbon\Carbon;

class ResponseSender
{
    public static function __callStatic($name, $arguments) {
        $exception = $arguments[1];

        dd($exception);

        if(config('app.env') === 'local') {
            return Response::send([
                'error' => true,
                'message' => get_class($exception).'. '.$exception->getMessage(),
                'trace' => $exception->getTrace()
            ], 'INTERNAL_ERROR');
        } else {
            return Response::error('Internal server error', 'INTERNAL_ERROR');
        }
    }

    public static function NotFoundHttpException($request, $exception) {
        return Response::error('Not found', 'NOT_FOUND');
    }
    
    public static function ModelNotFoundException($request, $exception) {
        return Response::error('Requested resource is not found', 'NOT_FOUND');
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

    public static function MethodNotAllowedHttpException($request, $exception) {
        return Response::error($exception->getMessage(), 'METHOD_NOT_ALLOWED');
    }

    public static function ValidationException($request, $exception) {
        return Response::error($exception->errors(), 'VALIDATION_ERROR');
    }

    public static function AuthenticationException($request, $exception) {
        return Response::error('Unauthenticated. Proceed to '.route('login').' for authentication', 'UNAUTHORIZED');
    }

    public static function OAuthServerException($request, $exception) {
        return Response::error($exception->getMessage(), 'UNAUTHORIZED');
    }
}