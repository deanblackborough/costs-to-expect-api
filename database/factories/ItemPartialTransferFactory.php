<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\ItemPartialTransfer::class, function (Faker $faker) {
    return [
        'resource_type_id' => $faker->randomNumber(),
        'from' => $faker->randomNumber(),
        'to' => $faker->randomNumber(),
        'item_id' => $faker->randomNumber(),
        'percentage' => $faker->boolean,
        'transferred_by' => $faker->randomNumber(),
    ];
});
