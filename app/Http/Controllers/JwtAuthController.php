<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator; 
use App\Models\User;

use Illuminate\Support\Facades\Auth;

class JWTAuthController extends Controller
{
    public function __construct(){
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    protected function guard(){
        return Auth::guard();
    }

    // Get JWT via given credentials
    public function login(Request $request){
        $req = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:5',
        ]);

        if($req->fails()){
            return response()->json(['message' => $req->errors()],422);
        }

        $token_validity = (24*60); //1 Day Validity
        auth()->factory()->setTTL($token_validity);

        if(! $token = auth()->attempt($req->validated())){
            return response()->json(['Auth Error' => 'Unauthorized'],401);
        }

        return $this->generateToken($token);
    }

    // Signup

    public function register(Request $request){
        $req = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);

        if($req->fails()){
            return response()->json($req->errors()->toJson(), 400);
        }

        $user = User::create(array_merge($req->validated(), ['password' => bcrypt($request->password)]));

        return response()->json(['message' => 'User Signed Up', 'user' => $user],201);
    }

    // Signout
    public function signout(){
        auth()->logout();
        return response()->json(['message'=> 'User Logged Out']);
    }

    // Token Refresh
    public function refresh(){
        return $this->generateToken(auth()->refresh());
    }

    // User
    public function user(){
        return response()->json(["data" => auth()->user()]);
    }

    // Generate Token
    protected function generateToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }
}
