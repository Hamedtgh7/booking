<?php

namespace App\Traits;

use Symfony\Component\HttpFoundation\Response;

trait ApiResponse
{
    protected function successResponse($message='',$data=[],$statusCode=Response::HTTP_OK)
    {
        return response()->json([
            'status'=>'success',
            'message'=>$message,
            'data'=>$data
        ],$statusCode);
    }

    protected function errorResponse($message='',$errors=[],$statusCode=Response::HTTP_BAD_REQUEST)
    {
        return response()->json([
            'status'=>'error',
            'message'=>$message,
            'errors'=>$errors
        ],$statusCode);
    }
}