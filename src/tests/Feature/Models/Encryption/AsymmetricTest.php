<?php

namespace Tests\Feature\Models\Encryption;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

use App\Models\Encryption\Asymmetric;
use App\Models\User;
use App\Helpers;

class AsymmetricTest extends TestCase
{
    private function createKeyPair() {
        $seed = Helpers::randomHex(256);

        $keyPair = Asymmetric::createKeyPair($seed);

        return $keyPair;
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testGenerateKeyPairTest()
    {
        $keyPair = $this->createKeyPair();

        $this->assertTrue($keyPair->test());
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testRestoreKeyPairTest()
    {
        $keyPair = $this->createKeyPair();
        $user = factory(User::class)->make();

        $storage = Storage::disk('public');

        $storage->put('test.key', $keyPair->exportPrivateKey([ 'email' => $user->email ]), 'public');
        $storage->assertExists('test.key');
    
        $keyPair = Asymmetric::restoreKeyPair($storage->url('test.key'));

        $this->assertTrue($keyPair->test());

        $storage->delete('test.key');
        $storage->assertMissing('test.key');
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testPublicKeyTest()
    {
        $keyPair = $this->createKeyPair();

        $this->assertStringMatchesFormat("%x", $keyPair->exportPublicKey());
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testPrivateKeyTest()
    {
        $keyPair = $this->createKeyPair();
        $user = factory(User::class)->make();

        $key = explode("\n", $keyPair->exportPrivateKey([ 'email' => $user->email ]));

        $this->assertTrue($key[0] == config('app.name'));

        foreach($key as $index => $value) {
            switch($index) {
                case 2:
                    $this->assertTrue($value == "email: {$user->email}");
                    break;
                    
                case 4:
                case 5:
                case 6:
                case 7:
                    $keyPart = substr($value, 0, 16);
                    $crcPart = substr($value, 16);

                    $crc = dechex(crc32(hex2bin($keyPart)));
                    $crc = substr("00000000", 0, 8 - strlen($crc)).$crc;

                    $this->assertTrue($crc == $crcPart);
                    break;
            }
        }
    }
}
