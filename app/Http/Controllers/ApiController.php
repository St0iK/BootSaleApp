<?php
namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Routing\ResponseFactory;

class ApiController extends Controller{
	
	public $response;
	
	/**
	 * @param  Illuminate\Routing\ResponseFactory  $response
	 */
	public function __construct(ResponseFactory $response)
	{
		$this->response = $response;
	}

	/**
	 * Generic JSON formatted response
	 * 
	 * @param  $data array
	 * @param  $responase_message
	 * @param  $status_codeinteger
	 * @return JSON object
	 */
	public function respond($data, $response_message = NULL, $status_code = 200) 
	{
		 $response['status'] = true;
		 if (isset($data)){
		 	$response['data'] = $data;	
		 }
		 
		 if (isset($response_message)){
		 	$response['message'] = $response_message;	
		 }
		 
		 if (isset($status_code)){
		 	$response['status_code'] = $status_code;
		 }
		 	
		 return $this->response->json($response, $status_code);
	 }

	 /**
	  * Generic JSON response on errors
	  * 
	  * @param  $errors array, containing the errors
	  * @param  $error_code integer
	  * @param  $status_code integer
	  * @return JSON object
	  */
	 public function respondWithError($errors, $error_code, $status_code = 400) 
	 {
	 	 $response['status'] = false;
	 	 if (isset($errors)){
	 	 	$response['errors'] = $errors;	
	 	 }
	 	 
	 	 if (isset($error_code)){
	 	 	$response['error_code'] = $error_code;	
	 	 }
	 	 
	 	 if (isset($status_code)){
	 	 	$response['status_code'] = $status_code;
	 	 }
	 	 	
	 	 return $this->response->json($response, $status_code);
	  }

	  /**
	   * Respose for validaton errors
	   * 
	   * @param  $message string
	   * @param  $validationErrors array
	   * @param  $status_code integer
	   * @return [type]
	   */
	  public function respondWithValidationErrors($message, $validationErrors, $status_code = 400) 
	  {
		  $message = [
			  'status' => false,
			  'message' => isset($message) ? $message : null,
			  'errors' => [$validationErrors]
		  ];

	  	  return $this->response->json($message, $status_code);
	  }


	public function respondCreated($data = null , $message = 'Resource created') {
		return $this->respond($data, $message, 201);
	}

	public function respondUnauthorized( $error_code, $message = 'You are not authorized for this') {
		return $this->respondWithError($message, $error_code, 401);
	}

	public function respondNotFound( $error_code, $message = 'Resource not found') {
		return $this->respondWithError($message, $error_code, 404);
	}

	public function respondInternalError( $error_code, $message = 'Internal error') {
		return $this->respondWithError($message, $error_code, 500);
	}

	public function respondOk( $message = 'Done') {
		return $this->respond(null, $message, 200);
	}
}