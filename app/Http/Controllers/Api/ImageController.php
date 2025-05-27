<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Models\Service;
use App\Models\ServiceInformation;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImageController extends Controller
{
    public function uploadImage(Request $request, int $id)
    {
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
        ]);
        $imageData = [];
        $entity = null;
        $filePath = '';
        if ($request->get('type') == 'service_information') {
            $entity = ServiceInformation::find($id);
            if (!$entity) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Service not found',
                ], 404);
            }
            $filePath = 'images/service_information/';
        }

        if ($request->get('type') == 'service') {
            $entity = Service::find($id);
            if (!$entity) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Service not found',
                ], 404);
            }
            $filePath = 'images/service/';
        }

        foreach ($request->file('images') as $file) {
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path($filePath), $fileName);

            $imagePath = $filePath . $fileName;
            $imageData[] = [
                's_information_id' => $request->get('type') == 'service_information' ? $entity->id : null,
                'service_id' => $request->get('type') == 'service' ? $entity->id : null,
                'user_id' => Auth::user()->id,
                'image' => url($imagePath),
            ];
        }

        Image::insert($imageData);

        return response()->json([
            'status' => 'success',
            'message' => 'Uploaded successfully',
            'data' => $imageData,
        ], 200);
    }

    public function destroy($id)
    {
        try {
            $images = Image::where('s_information_id', $id)->get();

            foreach ($images as $image) {
                $imagePath = public_path(str_replace(url('/'), '', $image->image));
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
                $image->delete();
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Images have been deleted successfully',
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Images not found',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
