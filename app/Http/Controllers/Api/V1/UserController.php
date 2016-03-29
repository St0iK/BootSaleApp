<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use Auth;
use Storage;
use Input;
use App\User;
use AWS;
use Uuid;
use Image;

class UserController extends Controller
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
        $this->validate($request, [
            'login' => 'required', 
            'password' => 'required',
        ]);
    }

    /**
     * Validate the user register request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateRegister(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'profile_pic' => 'required',
            'first_name' => 'required',
            'last_name' => 'required'
        ]);
    }

    /**
     * Authenticate a user with login(username/email)
     *
     * @return Response
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);
      
        $status = "error";
        $message = "User could not be authenticated";
         
        $credentials = $this->getLoginCredentials($request);
        if (Auth::attempt($credentials)) 
        {
           $status = "success";
           $message = "User authenticated using username";
        }
       
        return [
            'status' => $status, 
            'message' => $message
        ];
    }

    /**
     * Register a new user
     *
     * @return Response
     */
    public function register(Request $request)
    {
        $this->validateRegister($request);
        $s3 = AWS::createClient('s3');    
        $data = $this->getRegisterCredentials($request);
        
        // Check if image is valid
        if (!$data['profile_pic']->isValid()) {
            
          return [
              'status' => "error",
              'message' => "The image you are trying to upload is not valid",
          ];

        }

        // Save image localy 
        $image = Uuid::generate() . '.' . $data['profile_pic']->getClientOriginalExtension();
        $path = '/tmp/' . Uuid::generate() . '.' . $data['profile_pic']->getClientOriginalExtension();
        Image::make($data['profile_pic']->getRealPath())->resize(200, 200)->save($path);

        // Upload normal image && thumb
        $s3_upload = $s3->putObject(array(
            'Bucket'     => '7480683303',
            'Key'        => "users/".$image,
            'SourceFile' => $data['profile_pic']->getRealPath(),
        ));

        $s3_thumb_upload = $s3->putObject(array(
            'Bucket'     => '7480683303',
            'Key'        => "users/thumbs/".$image,
            'SourceFile' => $path,
        ));

        // Image was successfully uploaded to S3
        // We are deleting it locally
        if($s3_upload['ObjectURL'] && $s3_thumb_upload['ObjectURL'])
        {
            return User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'username' => $data['username'],
                'profile_pic' => "users/".$image,
                'password' => bcrypt($data['password']),
            ]);

        }        

    }

}
