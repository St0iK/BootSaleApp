<?php

use Illuminate\Database\Seeder;
use App\Ad;
use App\AdCategory;
use App\User;

class AdsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();
        $users = User::lists('id')->All();
        $categories = AdCategory::lists('id')->All();
        Ad::truncate();

        foreach(range(1,3000) as $index)  
        {  
            Ad::create([  
            	'title' => $faker->sentence,
                'description' => $faker->paragraph,   
                'category_id' => $faker->randomElement($categories),
                'status' => 1, 
                'price' => $faker->randomNumber(2),   
                'latitude' => $faker->latitude,  
                'longitude' => $faker->longitude,  
                'currency_code' => $faker->currencyCode,
                'user_id' => $faker->randomElement($users),
            ]);  
        }
    }
}
