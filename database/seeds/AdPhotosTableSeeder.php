<?php

use Illuminate\Database\Seeder;
use App\Ad;
use App\AdPhoto;

class AdPhotosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $s3 = AWS::createClient('s3');
        $faker = Faker\Factory::create();
        $ads = Ad::lists('id')->All();
        AdPhoto::truncate();
        $images = array();
        foreach(range(1,100) as $index)  
        {
             $image = $faker->image('/tmp');
        	 $images[] = $image;
             $s3->putObject(array(
                 'Bucket'     => '7480683303',
                 'Key'        => "ads/".basename($image),
                 'SourceFile' => $image,
             ));
        }

        foreach(range(1,3000) as $index)  
        {  
        	
        	$image = $images[array_rand($images)];
            AdPhoto::create([  
                'ad_id' => $faker->randomElement($ads),
                'title' => $faker->sentence(),
                'description' => $faker->paragraph(),
                'path' => $image,
                'thumb_path' => "ads/".basename($image),
            ]);  
        }
    }
}
