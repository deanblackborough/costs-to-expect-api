<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\Resource::class, function (Faker $faker) {
    return [
        'resource_type_id' => factory(App\Models\ResourceType::class),
        'name' => $faker->name,
        'description' => $faker->text,
        'effective_date' => $faker->date(),
    ];
});
