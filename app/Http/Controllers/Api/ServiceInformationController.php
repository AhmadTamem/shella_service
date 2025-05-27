<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\ServiceInformation;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ServiceInformationController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(): JsonResponse
    {
        $serviceInformation = ServiceInformation::with('service')->get();
        if ($serviceInformation->count() > 0) {
            return response()->json([
                'status' => 200,
                'service information' => $serviceInformation,
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => "not Services Information found",
            ], 404);
        }
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request): JsonResponse
    {

        $validator = Validator::make($request->all(), [
            'service_id' => 'required|exists:services,id',
            'location_id' => 'required|exists:locations,id',
            'description' => 'required|string|max:255',
            'start_price' => 'required|numeric|min:0',
            'provider_price' => "required|numeric|min:0",
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->messages(),
            ], 422);
            } else {
                $inputPrice = floatval( $request->input('start_price', 0));
                $existingPrices = ServiceInformation::whereNotNull('start_price')->pluck('start_price');
                if ($existingPrices->count() > 0) {
                    $totalSum = $existingPrices->sum() + $inputPrice;
                    $entryCount = $existingPrices->count() + 1;
                    $deducted = $totalSum / $entryCount;
                    $deducted = round($deducted, 2);
                } else {
                    $deducted = $inputPrice - ($inputPrice * 0.30);
                }


            $serviceinformation = ServiceInformation::create([
                'user_id' => Auth::user()->id,
                'service_id' => $request->service_id,
                'location_id' => $request->location_id,
                'description' => $request->description,
                'start_price' => $deducted,
                'provider_price' => $request->provider_price,

            ]);

            if ($serviceinformation) {
                return response()->json([
                    'status' => 200,
                    'message' => "Service Information added successfully",
                    'data' => $serviceinformation
                ], 200);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => "Something Went Wront!",
                ], 500);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $serviceInformation = ServiceInformation::find($id);
        if ($serviceInformation) {
            return response()->json([
                'status' => 200,
                'serviceInformation' => $serviceInformation,
            ], 200);
        }
        return response()->json([
            'status' => 404,
            'message' => "Service Information not found!",
        ], 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [

            'service_id' => 'required|exists:services,id',
            'location_id' => 'required|exists:locations,id',
            'description' => 'required|string|max:255',
            'start_price' => 'required|numeric|min:0',
            'provider_price' => "required|numeric|min:0",
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->messages(),
            ], 422);
        } else {
            $serviceinformation = ServiceInformation::find($id);
            if ($serviceinformation) {
                $serviceinformation->update([
                    'service_id' => $request->service_id,
                    'location_id' => $request->location_id,
                    'description' => $request->description,
                    'start_price' => $request->start_price,
                    'provider_price' => $request->provider_price,
                ]);
                return response()->json([
                    'status' => 200,
                    'message' => "Service Information updated successfully",
                ], 200);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => "Something Went Wrong!",
                ], 500);
            }
        }
    }
    public function destroy($id)
    {
        try {
            $service_information=ServiceInformation::findOrFail($id);
            if($service_information){
                $service_information->delete();
            }
            else{
                return response()->json([
                    'status' => 'error',
                    'message' => "service information not found",
                ], 404);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
