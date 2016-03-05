<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Ad;
use App\AdBid;

class AdBidsTableSeeder extends Seeder
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
        $ads = Ad::lists('id')->All();
        AdBid::truncate();

        foreach(range(1,3000) as $index)  
        {  
            AdBid::create([  
                'status' => 1, 
                'amount' => $faker->randomNumber(2),   
                'ad_id' => $faker->randomElement($ads),
                'user_id' => $faker->randomElement($users),
            ]);  
        }
    }
}
