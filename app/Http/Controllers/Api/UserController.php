<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
           /**
     * Gets users except yourself
     *
     * 
     * 
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $users = User::where('id', '!=', Auth::user()->id)->get();
        return $this->success($users);
    }
}
