<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LocationController extends Controller
{
    public function store(Request $request)
    {
        try {
            $request->validate([
                'longitude' => 'required',
                'latitude' => 'required',
            ]);
            $location = Location::create([
                'longitude' => $request->longitude,
                'latitude' => $request->latitude,
                'user_id' => Auth::user()->id
            ]);
            if ($location) {
                return response()->json([
                    'status' => 200,
                    'message' => "Location Added Successfully",
                    'data' => $location,
                ], 200);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        } catch (ValidationException $validationException) {
            return response()->json([
                'status' => 'error',
                'message' => $validationException->errors(),
            ], 400);
        }
    }

    public function update(Request $request,$id){
        try{
            $request->validate([
                'longitude' => 'required',
                'latitude' => 'required',
            ]);
            $location = Location::find($id);
            
            if ($location) {
                $location->update([
                    'longitude' => $request->longitude,
                    'latitude' => $request->latitude,
                ]);
                return response()->json([
                    'status' => 200,
                    'message' => "Location Updated Successfully",
                    'data' => $location,
                ], 200);
            }
            else{
                return response()->json([
                    'status' => 404,
                    'message' => "No Such Location Found",
                ], 404);
            }

        }
        catch(Exception $e){
                return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
        catch (ValidationException $validationException) {
            return response()->json([
                'status' => 'error',
                'message' => $validationException->errors(),
            ], 400);
        }

    }
    public function destroy ($id){
        $location = Location::find($id);
        if ($location) {
            $location->delete();
            return response()->json([
                'status' => 200,
                'message' => "Location Deleted Successfully",
            ], 200);
        }
        else{
            return response()->json([
                'status' => 404,
                'message' => "No Such Location Found",
            ], 404);
        }
    }
}
