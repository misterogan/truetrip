<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Trutrip API Documentations",
 *      description="",
 *      @OA\Contact(
 *          email="djersey18@gmail.com"
 *      ),
 *      @OA\License(
 *          name="Apache 2.0",
 *          url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *      )
 * )

 *
 * @OA\Tag(
 *     name="Truetrip API",
 *     description="API Endpoints of Truetrip"
 * )


 * @OA\SecurityScheme(
 *    securityScheme="bearer_token",
 *    in="header",
 *    name="Authorization",
 *    type="apiKey",
 *    scheme="bearer",
 *    bearerFormat="JWT",
 * )
 */


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function successResponse($data = null, $message = "success", $code = 200)
    {
        return response()->json([
            'status'=> 'success',
            'message' => $message,
            'code' => $code,
            'data' => $data
        ], $code);
    }

    protected function errorResponse($message, $code)
    {
        return response()->json([
            'status'=>'error',
            'message' => $message,
            'code' => $code,
            'data' => null
        ], 400);
    }
}
