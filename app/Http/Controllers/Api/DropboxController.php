<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Spatie\Dropbox\Client;
use League\Flysystem\Filesystem;
use League\Flysystem\Dropbox\DropboxAdapter;
use GuzzleHttp\Client as GuzzleClient;

class DropboxController extends Controller
{
    // Redirect to Dropbox OAuth
    public function redirectToDropbox()
    {
        try {
            $user = Auth::guard('sanctum')->user();
            if (!$user) {
                \Log::error('No authenticated user found');
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Generate the OAuth 2.0 authorization URL with state parameter
            $authUrl = 'https://www.dropbox.com/oauth2/authorize?' . http_build_query([
                'client_id' => config('services.dropbox.key'),
                'response_type' => 'code',
                'redirect_uri' => config('services.dropbox.redirect'),
                'force_reapprove' => 'true',
                'token_access_type' => 'offline',
                'state' => 'dropbox' // Add state parameter to identify the source
            ]);

            return response()->json([
                'status' => 'success',
                'redirect_url' => $authUrl,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in redirectToDropbox:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while generating the Dropbox auth URL'
            ], 500);
        }
    }

    // Handle Dropbox OAuth Callback
    public function handleCallback(Request $request)
    {
        try {
            \Log::info('Dropbox callback received:', [
                'request_data' => $request->all(),
                'headers' => $request->headers->all()
            ]);

            if (!$request->code) {
                \Log::error('No authorization code received');
                return response()->json([
                    'status' => 'error',
                    'message' => 'No authorization code received'
                ], 400);
            }

            $user = Auth::guard('sanctum')->user();
            if (!$user) {
                \Log::error('No authenticated user found');
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not authenticated'
                ], 401);
            }

            \Log::info('Processing callback for user:', ['user_id' => $user->id]);

            // Exchange the authorization code for an access token
            $client = new GuzzleClient();
            \Log::info('Attempting to exchange code for token');
            $response = $client->post('https://api.dropboxapi.com/oauth2/token', [
                'form_params' => [
                    'code' => $request->code,
                    'grant_type' => 'authorization_code',
                    'client_id' => config('services.dropbox.key'),
                    'client_secret' => config('services.dropbox.secret'),
                    'redirect_uri' => config('services.dropbox.redirect'),
                ]
            ]);

            $tokenData = json_decode($response->getBody(), true);
            \Log::info('Token response received:', ['token_data' => array_keys($tokenData)]);

            if (!isset($tokenData['access_token'])) {
                \Log::error('Failed to get Dropbox access token');
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to get access token'
                ], 400);
            }

            // Save access token in DB
            \Log::info('Saving access token to database');
            $user->dropbox_access_token = $tokenData['access_token'];
            $user->save();
            
            \Log::info('Successfully saved Dropbox access token for user:', ['user_id' => $user->id]);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Dropbox connected successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in Dropbox callback:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred during Dropbox authentication'
            ], 500);
        }
    }

    // List Files from Dropbox
    public function listFiles()
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                \Log::error('No authenticated user found when listing Dropbox files');
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not authenticated'
                ], 401);
            }

            if (!$user->dropbox_access_token) {
                \Log::error('No Dropbox token found for user: ' . $user->id);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Dropbox not connected'
                ], 401);
            }

            $client = new Client($user->dropbox_access_token);
            $files = $client->listFolder('');

            \Log::info('Successfully retrieved ' . count($files['entries']) . ' files for user: ' . $user->id);

            return response()->json([
                'status' => 'success',
                'files' => $files['entries']
            ]);
        } catch (\Exception $e) {
            \Log::error('Error listing Dropbox files:', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while listing files'
            ], 500);
        }
    }

    // Disconnect Dropbox
    public function disconnect()
    {
        try {
            $user = Auth::guard('sanctum')->user();
            if (!$user) {
                \Log::error('No authenticated user found');
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Clear the Dropbox token
            $user->dropbox_access_token = null;
            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Successfully disconnected from Dropbox'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error disconnecting from Dropbox:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while disconnecting from Dropbox'
            ], 500);
        }
    }
} 