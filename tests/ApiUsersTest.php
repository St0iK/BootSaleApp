<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\User;

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
    use DatabaseMigrations;

    /**
     * Test api for user registration
     *
     * @return void
     */
    public function testUserRegistration()
    {
        $user = factory(App\User::class)->make();
        $post = [
            'username' => $user->username,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'password' => str_random(10)
        ];

        $this->json('POST', '/api/v1/users/register', $post)
                     ->seeJsonEquals([
                         'status' => true,
                         'data' => 'User created successfully',
                         'status_code' => 200
                     ]);
    }

    /**
     * [testUserRegistration description]
     * @return [type] [description]
     */
    public function testUserRegistrationFailEmailExists()
    {
        $user1 = factory(App\User::class)->create();
        $user = factory(App\User::class)->make();
        $post = [
            'username' => $user->username,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user1->email,
            'password' => str_random(10)
        ];

        $this->json('POST', '/api/v1/users/register', $post)
                     ->seeJson([
                         'status' => false,
                         "errors" => [["email" => ["The email has already been taken."]]]
                     ]);
    }


    /**
     * [testUserRegistration description]
     * @return [type] [description]
     */
    public function testUserRegistrationFailUsernameExists()
    {
        $user1 = factory(App\User::class)->create();
        $user = factory(App\User::class)->make();
        $post = [
            'username' => $user1->username,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'password' => str_random(10)
        ];

        $this->json('POST', '/api/v1/users/register', $post)
                     ->seeJson([
                         'status' => false,
                         "errors" => [["username" => ["The username has already been taken."]]]
                     ]);
    }

    /**
     * [testUserRegistration description]
     * @return [type] [description]
     */
    public function testUserRegistrationRequiredFields()
    {
        
        $post = [
            'username' => "randomUsername",
            'first_name' => "firtsName",
            'last_name' => "lastName",
            'password' => "lastName",
        ];

        $this->json('POST', '/api/v1/users/register', $post)
                     ->seeJson([
                          'status' => false,
                          'errors' => [["email" => ["The email field is required."]]]
                     ]);

    }

    /**
     * [testUserRegistration description]
     * @return [type] [description]
     */
    public function testUserLoginRequiredPasswordField()
    {
        
        $post = ['login' => "randomUsername"];

        $this->json('POST', '/api/v1/users/login', $post)
                     ->seeJson([
                         'status' => false,
                         'errors' => [["password" => ["The password field is required."]]]
                     ]);
    }

    public function testUserLoginRequiredLoginField()
    {
        
        $post = ['login' => "randomUsername"];

        $this->json('POST', '/api/v1/users/login', $post)
                     ->seeJson([
                         'status' => false,
                         'errors' => [["password" => ["The password field is required."]]]
                     ]);
    }


    /**
     * Test api for user login
     */
    public function testUserLoginWithUsername()
    {
        // Create a new user and override password 
        // so we can user to test login
        $user = factory(App\User::class)->create(['password' => bcrypt('!@£^&*password!@££$%%^&&')]);
        
        $post = [
            'login' => $user->username,
            'password' => '!@£^&*password!@££$%%^&&',
        ];
        $this->json('POST', '/api/v1/users/login', $post)
                     ->seeJsonEquals([
                         'status' => true,
                         'status_code' => 200
                     ]);
        
    }

    /**
     * Test api for user login with email
     */
    public function testUserLoginWithEmail()
    {
        // Create a new user and override password 
        // so we can user to test login
        $user = factory(App\User::class)->create(['password' => bcrypt('!@£^&*password!@££$%%^&&')]);
        
        $post = [
            'login' => $user->email,
            'password' => '!@£^&*password!@££$%%^&&',
        ];
        $this->json('POST', '/api/v1/users/login', $post)
                     ->seeJsonEquals([
                         'status' => true,
                         'status_code' => 200
                     ]);
        
    }


}
