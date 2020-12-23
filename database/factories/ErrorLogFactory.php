<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\ErrorLog::class, function (Faker $faker) {
    return [
        'message' => $faker->text,
        'file' => $faker->word,
        'line' => $faker->word,
        'trace' => $faker->text,
    ];
});
