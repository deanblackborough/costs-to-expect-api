<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\Queue::class, function (Faker $faker) {
    return [
        'queue' => $faker->word,
        'payload' => $faker->text,
        'attempts' => $faker->boolean,
        'reserved_at' => $faker->randomNumber(),
        'available_at' => $faker->randomNumber(),
    ];
});
