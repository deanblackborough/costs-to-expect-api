<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\Item::class, function (Faker $faker) {
    return [
        'resource_id' => factory(App\Models\Resource::class),
        'created_by' => $faker->randomNumber(),
        'updated_by' => $faker->randomNumber(),
    ];
});
