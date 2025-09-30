<?php

namespace App\Http\Controllers;

abstract class Controller
{
    protected function sendResponse($message , $data = null , $code = 200)
    {
        return response()->json([

            'message' => $message ,
            'data' => $data ,
            'code' => $code ,

        ] , $code);
    }

}
