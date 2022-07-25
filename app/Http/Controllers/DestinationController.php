<?php

namespace App\Http\Controllers;

use App\Http\Resources\DestinationResources;
use App\User;
use App\UserDestination;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class DestinationController extends Controller
{
    /**
     * @OA\Get (
     *     path="/api/destination/get",
     *     operationId="get user destination",
     *     tags={"Destination"},
     *     @OA\Response(
     *         response="200",
     *         description="destination created",
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

    public function index(Request $request){
        $user = Auth::user();

//
//        if(Cache::get($key)){
//            $destinations = Cache::get($key);
//        }else{
//            $destinations = UserDestination::where('user_id',$user->id)->get();
//            foreach ($destinations as $item){
//
//            }
//            Cache::put($key, $destinations, 1800);
//        }
        $destinations = UserDestination::where('user_id',$user->id)->get();
        $data = array();
        foreach ($destinations as $item){
            $key = "__destination".$item->id;
            if(Cache::get($key)){
                $data[] = Cache::get($key);
            }else{
                $destinations = UserDestination::where('id',$item->id)->first();
                Cache::put($key, $destinations->toArray(), 1800);
                $data[] = $destinations;
            }

        }

        $my_destination = DestinationResources::collection($destinations);
        return $this->successResponse($my_destination,'my destination list',200);

    }
    /**
     * @OA\Post(
     *     path="/api/destination/create",
     *     operationId="user destination",
     *     tags={"Destination"},
     *     @OA\RequestBody(
     *         description="Input data format",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="title",
     *                     description="title destination",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="origin",
     *                     description="The origin of the destination",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="destination",
     *                     description="The journey of the destination",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="type",
     *                     description="type of the destination",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="start",
     *                     description="start date of the destination",
     *                     type="datetime"
     *                 ),
     *                 @OA\Property(
     *                     property="end",
     *                     description="end date of the destination",
     *                     type="datetime",
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     description="description of the destination",
     *                     type="string"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="destination created",
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
    public function create(Request $request){

        $validation = Validator::make($request->all(), [
            'title'        => 'required',
            'origin'       => 'required|date_format:Y-m-d H:i:s',
            'destination'  => 'required|date_format:Y-m-d H:i:s',

        ]);

        if ($validation->fails()) {
            return $this->errorResponse( $validation->messages(),201);
        }
        $user = Auth::user();

        $start_time = $request->start;
        $end_time    = $request->end;
        $get_destination = UserDestination::where('user_id',$user->id)->where(function ($query) use ($start_time, $end_time) {
            $query->where(function ($query) use ($start_time, $end_time) {
                $query->where('start', '>=', $start_time)
                    ->where('end', '<', $start_time);
            })
                ->orWhere(function ($query) use ($start_time, $end_time) {
                    $query->where('start', '<', $end_time)
                        ->where('end', '>=', $end_time);
                });
        })->count();

        if($get_destination){
            return $this->errorResponse('date already taken on another destination',202);
        }


        $user_destination = UserDestination::create([
            'user_id'       =>$user->id,
            'title'         =>$request->title,
            'origin'        =>$request->origin,
            'destination'   =>$request->destination,
            'type'          =>$request->type,
            'start'         =>$request->start,
            'end'           =>$request->end,
            'description'   =>$request->description,
            'created_at'    =>Carbon::now(),
        ]);
        $key = "__destination".$user_destination->id;
//        Cache::forget($key);
//        $get_destination = UserDestination::where('user_id',$user->id)->get();

        Cache::put($key, $user_destination, 1800);

        return $this->successResponse($user_destination,'destination created',200);
    }

    /**
     * @OA\Post(
     *     path="/api/destination/delete",
     *     operationId="user cancel destination",
     *     tags={"Destination"},
     *     @OA\RequestBody(
     *         description="Input data format",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="destination_id",
     *                     description="id destination",
     *                     type="integer",
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="destination created",
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

    public function delete(Request $request){

        $user = Auth::user();
        $destination = UserDestination::where('id',$request->destination_id)->first();
        if(!$destination){
            return $this->errorResponse('destination not found',203);
        }
        $key = "__destination".$destination->id;
        Cache::forget($key);
        $destination->delete();
        return $this->successResponse($request->id);

    }


}
