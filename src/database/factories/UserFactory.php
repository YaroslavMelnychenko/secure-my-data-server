<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Helpers;

use App\Models\Encryption\Asymmetric;
use App\Models\Session;

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

$seed = '0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef';
$keyPair = Asymmetric::createKeyPair($seed);

$factory->define(User::class, function (Faker $faker) use ($keyPair) {
    return [
        'email' => $faker->unique()->safeEmail,
        'password' => bcrypt('passwordstring'),
        'public_key' => $keyPair->exportPublicKey(),
        'verified' => false,
        'verification_code' => $faker->randomNumber(6)
    ];
});

$factory->state(User::class, 'verified', function ($faker) {
    return [
        'verified' => true,
        'verification_code' => null
    ];
});

$factory->afterCreatingState(User::class, 'logged', function ($user, $faker) use ($keyPair) {
    $session = Session::createInstance($user, Helpers::randomHex(856));
    $session->storeKeyPair($keyPair);
});