<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected $AuthService;

    public function __construct()
    {
        // $this->CartService = $CartService;
    }

    public function register(Request $request)
    {
        // Use Validator for detailed error handling
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Return validation errors if any
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Generate authentication token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        // Use Validator for detailed error handling
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        // Return validation errors if any
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Find user by email
        $user = User::where('email', $request->email)->first();

        // Check if user exists and password is correct
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Generate authentication token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Get user's roles and permissions using Spatie
        $roles = $user->getRoleNames(); // Returns a collection of role names
        $permissions = $user->getAllPermissions()->pluck('name'); // Get all permissions assigned

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'roles' => $roles, // List of roles
            'permissions' => $permissions, // List of permissions
            'token' => $token,
        ]);
    }


    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out']);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }


    public function oauthCallback(Request $request)
    {
        $code = $request->query('code');
        $locationId = $request->query('locationId');

        $array = [
            'code' => $code,
            'location_id' => $locationId
        ];

        // // Step 1: Exchange code for access token
        // $response = Http::post('https://services.leadconnectorhq.com/oauth/token', [
        //     'client_id' => env('GHL_CLIENT_ID'),
        //     'client_secret' => env('GHL_CLIENT_SECRET'),
        //     'grant_type' => 'authorization_code',
        //     'code' => $code,
        //     'redirect_uri' => route('ghl.oauth.callback'),
        // ]);

        // $data = $response->json();

        // if (isset($data['access_token'])) {
        //     // Step 2: Save Location and Credentials
        //     GhlLocation::updateOrCreate(
        //         ['location_id' => $locationId],
        //         [
        //             'access_token' => $data['access_token'],
        //             'refresh_token' => $data['refresh_token'],
        //             'expires_in' => now()->addSeconds($data['expires_in']),
        //         ]
        //     );

        //     // Step 3: Redirect to form page
        //     return redirect()->route('ghl.credentials.form', ['locationId' => $locationId]);
        // }

        return response()->json(['error' => 'OAuth failed', 'details' => $array]);
    }
}
