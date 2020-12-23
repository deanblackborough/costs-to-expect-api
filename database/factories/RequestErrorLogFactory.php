<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\RequestErrorLog::class, function (Faker $faker) {
    return [
        'method' => $faker->word,
        'source' => $faker->word,
        'debug' => $faker->word,
        'expected_status_code' => $faker->randomNumber(),
        'returned_status_code' => $faker->randomNumber(),
        'request_uri' => $faker->word,
    ];
});
