<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\ItemType\AllocatedExpense\Model::class, function (Faker $faker) {
    return [
        'item_id' => $faker->randomNumber(),
        'name' => $faker->name,
        'description' => $faker->text,
        'effective_date' => $faker->date(),
        'publish_after' => $faker->date(),
        'currency_id' => $faker->boolean,
        'total' => $faker->randomFloat(),
        'percentage' => $faker->boolean,
        'actualised_total' => $faker->randomFloat(),
    ];
});
