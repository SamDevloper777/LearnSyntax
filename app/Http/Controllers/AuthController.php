<?php
 
namespace App\Http\Controllers;
 
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Facades\JWTAuth;
 
class AuthController extends Controller
{
    // Register method
    public function register(Request $request)
    {
        // Check if email already exists
        if (User::where('email', $request->email)->exists()) {
            return response()->json(['error' => 'Email already in use.'], 409);
        }
 
        // Validate request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email|max:255',
            'password' => 'required|string|min:6',
        ]);
 
        // Create new user
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password']),
        ]);
 
        return response()->json(['message' => 'User successfully registered', 'user' => $user], 200);
    }
 
    // Login method
    public function login(Request $request)
    {
        // Validate login credentials
        $validatedData = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
 
        // Attempt to create JWT token
        $credentials = $request->only('email', 'password');
        
 
        if ($token = JWTAuth::attempt($credentials)) {
            return $this->respondWithToken($token);
        }
 
        return response()->json(['error' => 'Invalid credentials','message'=>'login successfully'], 401);
    }
 
    // Get user info method
    public function user(Request $request)
    {
        // Get token from request
        $token = $request->bearerToken();
        if (!$token) {
            return response()->json(['error' => 'Token is required'], 401);
        }
        try{
            $user = JWTAuth::setToken($token)->authenticate();
            $role = 'user';
            if($user->isAdmin){
                $role = 'admin';
            }
           
            return response()->json([
                'message'=>'Authenticated as'.ucfirst($role),
                'user'=>$user,
                'role'=>$role,
                'expires_in'=>auth()->factory()->getTTL() *60,
            ],200);
        } catch(TokenExpiredException $e){
            return response()->json(['error' => 'Token has Expired'],401);
        }
 
        // Get authenticated user based on the token
        $user = JWTAuth::user();
        return response()->json(['user' => $user], 200);
    }
 
    // Logout method
    public function logout()
    {
        $token = JWTAuth::getToken();
        if (!$token) {
            return response()->json(['error' => 'Token is required for logout'], 401);
        }
 
        // Invalidate the token
        JWTAuth::invalidate($token);
        return response()->json(['message' => 'Successfully logged out']);
    }
 
    // Refresh token method
    public function refresh(Request $request)
    {
        $token = $request->bearerToken();
        if (!$token) {
            return response()->json(['error' => 'Token is required for refresh'], 401);
        }
 
        try {
            // Refresh the token and return it
            $newToken = JWTAuth::refresh($token);
           
            return $this->respondWithToken($newToken);
        } catch (\Exception $e) {
            return response()->json([
               
                'error' => 'Token refresh failed'], 401);
        }
    }
 
    // Respond with token details
    protected function respondWithToken($token)
    {
        $user = JWTAuth::user();
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'user' => $user,
            'expires_in' => auth()->factory()->getTTL() * 60,
        ]);
    }
}