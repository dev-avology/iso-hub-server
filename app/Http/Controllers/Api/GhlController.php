<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\GhlLocation;

class GhlController extends Controller
{
    public function oauthCallback(Request $request)
    {
        $code = $request->query('code');
        $locationId = $request->query('locationId');

        $array = [
            'code' => $code,
            'location_id' => $locationId
        ];

        // Step 1: Exchange code for access token
        $response = Http::post('https://services.leadconnectorhq.com/oauth/token', [
            'client_id' => env('GHL_CLIENT_ID'),
            'client_secret' => env('GHL_CLIENT_SECRET'),
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => route('ghl.oauth.callback'),
        ]);

        $data = $response->json();

        if (isset($data['access_token'])) {
            // Step 2: Save Location and Credentials
            GhlLocation::updateOrCreate(
                ['location_id' => $locationId],
                [
                    'access_token' => $data['access_token'],
                    'refresh_token' => $data['refresh_token'],
                    'expires_in' => now()->addSeconds($data['expires_in']),
                ]
            );

            // Step 3: Redirect to form page
            return redirect()->route('ghl.credentials.form', ['locationId' => $locationId]);
        }

        return response()->json(['error' => 'OAuth failed', 'details' => $array]);
    }

    // GHLController.php
    public function showCredentialsForm($locationId)
    {
        return view('ghl-form', compact('locationId'));
    }
}
