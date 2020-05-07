<?php

namespace App\Http;

class Response
{
    public static $statusCodes = [
        'NOT_FOUND' => 404,
        'SUCCESS' => 200,
        'VALIDATION_ERROR' => 422,
        'INTERNAL_ERROR' => 500,
        'METHOD_NOT_ALLOWED' => 405,
        'UNAUTHORIZED' => 401,
        'TOO_MANY_ATTEMPTS' => 503
    ];

    public static function send($message, $status, $headers = null) {
        if($headers === null) {
            return response($message, static::$statusCodes[$status]);
        } else {
            return response($message, static::$statusCodes[$status])->withHeaders($headers);
        }
    }

    public static function error($message, $status) {
        return response([
            'error' => true,
            'message' => $message
        ], static::$statusCodes[$status]);
    }
}