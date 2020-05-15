<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SecuredData;
use App\Models\User;
use App\Models\Symmetric;
use App\Models\Asymmetric;

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
    $encryptedData = $instance->cryptor()->encrypt($data);
    $instance->saveToCloud($encryptedData);
});

$factory->afterCreatingState(SecuredData::class, 'attachment', function ($instance, $faker) use ($pathToImages, $image) {
    $imageFile = config('app.url').'/'.$pathToImages.'/'.$image;
    
    $tmp = tmpfile();
    fwrite($tmp, file_get_contents($imageFile));
    
    $encryptedFile = $instance->cryptor()->encryptFile(stream_get_meta_data($tmp)['uri']);

    fclose($tmp);

    $instance->saveToCloud($encryptedFile);
});