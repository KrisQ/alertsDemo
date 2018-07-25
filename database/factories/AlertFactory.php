<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\Alert::class, function (Faker $faker) {
    return [
        'user_id' => 1,
        'customer_id' => 1,
        'date_range' => '2018-07-31',
        'short_description' => str_random(10),
        'long_description' => str_random(200),
        'status' => 1
    ];
});
