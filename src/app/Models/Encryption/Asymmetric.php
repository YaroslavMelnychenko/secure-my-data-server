<?php

namespace App\Models\Encryption;

use ParagonIE\Halite\KeyFactory;
use ParagonIE\HiddenString\HiddenString;
use ParagonIE\Halite\EncryptionKeyPair;
use ParagonIE\Halite\Asymmetric\EncryptionSecretKey;
use ParagonIE\Halite\Asymmetric\Crypto;

use Illuminate\Support\Str;

class Asymmetric {
    private $privateKey, $publicKey;

    private function derivePublicKey() {
        return bin2hex($this->publicKey->getRawKeyMaterial());
    }

    private function derivePrivateKey() {
        return bin2hex($this->privateKey->getRawKeyMaterial());
    }

    private function generateKeyFile($hex, $userData = []) {
        $header = config('app.name')."\nElliptic Curve25519 secret key\n";
        $content = "";
        
        foreach($userData as $key => $value) {
            $content .= $key.": ".$value."\n";
        }

        $content .= "\n";

        $keyParts = str_split($hex, 16);

        foreach($keyParts as $part) {
            $crc = dechex(crc32(hex2bin($part)));
            $content .= $part.substr("00000000", 0, 8 - strlen($crc)).$crc."\n";
        }

        return $header.$content;
    }

    private static function readKeyFromFile($privateKeyFile) {
        $key = fopen($privateKeyFile, "r");
        $email = '';
        $keyBits = [];
        $keyString = '';

        if ($key) {
            while (($buffer = fgets($key)) !== false) {
                if(preg_match("/^[a-f0-9]{24}$/", $buffer)) {
                    $keyBits[] = str_replace(["\n", ' '], '', $buffer);
                }
            }
        }

        fclose($key);

        if(count($keyBits) != 4) {
            return false;
        }

        foreach($keyBits as $key) {
            $keyPart = substr($key, 0, 16);
            $crcPart = substr($key, 16);

            $crc = dechex(crc32(hex2bin($keyPart)));
            $crc = substr("00000000", 0, 8 - strlen($crc)).$crc;

            if($crc != $crcPart) {
                return false;
            } else {
                $keyString .= $keyPart;
            }
        }
        
        return $keyString;
    }

    private function __construct(EncryptionKeyPair $keyPair) {
        $this->publicKey = $keyPair->getPublicKey();
        $this->privateKey = $keyPair->getSecretKey();
    }

    public function exportPublicKey() {
        return $this->derivePublicKey();
    }

    public function exportPrivateKey($userData) {
        return $this->generateKeyFile($this->derivePrivateKey(), $userData); 
    }

    public function cryptor() {

        return new class($this->publicKey, $this->privateKey) {
            private $publicKey;
            private $privateKey;

            public function __construct($publicKey, $privateKey) {
                $this->publicKey = $publicKey;
                $this->privateKey = $privateKey;
            }

            public function encrypt($plainText) {
                return Crypto::seal(new HiddenString($plainText), $this->publicKey);
            }

            public function decrypt($cipherText) {
                return Crypto::unseal($cipherText, $this->privateKey)->getString();
            }
        };

    }

    public function test() {
        $testString = Str::random(40);
        $testCipher = $this->cryptor()->encrypt($testString);
          
        return $testString == $this->cryptor()->decrypt($testCipher);
    }

    public static function createKeyPair($seed) {
        $keyPair = KeyFactory::deriveEncryptionKeyPair(
            new HiddenString(hex2bin($seed)),
            hex2bin(env('EC_SALT'))
        );

        $instance = new static($keyPair);

        return $instance;
    }

    public static function restoreKeyPair($privateKeyFile) {
        $privateKeyMaterial = self::readKeyFromFile($privateKeyFile);

        if(!$privateKeyMaterial) return false;

        $privateKey = new EncryptionSecretKey(new HiddenString(hex2bin($privateKeyMaterial)));

        $keyPair = new EncryptionKeyPair($privateKey);

        $instance = new static($keyPair);

        return $instance;
    }
}