<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\GhlLocation;
use Illuminate\Support\Facades\Log;

class GhlController extends Controller
{
    public function oauthCallback(Request $request)
    {
        // Ensure the 'code' parameter is present
        $code = $request->query('code');
        if (!$code) {
            return response()->json(['error' => 'Missing authorization code'], 400);
        }

        // Exchange 'code' for the access token
        try {
            $response = Http::timeout(30)->post('https://services.leadconnectorhq.com/oauth/token', [
                'client_id' => env('GHL_CLIENT_ID'),
                'client_secret' => env('GHL_CLIENT_SECRET'),
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => route('ghl.oauth.callback'),
            ]);

            // Check if the response was successful
            if ($response->successful()) {
                $data = $response->json();

                // Retrieve locationId, if available
                $locationId = $data['locationId'] ?? null;

                // Save or update the location with the credentials
                if ($locationId) {
                    GhlLocation::updateOrCreate(
                        ['location_id' => $locationId],
                        [
                            'access_token' => $data['access_token'],
                            'refresh_token' => $data['refresh_token'],
                            'expires_in' => now()->addSeconds($data['expires_in']),
                        ]
                    );
                }

                // Redirect to the credentials form
                return redirect()->route('ghl.credentials.form', ['locationId' => $locationId]);
            } else {
                // Handle errors in the response
                Log::error('OAuth Token Exchange Failed:', $response->json());
                return response()->json(['error' => 'Failed to exchange code for access token', 'details' => $response->json()], 400);
            }
        } catch (\Exception $e) {
            // Handle any exceptions during the request
            Log::error('OAuth Request Error:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'OAuth request failed', 'details' => $e->getMessage()], 500);
        }
    }

    public function showCredentialsForm($locationId)
    {
        // Show a form or dashboard for the user to configure the location settings
        return view('ghl-form', compact('locationId'));
    }
}
