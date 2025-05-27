<?php

namespace App\Http\Controllers\Api;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $services = Service::all();
        if($services->count()>0)
        {
            return response()->json([
                'status'=>200,
                'services'=>$services,
            ],200);
        }
        else{
            return response()->json([
                'status'=>404,
                'message'=>'No Services Found'
            ],404);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(),[
            'category_id'=>'required|exists:categories,id',
            'name'=>'required|string|max:265',
            'description'=>'required|string|max:265',
            'status' => 'in:active,notactive'
        ]);
        if($validator->fails())
        {
            return response()->json([
                'status'=>422,
                'message'=>$validator->messages(),
            ],422);
        }
        else{
            $service = Service::create([
                'user_id'=>Auth::user()->id,
                'category_id'=>$request->category_id,
                'name'=>$request->name,
                'description'=>$request->description,
                'status' =>$request->status ?? 'notactive'
            ]);
            if($service){
                return response()->json([
                    'status'=>200,
                    'message'=>"Service Added Successfully",
                    'data' =>$service
                ],200);
            }
            else{
                return response()->json([
                    'status'=>500,
                    'message'=>"Something Went Wrong!",
                ],status: 500);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $service = Service::find($id);
        if($service){
            return response()->json([
                'status'=>200,
                'service'=>$service,
            ],200);
        }
        else{
            return response()->json([
                'status'=>404,
                'message'=>"service not found",
            ],404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $validator = Validator::make($request->all(),[
            'category_id'=>'required|exists:categories,id',
            'name'=>'required|string|max:265',
            'description'=>'required|string|max:265',
             'status' =>$request->status ?? 'active'
        ]);
        if($validator->fails())
        {
            return response()->json([
                'status'=>422,
                'message'=>$validator->messages(),
            ],422);
        }
        else{
            $service = Service::find($id);
            if($service){
                $service->update([
                    'category_id'=>$request->category_id,
                    'name'=>$request->name,
                    'description'=>$request->description,
                    'status' =>$request->status 
                ]);
                return response()->json([
                    'status'=>200,
                    'message'=>"Service updated Successfully",
                ],200);
            }
            else{
                return response()->json([
                    'status'=>500,
                    'message'=>"No such service id found!",
                ],status: 500);
            }
        }
    
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $service = Service::find($id);
        if ($service){
          $service->delete();
          return response()->json([
              'status'=>200,
              'message'=>"service Deleted Successfully",
          ],200);
        }
         else{
          return response()->json([
              'status'=>404,
              'message'=>"No Such service Found",
          ],404);
         };
    }
}
