<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\AdBid;

class AdBidController extends Controller
{
    /**
     * Get the data we need from the request
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function getBidInfo(Request $request)
    {
        $return = $request->only('user_id', 'ad_id', 'amount');
        return $return;
    }

    /**
     * Validate the the bid information
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateBid(Request $request)
    {
        $this->validate($request, [
            'ad_id' => 'required|integer|exists:ads,id', 
            'user_id' => 'required|integer|exists:users,id',
            'amount' => 'required|numeric',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->validateBid($request);
        $bid = $this->getBidInfo($request);
        return AdBid::create($bid);
    }

}
