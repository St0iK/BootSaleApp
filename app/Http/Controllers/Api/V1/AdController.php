<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;
use App\Ad;
use App\AdPhoto;
use AWS;
use Uuid;
use Image;
use Validator;

class AdController extends ApiController
{

    /**
     * @api {get} /ads?from_latitude=123123&from_latitude=13123 Get listings
     * @apiName GetListings
     * @apiGroup Listings
     *
     * @apiParam {String} from_latitude User's location latitude
     * @apiParam {String} from_longitude User's location longitude
     *
     * @apiSuccess {Boolean} status Flag true/false
     * @apiSuccess {Number} status_code Status Code
     * @apiSuccess {Number} data Contains the ads
     * @apiSuccessExample {json} Success-Response:
     {
       "status": true,
       "data": {
         "total": 1,
         "per_page": 10,
         "current_page": 1,
         "last_page": 1,
         "next_page_url": null,
         "prev_page_url": null,
         "from": 1,
         "to": 1,
         "data": [
           {
             "id": 1,
             "user_id": 1,
             "title": "title",
             "description": "description",
             "category_id": 1,
             "status": 0,
             "price": "20.00",
             "latitude": "1.00",
             "longitude": "1.00",
             "currency_code": "EUR",
             "created_at": "2016-04-19 22:13:48",
             "updated_at": "2016-04-19 22:13:48",
             "distance": 8045,
             "total_comments": 0,
             "total_bids": 0,
             "user": {
               "id": 1,
               "username": "atque",
               "first_name": "Alicia Rippin",
               "last_name": "Ortiz",
               "display_name": "",
               "email": "nicolas.dolores@haag.biz",
               "telephone": "",
               "profile_pic": "",
               "status": 0,
               "created_at": "2016-04-19 20:32:16",
               "updated_at": "2016-04-19 20:32:16"
             },
             "photos": []
           }
         ]
       },
       "status_code": 200
     }
     *
     * @apiError ValidationFailed Missing required fields on post
     * @apiErrorExample RequiredFieldsError {json} Error-Response:
     *     HTTP/1.1 400 Bad Request
     *   {
     *     "status": false,
     *     "errors": "Wrong request. Please include all required data",
     *     "error_code": "js_11922",
     *     "status_code": 400
     *    }     
     */
    
    public function index(Request $request)
    {
        // Make sure request contains latitude and longitude
        $validateRequest = $this->validateGetListingsRequest($request);
        if ($validateRequest->fails()) {
            $message = 'Wrong request. Please include all required data';
            return $this->respondWithError($message, 'js_11922');
        }
        $from_latitude = $request->input('from_latitude');
        $from_longitude = $request->input('from_longitude');
        $category_id = $request->input('category_id');
        
        return  $this->respond($this->getAds($category_id, $from_latitude, $from_longitude));
    
    }

    /**
     * [getAds description]
     * @param  [type] $category_id [description]
     * @return [type]              [description]
     */
    private function getAds($category_id = NULL, $from_latitude, $from_longitude)
    {
        if(!empty($category_id)){
            // return ads for this category
            return  Ad::with(['countComments','countBids','user','photos'])
            ->withindistance($from_latitude,$from_longitude)
            ->where('category_id', $category_id )
            ->paginate(10);
        }

        // Return all ads
        return Ad::with(['countComments','countBids','user','photos'])
        ->withindistance($from_latitude,$from_longitude)->paginate(10);            
    }

