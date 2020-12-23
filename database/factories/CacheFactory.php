<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\Cache::class, function (Faker $faker) {
    return [
        'key' => $faker->word,
        'value' => $faker->text,
        'expiration' => $faker->randomNumber(),
    ];
});
