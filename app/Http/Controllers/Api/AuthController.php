<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{


    public function Register(Request $request)
    {
        try {
            $request->validate([
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|confirmed|min:8',
                'phone_number' => 'required|digits:10|unique:users,phone_number',
                'address' => 'required',
                "role" => "in:admin,company,worker",
                'photo' => "mimes:jpg,png,jpeg|nullable",
            ]);
            if ($request->has('photo')) {
                $file = $request->file('photo');
                $file_extension =  $file->getClientOriginalExtension();
                $file_name = time() . "." . $file_extension;
                $path = 'images/user/';
                $file->move($path, $file_name);
                $image_path = $path . $file_name;
            } else {
                $image_path = 'images/defulte.png';
            }
            $user = User::create([
                "email" => $request->email,
                "password" => bcrypt($request->password),
                "first_name" => $request->first_name,
                'last_name' => $request->last_name,
                "phone_number" => $request->phone_number,
                "role" => $request->role ?? 'worker',
                "address" => $request->address,
                'photo' => Url($image_path)

            ]);


            if ($user) {
                return response()->json(['status' => 'success', 'message' => 'Successfully inserted data', 'data' => $user], 201);
            }
        } catch (ValidationException $e) {
            return response()->json(['status' => 'error', 'message' => $e->errors()], 400);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    //{post} form_data login api 
    public function login(Request $request)
    {
        try {
            $validate = $request->validate([
                'phone_number' => 'required|digits:10',
                "password" => "required|min:8",
            ]);
            if (!Auth::attempt(['phone_number' => $request->phone_number, 'password' => $request->password])) {
                return response()->json(['status' => 'error', 'message' => 'unbale login invalid'], 401);
            }
            $user = Auth::user();
            $token = $user->createToken('apiToken')->plainTextToken;
            return response()->json(['status' => 'success', 'token' => $token, 'role' => $user->role], 200);
        } catch (ValidationException $e) {
            return response()->json(['status' => 'error', 'message' => $e->errors()], 400);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
    public function profile()
    {
        try {
            $user = Auth::user();
            if ($user) {
                return response()->json(['status' => 'success', 'message' => 'true get data', 'data' => $user], 200);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
    //{GET} delete token ,,, logout api 
    public function logout()
    {
        try {
            $user = Auth::user();
            if ($user) {
                $user->currentAccessToken()->delete();
                return response()->json(['status' => 'sucsses', 'message' => 'logout sucssesfully'], 200);
            }
        } catch (ValidationException $e) {
            return response()->json(['status' => 'error', 'message' => $e->errors()], 400);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
    public function changePassword(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'old_password' => 'required',
                'new_password' => 'required|confirmed|min:8',
            ]);

            $user = Auth::user();

            if (!$user) {
                return response()->json(['status' => 'error', 'message' => 'User not authenticated'], 401);
            }

            if (!Hash::check($validatedData['old_password'], $user->password)) {
                return response()->json(['status' => 'error', 'message' => 'Old password does not match'], 402);
            }

            $user->update(['password' => bcrypt($validatedData['new_password'])]);

            return response()->json(['status' => 'success', 'message' => 'Password changed successfully'], 200);
        } catch (ValidationException $e) {
            return response()->json(['status' => 'error', 'message' => $e->errors()], 400);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
    public function changePhoneNumber(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'password' => 'required',
                "phone_number" => "required|digits:10|unique:users,phone_number",
            ]);
            $user = Auth::user();
            if (!$user) {
                return response()->json(['status' => 'error', 'message' => 'User not authenticated'], 401);
            }
            if (!Hash::check($validatedData['password'], $user->password)) {
                return response()->json(['status' => 'error', 'message' => ' password does not Valid'], 400);
            }
            $user->update(['phone_number' => $validatedData['phone_number']]);
            return response()->json(['status' => 'success', 'message' => 'PhoneNumber changed successfully'], 200);
        } catch (ValidationException $e) {
            return response()->json(['status' => 'error', 'message' => $e->errors()], 400);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
