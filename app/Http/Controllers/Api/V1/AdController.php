<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Ad;
use App\AdPhoto;
use AWS;
use Uuid;
use Image;

class AdController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $validator = $this->validate($request, [
                        'from_latitude' => 'required',
                        'from_longitude' => 'required',
                    ]);

        if ($validator) {
            $this->throwValidationException(
                $request, $validator
            );
        }

        $from_latitude = $request->input('from_latitude');
        $from_longitude = $request->input('from_longitude');
        $category_id = $request->input('category_id');
        
        if(!empty($category_id)){
            $ads = Ad::with(['countComments','countBids','user','photos'])->olomalakies($from_latitude,$from_longitude)->where('category_id', 2 )->paginate(10);
        }else{
            $ads = Ad::with(['countComments','countBids','user','photos'])->olomalakies($from_latitude,$from_longitude)->paginate(10);    
        }
        
        return $ads;
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
    public function validateData($request)
    {
        $this->validate($request, [
            'user_id' => 'required',
            'title' => 'required',
            'description' => 'required',
            'category_id' => 'required',
            'price' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'currency_code' => 'required'
        ]);
    }

    /**
     * Get the needed registration data from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function getAdCredentials(Request $request)
    {
        return $request->only('title','description','category_id','price','latitude','longitude','currency_code');
    }
    protected function getPhotoCredentials(Request $request)
    {
        return $request->only('image_1','image_2','image_3','image_4','image_5');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateData($request);
        $s3 = AWS::createClient('s3'); 
        $data = $this->getAdCredentials($request);
        $images = $this->getPhotoCredentials($request);

        $ad = Ad::create($data);

        foreach ($images as $imageData) 
        {
            if($imageData)
            {
                // Save image localy 
                $image = Uuid::generate() . '.' . $imageData->getClientOriginalExtension();
                $path = '/tmp/' . Uuid::generate() . '.' . $imageData->getClientOriginalExtension();
                Image::make($imageData->getRealPath())->resize(200, 200)->save($path);

                // Upload normal image && thumb
                $s3_upload = $s3->putObject(array(
                    'Bucket'     => '7480683303',
                    'Key'        => "ads/".$image,
                    'SourceFile' => $imageData->getRealPath(),
                ));

                $s3_thumb_upload = $s3->putObject(array(
                    'Bucket'     => '7480683303',
                    'Key'        => "ads/thumbs/".$image,
                    'SourceFile' => $path,
                ));

                $data = [
                    'path' => "ads/".$image,
                    'thumb_path' => "ads/thumbs/".$image,
                ];

                if($s3_upload && $s3_thumb_upload){
                    // $adPhoto = AdPhoto::create($data);  
                    $photo = new AdPhoto($data);
                    $photo->ad()->associate($ad); 
                    $photo->save();
                }
                
            }
        }

        return $ad;
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
