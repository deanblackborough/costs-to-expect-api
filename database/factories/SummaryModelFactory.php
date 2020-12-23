<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\ItemType\AllocatedExpense\SummaryModel::class, function (Faker $faker) {
    return [
        'resource_id' => $faker->randomNumber(),
        'created_by' => $faker->randomNumber(),
        'updated_by' => $faker->randomNumber(),
    ];
});
