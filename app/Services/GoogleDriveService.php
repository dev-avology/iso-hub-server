<?php

namespace App\Services;

use Google\Client;
use Google\Service\Drive;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class GoogleDriveService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setClientId(env('GOOGLE_CLIENT_ID'));
        $this->client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $this->client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));
        $this->client->addScope(Drive::DRIVE_READONLY);
        $this->client->setAccessType('offline');
    }

    // Get Google Auth URL
    public function getAuthUrl()
    {
        return $this->client->createAuthUrl();
    }

    // Handle Google Callback and Save Access Token
    // Handle Google Callback and Save Access Token
    public function handleGoogleCallback($code)
    {
        $this->client->setAuthConfig(storage_path('app/credentials.json'));

        $this->client->setHttpClient(new \GuzzleHttp\Client([
            'verify' => false, // Bypass SSL verification for local development
        ]));

        // Fetch access token using authorization code
        $accessToken = $this->client->fetchAccessTokenWithAuthCode($code);

        // ✅ Check if access token was retrieved successfully
        if (isset($accessToken['error'])) {
            throw new \Exception('Error fetching access token: ' . $accessToken['error_description']);
        }

        // Set the fetched access token
        $this->client->setAccessToken($accessToken);

        \Log::info(json_encode($accessToken));

        // // ✅ Update only if access token is valid
        // if (!empty($accessToken['access_token'])) {
        //     Auth::user()->update(['google_access_token' => json_encode($accessToken)]);
        // } else {
        //     throw new \Exception('Invalid access token received.');
        // }

        // 🔥 Fetch and return all Google Drive files immediately after connection
        return $this->listGoogleDriveFiles($accessToken);
    }

    // List Google Drive Files After Successful Connection
    public function listGoogleDriveFiles($accessToken)
    {
        // Set the access token received
        $this->client->setAccessToken($accessToken);

        // Create Google Drive Service instance
        $driveService = new Drive($this->client);

        // Get files from Google Drive
        $files = $driveService->files->listFiles([
            'pageSize' => 1000, // Fetch all files (optional: increase limit if needed)
            'fields' => 'files(id, name, mimeType, webViewLink, createdTime)',
        ]);

        // Log the files (for debugging)
        \Log::info(json_encode($files->getFiles()));

        // ✅ Return files
        return $files->getFiles();
    }



    // List Files from Google Drive
    public function listFiles()
    {
        $accessToken = json_decode('ya29.a0AeXRPp7XWoHPjoLb2am5Q7XBbQwxgerr9BizTYKIA1FYDkK1j-Dh0NM_U5KcNlrR21iNcCJ74fM7nRz46w-qqJ_ocpImwcgV_LraUH19nr0OWtrLrsFPTUqA0LwminNJnvGJCmwC8vKt42i2HIW4ARCwVvrVAx6bBBvCRynmaCgYKAUYSARESFQHGX2MicvfoYrTknkqyfnprgN0gkA0175', true);

        if (!$accessToken) {
            return [];
        }

        $this->client->setAccessToken($accessToken);

        $driveService = new Drive($this->client);

        $files = $driveService->files->listFiles([
            'pageSize' => 20, // Get 20 files
            'fields' => 'files(id, name, mimeType, webViewLink, createdTime)',
        ]);

        return $files->getFiles();
    }
}
