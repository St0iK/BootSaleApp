<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(CategoriesTableSeeder::class);
        $this->call(AdsTableSeeder::class);
        $this->call(AdBidsTableSeeder::class);
        $this->call(AdCommentsTableSeeder::class);
        $this->call(AdPhotosTableSeeder::class);
        
    }
}
