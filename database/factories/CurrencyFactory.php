<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\Currency::class, function (Faker $faker) {
    return [
        'code' => $faker->word,
        'name' => $faker->name,
    ];
});
