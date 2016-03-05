<?php

use Illuminate\Database\Seeder;
use App\AdCategory;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();
        AdCategory::truncate();

        foreach(range(1,20) as $index)  
        {  
            AdCategory::create([  
            	'title' => ucfirst($faker->word),
                'description' => $faker->paragraph
            ]);  
        }
    }
}
