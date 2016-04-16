<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * TODO:
 *
 * Things that need to be tested:
 * 1. Successfull register
 * 2. Validation failed register
 *    - username/email exists
 *    - Parameters missing
 * 3. Login successfull
 * 4. Login validation failed (will all messages)
 *    - Parameters missing
 *
 * Test should check status codes, erore codes, messages, status codes
 */
class ApiUsersTest extends TestCase
{
    /**
     * Test api for user registration
     *
     * @return void
     */
    public function testUserRegistration()
    {
    	$postData = ['first_name' => 'Iverson',
			    	 'last_name' => 'Allen',
			    	 'email' => 'jstoikidis@gmail.com',
			    	 'username' => 'Allen',
			    	 'password' => 'Allen',
    				];

        $this->json('POST', '/api/v1/users/register', $postData)
                     ->seeJson([
                         'email' => 'jstoikidis@gmail.com',
                     ]);
    }

    /**
     * Test api for user login
     */
    public function testUserLogin()
    {
        $this->assertTrue(true);
    }
}
