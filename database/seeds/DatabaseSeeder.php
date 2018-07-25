<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        // DB::table('alerts')->insert([
        //     'user_id' => 1,
        //     'customer_id' => 1,
        //     'date_range' => '2018-07-31',
        //     'short_description' => str_random(10),
        //     'long_description' => str_random(200),
        //     'status' => 1
        // ]);

        factory(App\Alert::class, 3000)->create();
    

    }
}
