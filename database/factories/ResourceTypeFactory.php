<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\ResourceType::class, function (Faker $faker) {
    return [
        'public' => $faker->boolean,
        'name' => $faker->name,
        'description' => $faker->text,
    ];
});
