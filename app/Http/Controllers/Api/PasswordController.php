<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mail\sendMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;
use Illuminate\Support\Str;
use app\Models\ModelPasswordReset;
use App\Models\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\support\Facades\Auth;
use app\Models\User;
use Illuminate\Support\Facades\DB;

class PasswordController extends Controller
{
    public function resetPassword(Request $request){
        $request->validate([
            'userId' => 'required',
            'email' => 'required',
            'password' =>'required',
            'newPassword' => 'required'
        ]);
        $result = Auth::attempt(['email' => $request->email, 'password' => $request->password]);
        if($result){
            User::where('id', $request->userId)->update(['password' => Hash::make($request->newPassword)]);
            return response()->json(['message'=>"password updated successfully", 'status'=>200]);
        }
        else{
            return response()->json(['message'=>"Check your old password", 'status'=>400]);
        }
    }

    public function forgotPassword(Request $request){
        $request->validate([
            'email' => 'required',
        ]);
        $email = $request->email;
        $user = User::where('email', $email)->first();
        if(!$user){
            return response()->json(['message' => "Email does not exists", 'status' => 404]);
        }
        else{
            
        $token = Str::random(10);
        $reset = PasswordReset::create([
            'email' => $request->email,
            'token' => $token
        ]);

        Mail::to($email)->send(new SendMail($token, $email));
        return "Token sent on mail";
        }     
    }

    public function reset(Request $request){
        $request->validate([
            'email' =>'required',
            'password' => 'required',
            'token' => 'required'
        ]);

        $passwordReset = PasswordReset::where('token', $request->token)->first();
        if(!$passwordReset){
            return response()->json(['message' => "Token is invalid or expired"]);
        }

        $user = DB::table('users')->where('email', $passwordReset->email)->update(['password'=>Hash::make($request->password)]);
        //$user->password = Hash::make($user->password);

        PasswordReset::where('email', $request->email)->delete();
        return "Password changed";
    }
}
