<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use App\Mail\sendMail;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * @OA\Post(
     *   path="/api/register",
     *   summary="register",
     *   description="register",
     *   @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"first_name","last_name", "email", "phone_no", "password", "role"},
     *               @OA\Property(property="first_name", type="string"),
     *               @OA\Property(property="last_name", type="string"),
     *               @OA\Property(property="email", type="email"),
     *               @OA\Property(property="phone_no", type="string"),
     *               @OA\Property(property="password", type="string"),
     *               @OA\Property(property="role", type="string"),
     *            ),
     *        ),
     *    ),
     *   @OA\Response(response=201, description="User successfully registered"),
     *   @OA\Response(response=401, description="The email has already been taken"),
     * )
     * 
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    { 
        $data = $request-> validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users,email',
            'phone_no' => 'required|string|min:10|max:12',
            'password' => 'required|string',
            // 'confirm_password' => 'required|same:password',
            'role' => 'required|string',
            // 'email_verified_at' => 'required'
        ]);
        $email = $request->email;
        $checkUser = DB::table('users')->where('email', $email)->first();
        if(!$checkUser){
            $user = User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone_no' => $data['phone_no'],
                'password' => Hash::make($data['password']),
                'role' => $data['role'],
                // 'email_verified_at' => $request->email_verified_at
            ]);
    
            $token = $user->createToken('Token')->plainTextToken;
            Mail::to($email)->send(new SendMail($token, $email));
            $response = [
                'user'=>$user,
                'token'=>$token,
            ];
            return response($response,201);
            
        }
        else{
            Log::channel('custom')->error("User already registered");
        }
    }

    public function verifyEmail(Request $request){
        $user = $request->user();
        $user->email_verified_at = now();
        $user->save();
        return response()->json(["message"=>"email is verified"]);
    }

    /**
     * @OA\Post(
     *   path="/api/login",
     *   summary="login",
     *   description="login",
     *   @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"email","password"},
     *               @OA\Property(property="email", type="email"),
     *               @OA\Property(property="password", type="string"),
     *            ),
     *        ),
     *    ),
     *   @OA\Response(response=201, description="User successfully logged in"),
     *   @OA\Response(response=401, description="Invalid Credentials"),
     * )
     * 
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $data = $request-> validate([        
            'email' => 'required|email|max:100|',
            'password' => 'required|string',
        ]);

        $user = User::where('email',$data['email'])->first();
        $email = $request->email;
        if(!$user || !Hash::check($data['password'], $user->password))
        {
            Log::channel('custom')->error("Invalid Credentials");
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

       /**
     * @OA\Post(
     *   path="/api/logout",
     *   summary="logout",
     *   description="logout",
     *  "securitySchemes":{
     *              "BearerAuth":{
     *                 "type":"http",
     *                "scheme":"bearer"
     *           }
     *         
     *     }
     *   @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *              
     *              
     *            ),
     *        ),
     *    ),
     *   security = {
     *      { "Bearer" : {} }
     *   }
     *   @OA\Response(response=201, description="User successfully registered"),
     *   @OA\Response(response=401, description="The email has already been taken"),
     * )
     * 
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message'=>"User logged out successfully", "SussceeStatus"=>200]);
    }
}
