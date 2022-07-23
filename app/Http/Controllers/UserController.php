<?php

namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class UserController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/auth/user/register",
     *     operationId="user register",
     *     tags={"User"},
     *     @OA\RequestBody(
     *         description="Input data format",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="name",
     *                     description="The name of the user",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     description="The email of the user",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     description="The password of the user",
     *                     type="string"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="User register success",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Error: Bad request. When required parameters were not supplied.",
     *     ),
     * )
     * )
     */
    public function register(Request $request){

        $validation = Validator::make($request->all(), [
            'name'        => 'required',
            'email'       => 'required|email|unique:users',
            'password'    => 'required|min:6',
        ]);

        if ($validation->fails()) {
            return $this->errorResponse( $validation->messages(),201);
        }
        $user = User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'password'      => Hash::make($request->password),
            'created_at'    => Carbon::now(),
        ]);

        return $this->successResponse($user,'Register success',200);
    }


    /**
     * @OA\Post(
     *     path="/api/auth/user/login",
     *     operationId="user login",
     *     tags={"User"},
     *     @OA\Parameter(
     *          name="email",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter(
     *          name="password",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="User login success",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Error: Bad request. When required parameters were not supplied.",
     *     ),
     * )
     * )
     */

    public function login(Request $request){

        $validation = Validator::make($request->all(), [
            'email'       => 'required|email',
            'password'    => 'required|min:6',
        ]);

        if ($validation->fails()) {
            return $this->errorResponse( $validation->messages(),201);
        }


        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){

            $user = Auth::user();
            $response['token'] = 'Bearer ' . $user->createToken('usertruetripz')->accessToken;
            return $this->successResponse($response,'login success',200);

        }else{
            return $this->errorResponse( 'Unauthorized',401);
        }
    }



    /**
     * @OA\Get(
     *     path="/api/auth/user",
     *     operationId="get user",
     *     tags={"User"},
     *     @OA\Response(
     *         response="200",
     *         description="Token success",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Error: Bad request. When required parameters were not supplied.",
     *     ),
     *     security={{"bearer_token":{}}}
     * )
     * )
     */

    public function get_user()
    {
        $user = Auth::user();
        $user = $user->makeHidden(['email_verified_at','password','remember_token']);
        return $this->successResponse($user);
    }

    /**
     * @OA\Get(
     *     path="/api/auth/user/logout",
     *     operationId="logout user",
     *     tags={"User"},
     *     @OA\Response(
     *         response="200",
     *         description="Token success",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Error: Bad request. When required parameters were not supplied.",
     *     ),
     *     security={{"bearer_token":{}}}
     * )
     * )
     */
    public function logout(Request $request){

        $request->user()->token()->revoke();
        return $this->successResponse();

    }


}