    /**
     * [validateGetListingsRequest description]
     * @return [type] [description]
     */
    private function validateGetListingsRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_latitude' => 'required',
            'from_longitude' => 'required',
        ]);

        return $validator;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * [validateData description]
     * @param  [type] $request [description]
     * @return [type]          [description]
     */
    public function validatePostData($request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'title' => 'required',
            'description' => 'required',
            'category_id' => 'required',
            'price' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'currency_code' => 'required'
        ]);
        
        return $validator;
    }

    /**
     * Get the needed registration data from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function getAdCredentials(Request $request)
    {
        return $request->only('user_id','title','description','category_id','price','latitude','longitude','currency_code');
    }
    protected function getPhotoCredentials(Request $request)
    {
        return $request->only('image_1','image_2','image_3','image_4','image_5');
    }

    /**
     * @api {post} /ads Create a new listing
     * @apiName AddListing
     * @apiGroup Listings
     *
     * @apiParam {String} user_id The id of the user that creates the listing
     * @apiParam {String} title The title of the listing ex. Macbook Pro 2013
     * @apiParam {String} description The title of the listing ex. Macbook Pro 2013
     * @apiParam {String} category_id The id of the category the listing belongs
     * @apiParam {String} price Suggested price
     * @apiParam {String} latitude Location informatio
     * @apiParam {String} longitude Location informatio
     * @apiParam {String} currency_code Currency code of the price
     *
     * @apiSuccess {Boolean} status Flag true
     * @apiSuccess {Number} status_code 200
     * @apiSuccess {Number} data Listing information
     * @apiSuccessExample {json} Success-Response:
     * {
          "status": true,
          "data": {
            "user_id": "1",
            "title": "title",
            "description": "description",
            "category_id": "1",
            "price": "20",
            "latitude": "1",
            "longitude": "1",
            "currency_code": "EUR",
            "updated_at": "2016-04-19 22:53:15",
            "created_at": "2016-04-19 22:53:15",
            "id": 2,
            "total_comments": 0,
            "total_bids": 0
          },
          "message": "Resource created",
          "status_code": 201
       }
     *
     * @apiError ValidationFailed Missing required fields on post
     * @apiErrorExample RequiredFieldsError {json} Error-Response:
     *     HTTP/1.1 400 Bad Request
     {
       "status": false,
       "message": "Please check your post, required fields are missing.",
       "errors": [
         {
           "user_id": [
             "The user id field is required."
           ],
           "title": [
             "The title field is required."
           ],
           "description": [
             "The description field is required."
           ],
           "category_id": [
             "The category id field is required."
           ]
         }
       ]
     }     
     */
    public function store(Request $request)
    {
        // Validate request
        $validatePostData = $this->validatePostData($request);
        if ($validatePostData->fails()) {
            $message = "Please check your post, required fields are missing.";
            $errors = $validatePostData->errors();
            return $this->respondWithValidationErrors($message, $errors);
        }
         
        $data = $this->getAdCredentials($request);
        $images = $this->getPhotoCredentials($request);

        $ad = Ad::create($data);
        $this->storeImages($ad, $images);
        return $this->respondCreated(Ad::with(['photos'])->find($ad['id']));
    }

    /**
     * [storeImages description]
     * @param  [type] $ad     [description]
     * @param  [type] $images [description]
     * @return [type]         [description]
     */
    private function storeImages($ad, $images)
    {
        foreach ($images as $imageData) 
        {
            $this->saveImage($ad, $imageData);
        }
    }

    /**
     * [saveImage description]
     * @param  [type] $ad        [description]
     * @param  [type] $imageData [description]
     * @return [type]            [description]
     */
    private function saveImage($ad, $imageData)
    {
        if(!$imageData) return;

        // Save image localy 
        $image = Uuid::generate() . '.' . $imageData->getClientOriginalExtension();
        $path = '/tmp/' . Uuid::generate() . '.' . $imageData->getClientOriginalExtension();
        Image::make($imageData->getRealPath())->resize(200, 200)->save($path);

        // Upload files to aws
        $s3_upload = $this->awsFileUpload("ads/".$image, $imageData->getRealPath());
        $s3_thumb_upload = $this->awsFileUpload("ads/thumbs/".$image, $path);

        $data = [
            'path' => "ads/".$image,
            'thumb_path' => "ads/thumbs/".$image,
        ];

        // Save photo to database and assosiate with ad
        if($s3_upload && $s3_thumb_upload){
            $photo = new AdPhoto($data);
            $photo->ad()->associate($ad); 
            $photo->save();
        }
    }

    /**
     * [awsFileUpload description]
     * @param  [type] $key        [description]
     * @param  [type] $sourceFile [description]
     * @return [type]             [description]
     */
    private function awsFileUpload($key, $sourceFile)
    {
        $s3 = AWS::createClient('s3');
        return $s3->putObject(array(
            'Bucket'     => \Config::get('aws.bucket'),
            'Key'        => $key,
            'SourceFile' => $sourceFile,
        ));
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Ad::with(['bids.user','comments.user','photos','user'])->find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
