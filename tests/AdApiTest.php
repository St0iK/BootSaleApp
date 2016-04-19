<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

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
     * A basic test example.
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
}
