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
        $code = $request->query('code');
        $locationId = $request->query('locationId');

        // Step 1: Exchange code for access token (using application/x-www-form-urlencoded)
        $response = Http::asForm()->post('https://services.leadconnectorhq.com/oauth/token', [
            'client_id' => env('GHL_CLIENT_ID'),
            'client_secret' => env('GHL_CLIENT_SECRET'),
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => route('ghl.oauth.callback'),
        ]);

        $data = $response->json();

        if (isset($data['access_token'])) {

            $locationsResponse = Http::withToken($data['access_token'])
                ->get('https://services.leadconnectorhq.com/v1/locations');

            $locations = $locationsResponse->json();

            GhlLocation::updateOrCreate(
                ['location_id' => $locations],
                [
                    'access_token' => $data['access_token'],
                    'refresh_token' => $data['refresh_token'],
                    'expires_in' => now()->addSeconds($data['expires_in']),
                ]
            );

            return redirect()->route('ghl.credentials.form');
        }

        return response()->json(['error' => 'Failed to exchange code for access token', 'details' => $data]);
    }


    public function showCredentialsForm()
    {
        // Show a form or dashboard for the user to configure the location settings
        return view('ghl-form');
    }
}
