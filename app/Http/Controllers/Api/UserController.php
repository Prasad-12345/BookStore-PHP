<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
    public function register(Request $request)
    { 
        $data = $request-> validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users,email',
            'phone_no' => 'required|string|min:10|max:12',
            'password' => 'required|string',
            'role' => 'required|string'
        ]);
        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone_no' => $data['phone_no'],
            'password' => Hash::make($data['password']),
            'role' => $data['role']
        ]);

        $token = $user->createToken('Token')->plainTextToken;

        $response = [
            'user'=>$user,
            'token'=>$token,
        ];
        return response($response,201);
    }

    public function verifyEmail(Request $request){
        $user = $request->user();
        $user->email_verified_at = now();
        $user->save();
        return response()->json(["message"=>"email is verified"]);
    }

    public function login(Request $request)
    {
        $data = $request-> validate([
            
            'email' => 'required|email|max:100|',
            'password' => 'required|string',
        ]);

        $user = User::where('email',$data['email'])->first();

        if(!$user || !Hash::check($data['password'], $user->password))
        {
            return response(['message' => 'Invalid Credentials'], 401);
        }
        else
        {
            $token = $user->createToken('Login')->plainTextToken;
            $response = [
                'user' => $user,
                'token' => $token,
            ];
            return response($response, 200);
        }
    }

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message'=>"User logged out successfully", "SussceeStatus"=>200]);
    }
}
