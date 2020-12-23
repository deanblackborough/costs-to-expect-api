<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\ItemSubcategory::class, function (Faker $faker) {
    return [
        'item_category_id' => factory(App\Models\ItemCategory::class),
        'sub_category_id' => $faker->randomNumber(),
    ];
});
