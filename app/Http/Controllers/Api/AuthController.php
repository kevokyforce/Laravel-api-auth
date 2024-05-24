<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register (Request $reqest)
    {
       try {
        $validate = Validator::make($reqest->all(),
        [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required| min:4',
            'confirm_password' => 'required|same:password',
        ]);

        if($validate->fails()){
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'errors' => $validate->errors()
            ], 400);
        }

        $user = User::create([
            'name' => $reqest->name,
            'email' => $reqest->email,
            'password' => bcrypt($reqest->password),

        ]);

            return response()->json([
                'status' => true,
                'message' => 'User Created Successfully',
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 200);

       } catch (\Throwable $th) {

        return response()->json([
            'status' => false,
            'message' => $th->getMessage(),
        ], 500);

       }
    }

    //  Login User
    public function login (Request $reqest)
    {
        try 
        {
            $validate = Validator::make($reqest->all(),
            [
                'email' => 'required|email| exist:users' ,
                'password' => 'required| min:4',
            ]);
    
            if($validate->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validate->errors()
                ], 400);
            }

            if(!Auth::attempt($reqest->only(['email', 'password']))) {
                
                return response()->json([
                    'status' => false,
                    'message' => 'Email or Password Does Not Match Our Records',
                ], 400);
            }

            $user = User::where('email', $reqest->email)->first();

            return response()->json([
                'status' => true,
                'message' => 'User Logged In Successfully',
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function profile() {
        $userData = auth()->user();
        return response()->json([
            'status' => true,
            'message' => 'Profile Information',
            'data' => $userData,
            'id' => auth()->user()->id
        ], 200);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response()->json([
            'status' => true,
            'message' => 'User Logged Out',
            'data' => [],
        ], 200);
    }
}
