<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

use App\Models\SecuredData;
use App\Models\User;

use Faker\Generator as Faker;

$pathToImages = 'storage/images';
$image = '110d0d5fedc896df594c36fda68884f3.jpg';

$factory->define(SecuredData::class, function (Faker $faker) {
    return [
        'user_id' => factory(User::class)->states('verified', 'logged'),
        'name' => $faker->catchPhrase,
        'ext' => null,
        'mime_type' => null,
        'size' => null
    ];
});

$factory->state(SecuredData::class, 'attachment', function ($faker) {
    return [
        'ext' => 'jpg',
        'mime_type' => 'image/jpeg',
        'size' => null
    ];
});

$factory->afterCreating(SecuredData::class, function ($instance, $faker) {
    $data = $faker->paragraph(10);
    $name = $faker->catchPhrase;
    
    $instance->storePlainData($instance->user, $name, $data);
});

$factory->afterCreatingState(SecuredData::class, 'attachment', function ($instance, $faker) use ($pathToImages, $image) {
    $attachment = UploadedFile::fake()->image(Str::random(32).'.png', 1280, 1024)->size(2000);    

    $instance->storeAttachment($instance->user, $attachment);
});