<?php

use Illuminate\Database\Seeder;

class CommunitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Community::class, 30)->create()->each(function (App\Community $community) {
            $community->texts()->saveMany(factory(App\Text::class, 3)->make());
            $community->texts()->save(factory(App\Text::class)->make([ 'user_pseudo' => 'johndoe' ]));
        });
    }
}
