<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\Category::class, function (Faker $faker) {
    return [
        'resource_type_id' => $faker->randomNumber(),
        'name' => $faker->name,
        'description' => $faker->text,
        'category_id' => factory(App\Models\Category::class),
    ];
});
