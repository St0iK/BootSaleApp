<?php

use Illuminate\Database\Seeder;
use App\User;

class UsersTableSeeder extends Seeder
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
        $images = array();
        foreach(range(1,100) as $index)  
        {
             $image = $faker->image('/tmp', 640, 480, "people");
             $images[] = $image;
             $s3->putObject(array(
                 'Bucket'     => '7480683303',
                 'Key'        => "users/".basename($image),
                 'SourceFile' => $image,
             ));
        }
        User::truncate();

        foreach(range(1,300) as $index)  
        {  
            $image = $images[array_rand($images)];
            User::create([  
                'username' => str_replace('.', '_', $faker->unique()->userName),   
                'first_name' => $faker->firstName,  
                'last_name' => $faker->lastName, 
                'display_name' => str_replace('.', '_', $faker->userName),   
                'email' => $faker->email,  
                'telephone' => $faker->phoneNumber,  
                'password' => bcrypt('password'),  
                'status' => 1,
                'profile_pic' => "users/".basename($image),
            ]);  
        }
       
    }
}
