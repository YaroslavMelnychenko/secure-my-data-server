<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Helpers;

use App\Models\Encryption\Asymmetric;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
    return [
        'email' => $faker->unique()->safeEmail,
        'password' => bcrypt('passwordstring'),
        'public_key' => Asymmetric::createKeyPair(Helpers::randomHex(256))->exportPublicKey()
    ];
});

$factory->state(User::class, 'verified', function ($faker) {
    return [
        'verified' => true,
        'verification_code' => null
    ];
});

$factory->state(User::class, 'unverified', function ($faker) {
    return [
        'verified' => false,
        'verification_code' => $faker->randomNumber(6)
    ];
});