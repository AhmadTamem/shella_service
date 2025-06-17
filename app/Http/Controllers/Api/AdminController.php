<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function get_all_provider(){
        $provider=User::with('services','locations','image');
        return response()->json([
            'status'=>200,
            'provider'=>$provider,
        ],200);
    }

    public function get_service(){
        $service=Service::with('category,images');
        return response()->json([
            'status'=>200,
            'service'=>$service,
        ],200);

    }
    public function get_masseges(){
        $massege=ChatMessage::with('user');
        return response()->json([
            'status'=>200,
            'service'=>$massege
        ],200);
    }
     
}
