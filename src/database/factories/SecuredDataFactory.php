<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SecuredData;
use App\Models\User;

use Faker\Generator as Faker;

$factory->define(SecuredData::class, function (Faker $faker) {
    return [
        'user_id' => factory(User::class)->states('verified'),
        'name' => $faker->name,
        'ext' => $faker->fileExtension,
        'mime_type' => $faker->mimeType
    ];
});
