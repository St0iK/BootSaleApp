<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Authorizer;
use Jenssegers\Agent\Agent;
use App\UserInformation;


class AccessTokenController extends Controller
{

    /**
     * [generateToken description]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    protected function generateToken(Request $request)
    {
        $this->storeUserInformation($request);
        // Generate access token and return
        return Authorizer::issueAccessToken();
    }

    /**
     * [userInformationData description]
     * @param  [type] $request [description]
     * @return [type]          [description]
     */
    private function storeUserInformation($request)
    {
        // Log user information
        $request_data = $request->only('user_id');
        if($request_data['user_id'])
        {
            UserInformation::create($this->userInformationData($request_data['user_id'], $request->ip()));
        }    
    }
    
    /**
     * [userInformationData description]
     * @return [type] [description]
     */
    private function userInformationData($user_id,$ip)
    {
        $agent = new Agent();
        return [
                'user_id' => $user_id,
                'ip_address' => $ip,
                'os' => $agent->platform(),
                'version' => $agent->device(),
                'user_agent' =>  $_SERVER['HTTP_USER_AGENT']
            ];
    }
}
