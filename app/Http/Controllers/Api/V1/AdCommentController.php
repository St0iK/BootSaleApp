<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\AdComment;

class AdCommentController extends Controller
{
    /**
     * Get the data we need from the request
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function getCommentInfo(Request $request)
    {
        $return = $request->only('user_id', 'ad_id', 'comment');
        return $return;
    }

    /**
     * Validate the the comment information
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateComment(Request $request)
    {
        $this->validate($request, [
            'ad_id' => 'required|integer|exists:ads,id', 
            'user_id' => 'required|integer|exists:users,id',
            'comment' => 'required',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->validateComment($request);
        $bid = $this->getCommentInfo($request);
        return AdComment::create($bid);
    }
}
