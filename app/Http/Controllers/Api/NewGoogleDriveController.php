<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Google\Client;
use Google\Service\Drive;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client as GuzzleClient;


class NewGoogleDriveController extends Controller
{
    // Redirect to Google OAuth
    public function redirectToGoogle()
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

            $client = new Client();
            $client->setClientId(config('services.google.client_id'));
            $client->setClientSecret(config('services.google.client_secret'));
            $client->setRedirectUri(config('services.google.redirect'));
            $client->addScope(Drive::DRIVE);
            $client->setAccessType('offline');
            $client->setPrompt('consent');
            $client->setIncludeGrantedScopes(true);

            $authUrl = $client->createAuthUrl();
            return response()->json([
                'status' => 'success',
                'redirect_url' => $authUrl,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in redirectToGoogle:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while generating the Google auth URL'
            ], 500);
        }
    }

    // Handle Google OAuth Callback
    public function handleCallback(Request $request)
    {
        try {
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

            $client = new Client();
            $client->setClientId(config('services.google.client_id'));
            $client->setClientSecret(config('services.google.client_secret'));
            $client->setRedirectUri(config('services.google.redirect'));
            $client->setAccessType('offline');
            $client->setPrompt('consent');

            $client->setHttpClient(new \GuzzleHttp\Client([
                'verify' => false,
            ]));

            try {
                $token = $client->fetchAccessTokenWithAuthCode($request->code);
            } catch (\Exception $e) {
                \Log::error('Error fetching access token:', [
                    'error' => $e->getMessage(),
                    'code' => $request->code,
                    'trace' => $e->getTraceAsString()
                ]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid authorization code'
                ], 400);
            }

            if (isset($token['error'])) {
                \Log::error('Google authentication error:', $token);
                return response()->json([
                    'status' => 'error',
                    'message' => $token['error']
                ], 400);
            }

            if (!isset($token['access_token'])) {
                \Log::error('No access token in response:', $token);
                return response()->json([
                    'status' => 'error',
                    'message' => 'No access token received'
                ], 400);
            }

            // Save access token in DB
            $user->google_access_token = json_encode($token);
            $user->save();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Google Drive connected successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in Google callback:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred during Google authentication'
            ], 500);
        }
    }

    // List Files from Google Drive
    public function listFiles()
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                \Log::error('No authenticated user found when listing Google Drive files');
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not authenticated'
                ], 401);
            }

            if (!$user->google_access_token) {
                \Log::error('No Google Drive token found for user: ' . $user->id);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Google Drive not connected'
                ], 401);
            }

            $accessToken = json_decode($user->google_access_token, true);

            if (!$accessToken) {
                \Log::error('Invalid Google Drive token format for user: ' . $user->id);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid Google Drive token'
                ], 401);
            }

            $client = new Client();
            $client->setClientId(config('services.google.client_id'));
            $client->setClientSecret(config('services.google.client_secret'));
            $client->setRedirectUri(config('services.google.redirect'));
            
            $client->setAccessToken($accessToken);

            // Add the correct scopes
            $client->addScope([
                'https://www.googleapis.com/auth/drive',
                'https://www.googleapis.com/auth/drive.file',
                'https://www.googleapis.com/auth/drive.readonly',
            ]);

            // Check if token is expired and refresh if necessary
            if ($client->isAccessTokenExpired()) {
                if (isset($accessToken['refresh_token'])) {
                    $newToken = $client->fetchAccessTokenWithRefreshToken($accessToken['refresh_token']);
                    $newToken['refresh_token'] = $accessToken['refresh_token'];

                    $user->google_access_token = json_encode($newToken);
                    $user->save();

                    $client->setAccessToken($newToken);
                } else {
                    \Log::error('No refresh token available for user: ' . $user->id);
                    return response()->json([
                        'status' => 'error',
                        'message' => 'No refresh token available. Please reconnect Google Drive.'
                    ], 401);
                }
            }

            // List Files from Google Drive
            $service = new Drive($client);
            $files = $service->files->listFiles([
                'fields' => 'files(id, name, mimeType, webViewLink, webContentLink, thumbnailLink)',
                'pageSize' => 100 // Adjust as needed
            ]);

            \Log::info('Successfully retrieved ' . count($files->getFiles()) . ' files for user: ' . $user->id);

            return response()->json([
                'status' => 'success',
                'files' => $files->getFiles()
            ]);
        } catch (\Exception $e) {
            \Log::error('Error listing Google Drive files:', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while listing files'
            ], 500);
        }
    }
}
