<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Ad;
use App\AdComment;

class AdCommentsTableSeeder extends Seeder
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
        AdComment::truncate();

        foreach(range(1,3000) as $index)  
        {  
            AdComment::create([  
                'comment' => $faker->sentence(),   
                'ad_id' => $faker->randomElement($ads),
                'user_id' => $faker->randomElement($users),
            ]);  
        }
    }
}
