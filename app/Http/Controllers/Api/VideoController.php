<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceInformation;
use App\Models\Video;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class VideoController extends Controller
{
    public function store(Request $request, $id)
    {
        try {
            $request->validate([
                "video" => 'required|mimes:mp4,ogg,webm,3gp,mkv|max:100000',
            ]);
            $entity = ServiceInformation::find($id);
            $filePath = '';
            if (!$entity) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Service information not found',
                ], 404);
            }
            $filePath = 'Video/';
            $file = $request->file('video');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path($filePath), $fileName);

            $videoPath = $filePath . $fileName;
            $video = Video::create([
                'video' => url($videoPath),
                's_information_id' => $entity->id,
                'user_id' => Auth::user()->id
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Video has been uploaded successfully',
                'data' => $video
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        } catch (ValidationException $validationException) {
            return response()->json([
                'status' => 'error',
                'message' => $validationException->errors(),
            ], 422);
        }
    }
    public function destroy(int $serviceInformationId)
    {
        try {
            $video = Video::where('s_information_id', $serviceInformationId)->firstOrFail();

            $videoPath = public_path(str_replace(url('/'), '', $video->video));
            if (file_exists($videoPath)) {
                unlink($videoPath);
            }
            $video->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Video has been deleted successfully',
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Video not found',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
