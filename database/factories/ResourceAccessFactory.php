<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\ResourceAccess::class, function (Faker $faker) {
    return [
        'resource_type_id' => $faker->randomNumber(),
        'user_id' => $faker->randomNumber(),
        'added_by' => $faker->randomNumber(),
    ];
});
