<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index():JsonResponse
    {
        $category = Category::with('services')->get();
        if($category->count()>0){
            return response()->json([
                'status'=>200,
                'category'=>$category,
            ],200);
        }
        else{
            return response()->json([
                'status'=>404,
                'message'=>'No Categories Found',
            ],404);
        }
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request):JsonResponse
    {
        $validator = Validator::make($request->all(),[
            'name'=>'required|string|max:266|unique:categories,name',
        ]);
        if($validator->fails()){
            return response()->json([
                'status'=>422,
                'message'=>$validator->messages(),
            ],422);
        }
        else{
           $category= Category::create([
                'name'=>$request->name,
            ]);
            if($category){
                return response()->json([
                    'status'=>200,
                    'message'=>"Category Created Successfully",
                ],200);
            }
            else{
                return response()->json([
                    'status'=>500,
                    'message'=>"Something Went Wrong!",
                ],500);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $category = Category::find($id);
        if($category){
            return response()->json([
                'status'=>200,
                'category'=>$category,
            ],200);
        }
        else{
            return response()->json([
                'status'=>404,
                'message'=>'No Such Category Found!',
            ],404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(),[
            'name'=>'required|string|max:266',
        ]);
        if($validator->fails()){
            return response()->json([
                'status'=>422,
                'message'=>$validator->messages(),
            ],422);
        }
        else{
            $category = Category::find($id);
          if ($category){
            $category->update([
                'name'=>$request->name,
            ]);
            return response()->json([
                'status'=>200,
                'message'=>"Category Updated Successfully",
            ],200);
          }
           else{
            return response()->json([
                'status'=>404,
                'message'=>"No Such Category Found",
            ],404);
           };
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $category = Category::find($id);
        if ($category){
          $category->delete();
          return response()->json([
              'status'=>200,
              'message'=>"Category Deleted Successfully",
          ],200);
        }
         else{
          return response()->json([
              'status'=>404,
              'message'=>"No Such Category Found",
          ],404);
         };
    }
}
