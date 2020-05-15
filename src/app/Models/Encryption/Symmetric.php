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

    private $key, $tmp;

    private function __construct(EncryptionKey $key) {
        $this->key = $key;
    }

    private function createTmpFile() {
        $tmp = tmpfile();
        $this->tmp = $tmp;
        return stream_get_meta_data($tmp)['uri'];
    }

    private function closeTmpFile() {
        fclose($this->tmp);
    }

    public function encryptFile($file) {
        $encrypted = $this->createTmpFile();

        File::encrypt($file, $encrypted, $this->key);
        $content = file_get_contents($encrypted);

        $this->closeTmpFile();

        return $content;
    }

    public function decryptFile($file) {
        $decrypted = $this->createTmpFile();

        File::decrypt($file, $decrypted, $this->key);
        $content = file_get_contents($decrypted);

        $this->closeTmpFile();

        return $content;
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