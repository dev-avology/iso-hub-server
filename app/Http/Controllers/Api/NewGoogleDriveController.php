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
        $client = new Client();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setRedirectUri(config('services.google.redirect'));
        $client->addScope(Drive::DRIVE);
        $client->setAccessType('offline'); // Required for refresh token

        $authUrl = $client->createAuthUrl();
        return response()->json([
            'status' => 'success',
            'redirect_url' => $authUrl,
        ]);
    }

    // Handle Google OAuth Callback
    public function handleGoogleCallback(Request $request)
    {
        $client = new Client();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setRedirectUri(config('services.google.redirect'));

        $client->setHttpClient(new \GuzzleHttp\Client([
            'verify' => false, // Bypass SSL verification for local development
        ]));

        // Exchange authorization code for an access token
        $token = $client->fetchAccessTokenWithAuthCode($request->code);

        if (isset($token['error'])) {
            return response()->json(['error' => 'Google authentication failed!']);
        }

        // Find authenticated user (from session or token)
        // $user = Auth::user(); // Modify this for API-based auth

        $user = User::find('2');

        if ($user) {
            // Save access token in DB
            $user->google_access_token = json_encode($token);
            $user->save();
            return response()->json(['message' => 'Google Drive connected successfully!']);
        }

        return response()->json(['error' => 'User not authenticated']);
    }

    // List Files from Google Drive
    // List Files from Google Drive
    // List Files from Google Drive
    public function listFiles()
    {
        // $user = Auth::user();
        $user = User::find('2');
        $accessToken = $user->google_access_token;

        $client = new Client();
        $client->setAuthConfig(storage_path('app/credentials.json'));
        $client->setAccessToken($accessToken);

        // ✅ Add the correct scopes
        $client->addScope([
            'https://www.googleapis.com/auth/drive',
            'https://www.googleapis.com/auth/drive.file',
            'https://www.googleapis.com/auth/drive.readonly',
        ]);

        // ✅ Disable SSL Verification (Temporary)
        $guzzleClient = new GuzzleClient(['verify' => false]);
        $client->setHttpClient($guzzleClient);

        // ✅ Check if the token is expired and refresh if necessary
        if ($client->isAccessTokenExpired()) {
            if (isset($accessToken['refresh_token'])) {
                $newToken = $client->fetchAccessTokenWithRefreshToken($accessToken['refresh_token']);
                $newToken['refresh_token'] = $accessToken['refresh_token'];

                // ✅ Save the new token if necessary
                $user = User::where('id', 1)->first();
                if ($user) {
                    $user->google_access_token = json_encode($newToken);
                    $user->save();
                }

                $client->setAccessToken($newToken);
            } else {
                return response()->json(['error' => 'No refresh token available. Please reconnect Google Drive.']);
            }
        }

        // ✅ List Files from Google Drive
        $service = new Drive($client);
        $files = $service->files->listFiles([
            'fields' => 'files(id, name, mimeType, webViewLink, webContentLink, thumbnailLink)',
        ]);
        return response()->json(['status' => 'success','files' => $files->getFiles()]);
    }
}
