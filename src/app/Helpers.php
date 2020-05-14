<?php

namespace App;

class Helpers
{
    public static function randomHex($length = 8) {
        $alphabet = '0123456789abcdef';
        
        $hex = '';
    
        do {
            $hex .= $alphabet[rand(0, 15)];
        } while(strlen($hex) < $length);
    
        return $hex;
    }

    public static function formatBytes($size, $precision = 2) {
        if ($size > 0) {
            $size = (int) $size;
            $base = log($size) / log(1024);
            $suffixes = array(' bytes', ' KB', ' MB', ' GB', ' TB');

            return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
        } else {
            return $size;
        }
    }
}