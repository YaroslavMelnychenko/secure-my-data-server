<?php

use Illuminate\Database\Seeder;
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

        $users = factory(User::class, 10)->make();

        $customUser = new User();
        $customUser->email = 'test@example.com';
        $customUser->password = bcrypt('passwordstring');
        $customUser->verification_code = '123456';
        $customUser->public_key = Asymmetric::createKeyPair($seed)->exportPublicKey();

        $users->push($customUser);

        foreach($users as $user) $user->save();
    }
}
