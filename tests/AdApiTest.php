<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use Illuminate\Http\UploadedFile;
use App\Ad;

/**
 * TODO:
 *
 * Things that need to be tested:
 * 1. Get ads
 *    Get ads missing latitude and longitude
 * 2. Post ads
 *    Create a new ad with images
 *    Validation failed
 * Test should check status codes, erore codes, messages, status codes
 */
class AdApiTest extends TestCase
{
    /**
     * Test that GET for ads works
     *
     * @return void
     */
    public function testAdsGet()
    {
    	$from_latitude = 1;
    	$from_longitude = 1;
        $this->json('GET', 
        	'/api/v1/ads?from_latitude='.$from_latitude.'&from_longitude='.$from_longitude)
                     ->seeJson([
                         'status' => true,
                         'status_code' => 200
                     ]);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testAdsGetValidationFailed()
    {
        $this->json('GET', 
            '/api/v1/ads')->seeJson([
                         'status' => false,
                         "errors" => "Wrong request. Please include all required data"
                     ]);
                     
    }

    /**
     * Basic test for creating new AD
     *
     * @return void
     */
    public function testCreateNewAd()
    {
        $ad = factory(App\Ad::class)->make();
        $this->json('POST', '/api/v1/ads',$ad->toArray())
                     ->seeJson([
                         'status' => true,
                         'status_code' => 201
                     ]);
                     
    }

    /**
     * Test ad creation with file upload to AWS
     *
     * @return void
     */
    public function testCreateNewAdWithPhotoUpload()
    {
        $file = $this->createLocalImageForTesting();
        $ad = factory(App\Ad::class)->make();
        $this->call('POST', '/api/v1/ads', $ad->toArray(), [], ['image_1' => $file], ['Accept' => 'application/json']);
        $file = $this->testRemoteImage();
       
    }

    /**
     * Test Multile files upload
     */
    public function testCreateNewAdWithMultiplePhotoUpload()
    {
        $ad = factory(App\Ad::class)->make();
        $file = $this->createLocalImageForTesting();
        $this->call('POST', '/api/v1/ads', $ad->toArray(), [], ['image_1' => $file,'image_2' => $file,'image_3' => $file,'image_4' => $file,'image_5' => $file], ['Accept' => 'application/json']);
        $this->testRemoteImage();
        
    }

    /**
     * Test validation for required fields
     */
    public function testAdPostValidationFail(){
        
        $post = ['category_id' => 6];

        $this->json('POST', '/api/v1/ads', $post)
                     ->seeJson([
                         'status' => false,
                         'message' => 'Please check your post, required fields are missing.'
                     ]);
    }

    function createLocalImageForTesting()
    {
        $stub = __DIR__.'/stubs/test.png';
        $name = str_random(8).'.png';
        $path = sys_get_temp_dir().'/'.$name;
        copy($stub, $path);
        return new UploadedFile($path, $name, filesize($path), 'image/png', null, true);
    }

    function testRemoteImage()
    {
       $s3 = AWS::createClient('s3');
       $lastInsertedAd = Ad::orderBy('id', 'desc')->with(['photos'])->first();
       if(empty($lastInsertedAd->photos))
       {
           $this->assertTrue(FALSE, 'Nope it didnt work');
       }

       foreach ($lastInsertedAd->photos as $key => $photo) 
       {
           $keyname = $lastInsertedAd->photos[$key]['path'];
           $result = $s3->getObject(array(
               'Bucket' => \Config::get('aws.bucket'),
               'Key'    => $keyname
           ));
           
           if ($result)
           {
               $this->assertTrue(TRUE,'Worked');
           }    
       }
    }
}
