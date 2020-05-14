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
        $unverifiedUsers = factory(User::class, 10)->create();
        
        $verifiedUsers = factory(User::class, 10)->states('verified')->create();

        $customUser = factory(User::class)->create([
            'email' => 'test@example.com',
            'verified' => false,
            'verification_code' => '123456'
        ]);
    }
}
