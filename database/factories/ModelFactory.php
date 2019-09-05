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
        'pseudo' => $faker->unique()->username,
        'name' => $faker->firstName,
        'email' => $faker->unique()->email,
        'password' => app('hash')->make($faker->password),
    ];
});

$factory->define(App\Community::class, function (Faker\Generator $faker) {
    return [
        'pseudo' => $faker->unique()->word,
        'name' => $faker->words(2, true),
        'description' => $faker->text,
        'user_pseudo' => factory(App\User::class)->create()->pseudo,
    ];
});

$factory->define(App\Text::class, function (Faker\Generator $faker) {
    return [
        'text' => $faker->paragraphs(2, true),
        'community_pseudo' => factory(App\Community::class)->create()->pseudo,
        'user_pseudo' => factory(App\User::class)->create()->pseudo,
    ];
});

$factory->define(App\Suggestion::class, function (Faker\Generator $faker) {
    return [
        'original' => $faker->text,
        'suggestion' => $faker->text,
        'text_id' => factory(App\Text::class)->create()->id,
        'user_pseudo' => factory(App\User::class)->create()->pseudo,
    ];
});
