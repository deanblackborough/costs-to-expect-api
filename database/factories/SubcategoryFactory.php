<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\Subcategory::class, function (Faker $faker) {
    return [
        'category_id' => factory(App\Models\Category::class),
        'name' => $faker->name,
        'description' => $faker->text,
    ];
});
