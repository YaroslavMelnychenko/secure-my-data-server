<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

use App\Models\User;
use App\Models\Encryption\Asymmetric;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $seed = '0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef';

        $verifiedUsers = factory(User::class, 10)->states('verified')->create([
            'public_key' => Asymmetric::createKeyPair($seed)->exportPublicKey()
        ]);

        $unverifiedUsers = factory(User::class, 10)->states('unverified')->create([
            'public_key' => Asymmetric::createKeyPair($seed)->exportPublicKey()
        ]);

        $customUser = factory(User::class)->create([
            'email' => 'test@example.com',
            'verified' => false,
            'verification_code' => '123456',
            'public_key' => Asymmetric::createKeyPair($seed)->exportPublicKey()
        ]);
    }
}
