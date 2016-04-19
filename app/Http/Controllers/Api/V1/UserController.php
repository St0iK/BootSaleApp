<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;
use Validator;
use Auth;
use Storage;
use Input;
use App\User;
use AWS;
use Uuid;
use Image;

class UserController extends ApiController
{

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function getLoginCredentials(Request $request)
    {
        $return = $request->only('login', 'password');
        $return['username'] = $return['login'];
        
        // Update array keys to email if necessary
        if(filter_var( $return['username'], FILTER_VALIDATE_EMAIL ))
        {
            unset($return['username']);
            $return['email'] = $return['login'];
        }
        // Unset login from the array
        // as we dong need it anymore
        unset($return['login']);
        return $return;
    }

    /**
     * Get the needed registration data from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function getRegisterCredentials(Request $request)
    {
        return $request->only('username','email','password','profile_pic','first_name','last_name');
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateLogin(Request $request)
    {
        // we use a custom laravel validator 
        // to grab the error messages and feed the json response
        $validator = Validator::make($request->all(), [
            'login' => 'required', 
            'password' => 'required',
        ]);

        return $validator;
    }

    /**
     * Validate the user register request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateRegister(Request $request)
    {
        // we use a custom laravel validator 
        // to grab the error messages and feed the json response
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'first_name' => 'required',
            'last_name' => 'required'
        ]);

        return $validator;
    }

    /**
     * @api {post} /users/login Login user
     * @apiName AuthenticateUser
     * @apiGroup User
     *
     * @apiParam {String} login Users username or email address
     *
     * @apiSuccess {Boolean} status Flag true/false
     * @apiSuccess {Number} status_code Status Code
     * @apiSuccessExample {json} Success-Response:
     *            {"status":true,"status_code":200}  
     *
     * @apiError AuthenticationFailedError When the provided credentials are wrong
     * @apiError ValidationFailed Missing required fields on post
     * @apiErrorExample AuthenticationFailedError {json} Error-Response:
     *     HTTP/1.1 400 Bad Request
     *   {
     *     "status": false,
     *     "errors": "User not authenticated",
     *     "error_code": "js_11921",
     *     "status_code": 400
     *    }
     * @apiErrorExample ValidationFailed {json} Error-Response:
     *     HTTP/1.1 400 Bad Request
     *    {
     *      "status": false,
     *      "message": "message",
     *      "errors": [
     *        {
     *          "login": [
     *            "The login field is required."
     *          ],
     *          "password": [
     *            "The password field is required."
     *          ]
     *        }
     *      ]
     *    }
     
     */
    

    public function login(Request $request)
    {
        // Validate login request
        $loginValidator = $this->validateLogin($request);
        if ($loginValidator->fails()) {
            $message = "Please check your form";
            $errors = $loginValidator->errors();
            return $this->respondWithValidationErrors('message', $errors);
        }

        $credentials = $this->getLoginCredentials($request);

        // Try to authenticate the user
        // with the credentials provided
        if (Auth::attempt($credentials)) 
        {
            return $this->respondOk('User authenticated successfully');
        }

        return $this->respondWithError('User not authenticated', 'js_11921');
       
        
    }

    /**
     * @api {post} /users/register Register user
     * @apiName RegisterUser
     * @apiGroup User
     *
     * @apiParam {String} username User's username
     * @apiParam {String} email User's e-mail address
     * @apiParam {String} first_name User's first name
     * @apiParam {String} last_name User's last name
     * @apiParam {String} password User's password
     *
     * @apiSuccess {Boolean} status Flag true/false
     * @apiSuccess {Number} status_code Status Code
     * @apiSuccessExample {json} Success-Response:
     *      {
     *         "status": true,
     *         "data": "User created successfully",
     *         "status_code": 200
     *       }  
     *
     * @apiError ValidationFailed Missing required fields on post
     * @apiErrorExample ValidationFailed {json} Error-Response:
     * {
     *   "status": false,
     *   "message": "message",
     *      "errors": [
     *        {
     *          "username": [
     *            "The username has already been taken."
     *          ],
     *          "email": [
     *            "The email has already been taken."
     *          ]
     *        }
     *      ]
     *    }
     
     */
    public function register(Request $request)
    {
        // Validate register request
        $registerValidator = $this->validateRegister($request);
        if ($registerValidator->fails()) {
            $message = "Please check your form";
            $errors = $registerValidator->errors();
            return $this->respondWithValidationErrors('message', $errors);
        }

        $data = $this->getRegisterCredentials($request);
        
        // Save the new user  
        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'username' => $data['username'],
            'password' => bcrypt($data['password']),
        ]);
        
        return $this->respondCreated($user);

    }

}
