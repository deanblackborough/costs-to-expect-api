<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\ResourceTypeItemType::class, function (Faker $faker) {
    return [
        'resource_type_id' => $faker->randomNumber(),
        'item_type_id' => $faker->boolean,
    ];
});
