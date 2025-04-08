<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client as GuzzleClient;

class OneDriveController extends Controller
{
    // Redirect to OneDrive OAuth
    public function redirectToOneDrive()
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
            $authUrl = 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize?' . http_build_query([
                'client_id' => config('services.onedrive.client_id'),
                'response_type' => 'code',
                'redirect_uri' => config('services.onedrive.redirect_uri'),
                'response_mode' => 'query',
                'scope' => 'offline_access Files.Read',
                'state' => 'onedrive' // Add state parameter to identify the source
            ]);

            return response()->json([
                'status' => 'success',
                'redirect_url' => $authUrl,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in redirectToOneDrive:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while generating the OneDrive auth URL'
            ], 500);
        }
    }

    // Handle OneDrive OAuth Callback
    public function handleCallback(Request $request)
    {
        try {
            \Log::info('OneDrive callback received:', [
                'request_data' => $request->all(),
                'headers' => $request->headers->all(),
                'content_type' => $request->header('Content-Type')
            ]);

            // Check if the request has the code in the correct format
            $code = $request->input('code');
            if (!$code) {
                \Log::error('No authorization code received in request body');
                return response()->json([
                    'status' => 'error',
                    'message' => 'No authorization code received in request body'
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
            \Log::info('Attempting to exchange code for token with params:', [
                'code' => $code,
                'client_id' => config('services.onedrive.client_id'),
                'redirect_uri' => config('services.onedrive.redirect_uri'),
            ]);

            try {
                $response = $client->post('https://login.microsoftonline.com/common/oauth2/v2.0/token', [
                    'form_params' => [
                        'client_id' => config('services.onedrive.client_id'),
                        'client_secret' => config('services.onedrive.client_secret'),
                        'code' => $code,
                        'redirect_uri' => config('services.onedrive.redirect_uri'),
                        'grant_type' => 'authorization_code',
                    ]
                ]);

                $tokenData = json_decode($response->getBody(), true);
                \Log::info('Token response received:', ['token_data' => array_keys($tokenData)]);

                if (!isset($tokenData['access_token'])) {
                    \Log::error('Failed to get OneDrive access token:', ['response' => $tokenData]);
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Failed to get access token from OneDrive'
                    ], 400);
                }

                // Save access token in DB
                \Log::info('Saving access token to database');
                $user->onedrive_access_token = $tokenData['access_token'];
                $user->onedrive_refresh_token = $tokenData['refresh_token'] ?? null;
                $user->save();
                
                \Log::info('Successfully saved OneDrive access token for user:', ['user_id' => $user->id]);
                
                return response()->json([
                    'status' => 'success',
                    'message' => 'OneDrive connected successfully'
                ]);
            } catch (\GuzzleHttp\Exception\ClientException $e) {
                \Log::error('OneDrive API error:', [
                    'error' => $e->getMessage(),
                    'response' => $e->getResponse()->getBody()->getContents(),
                    'status' => $e->getResponse()->getStatusCode()
                ]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to authenticate with OneDrive: ' . $e->getMessage()
                ], 400);
            }
        } catch (\Exception $e) {
            \Log::error('Error in OneDrive callback:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred during OneDrive authentication: ' . $e->getMessage()
            ], 500);
        }
    }

    // List Files from OneDrive
    public function listFiles()
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                \Log::error('No authenticated user found when listing OneDrive files');
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not authenticated'
                ], 401);
            }

            if (!$user->onedrive_access_token) {
                \Log::error('No OneDrive token found for user: ' . $user->id);
                return response()->json([
                    'status' => 'error',
                    'message' => 'OneDrive not connected'
                ], 401);
            }

            $client = new GuzzleClient();
            $response = $client->get('https://graph.microsoft.com/v1.0/me/drive/root/children', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $user->onedrive_access_token,
                    'Accept' => 'application/json',
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            $files = $data['value'] ?? [];

            \Log::info('Successfully retrieved ' . count($files) . ' files for user: ' . $user->id);

            return response()->json([
                'status' => 'success',
                'files' => $files
            ]);
        } catch (\Exception $e) {
            \Log::error('Error listing OneDrive files:', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while listing files'
            ], 500);
        }
    }

    // Disconnect OneDrive
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

            // Clear the OneDrive token
            $user->onedrive_access_token = null;
            $user->onedrive_refresh_token = null;
            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Successfully disconnected from OneDrive'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error disconnecting from OneDrive:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while disconnecting from OneDrive'
            ], 500);
        }
    }
} 