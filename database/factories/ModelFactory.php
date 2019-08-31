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
        'pseudo' => $faker->username,
        'name' => $faker->name,
        'email' => $faker->email,
        'password' => app('hash')->make($faker->password),
    ];
});

$factory->define(App\Community::class, function (Faker\Generator $faker) {
    return [
        'pseudo' => $faker->username,
        'name' => $faker->company,
        'description' => $faker->text,
        'user_pseudo' => factory(App\User::class)->create()->pseudo,
    ];
});

$factory->define(App\Text::class, function (Faker\Generator $faker) {
    return [
        'text' => $faker->text,
        'community_pseudo' => factory(App\Community::class)->create()->pseudo,
        'user_pseudo' => factory(App\User::class)->create()->pseudo,
    ];
});
