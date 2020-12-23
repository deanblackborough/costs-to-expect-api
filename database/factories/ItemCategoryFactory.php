<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\ItemCategory::class, function (Faker $faker) {
    return [
        'item_id' => $faker->randomNumber(),
        'category_id' => $faker->randomNumber(),
    ];
});
