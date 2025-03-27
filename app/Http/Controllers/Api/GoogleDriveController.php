<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GoogleDriveService;
use Illuminate\Http\Request;

class GoogleDriveController extends Controller
{
    protected $googleDriveService;

    public function __construct(GoogleDriveService $googleDriveService)
    {
        $this->googleDriveService = $googleDriveService;
    }

    // Get Google Auth URL to Connect Drive
    public function getAuthUrl()
    {
        $authUrl = $this->googleDriveService->getAuthUrl();

        return response()->json([
            'status' => 'success',
            'auth_url' => $authUrl,
        ]);
    }

    // Handle Google Callback and Save Access Token
    public function handleCallback(Request $request)
    {
        if ($request->has('code')) {
            $data = $this->googleDriveService->handleGoogleCallback($request->code);

            return response()->json([
                'status' => 'success',
                'message' => 'Google Drive connected successfully!',
                'data' => $data
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Failed to connect Google Drive.',
        ], 400);
    }

    // List Google Drive Files for the User
    public function listFiles()
    {
        $files = $this->googleDriveService->listFiles();

        if (empty($files)) {
            return response()->json([
                'status' => 'error',
                'message' => 'No files found or Google Drive is not connected.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'files' => $files,
        ]);
    }
}
