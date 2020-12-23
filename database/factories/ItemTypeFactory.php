<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\ItemType::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'friendly_name' => $faker->word,
        'description' => $faker->text,
        'example' => $faker->word,
    ];
});
