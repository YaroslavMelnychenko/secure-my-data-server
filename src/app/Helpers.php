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
}