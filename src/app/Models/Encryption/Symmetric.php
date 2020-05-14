<?php

namespace App\Models\Encryption;

use ParagonIE\Halite\KeyFactory;
use ParagonIE\Halite\Symmetric\EncryptionKey;
use ParagonIE\HiddenString\HiddenString;
use ParagonIE\Halite\Symmetric\Crypto;
use ParagonIE\Halite\File;

use Illuminate\Support\Str;

use App\Models\Encryption\Asymmetric;

class Symmetric {
    // generate single key
    
    // encrypt data

    private $key;

    private function __construct(EncryptionKey $key) {
        $this->key = $key;
    }

    public function encryptFile($file) {
        $encrypted = 'encrypted';
        File::encrypt($file, $encrypted, $this->key);

        return $encrypted;
    }

    public function decryptFile($file) {
        $decrypted = 'decrypted';
        File::decrypt($file, $decrypted, $this->key);

        return $decrypted;
    }

    public function encrypt($plainData) {
        return Crypto::encrypt(new HiddenString($plainData), $this->key);
    }

    public function decrypt($cipherData) {
        return Crypto::decrypt($cipherData, $this->key)->getString();
    }

    public function test() {
        $testString = Str::random(40);
        $testCipher = $this->encrypt($testString);
          
        return $testString == $this->decrypt($testCipher);
    }

    public static function createFromAsymmetric($secret, Asymmetric $keyPair) {
        $privateKey = $keyPair->exportPrivateKey([]);

        $key = KeyFactory::deriveEncryptionKey(
            new HiddenString($privateKey),
            substr($secret, 0, 16)
        );

        $instance = new static($key);

        return $instance;
    }
}