<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'first_name' => $faker->name,
		'last_name' => $faker->lastName,
		'email' => $faker->email,
		'username' => $faker->word . str_random(10),
		'password' => bcrypt($faker->password),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Ad::class, function (Faker\Generator $faker) {
    return [
        'user_id' => 1,
		'title' => $faker->sentence,
		'description' => $faker->sentence,
		'category_id' => $faker->randomDigit, // set a random category id
		'price' => $faker->randomFloat,
        'latitude' => $faker->randomDigit,
        'longitude' => $faker->randomDigit,
        'currency_code' => $faker->currencyCode,
    ];
});
