<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\UserService;
use App\Services\RepService;
use App\Services\DashboardService;
use App\Services\ApiResponseService; // Import API response service
use App\Models\User;
use App\Models\UploadFiles;
use Illuminate\Support\Facades\Mail;
use App\Models\Vendor;
use App\Mail\ProspectMail;
use App\Mail\ClearSignatureMail;
use App\Models\JotForm;
use App\Models\Rep;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class SecureTracerDataController  extends Controller
{
    public function encryptCred()
    {
        $encString = "cburnell24:Summer2024!"; // simulate .env data

        $key = substr(hash('sha256', env('ENCRYPTION_SECRET')), 0, 32);
        $iv = openssl_random_pseudo_bytes(16);

        $encrypted = openssl_encrypt($encString, 'AES-256-CBC', $key, 0, $iv);

        $enc = [
            'cipher' => $encrypted,
            'iv' => base64_encode($iv),
        ];

        return ApiResponseService::success('encrypt', $enc);
    }

    public function decryptCred(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cipher' => 'required|string',
            'iv' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $key = substr(hash('sha256', env('ENCRYPTION_SECRET')), 0, 32);
        $iv = base64_decode($request->iv);

        $decrypted = openssl_decrypt($request->cipher, 'AES-256-CBC', $key, 0, $iv);

        return response()->json([
            'decrypted' => $decrypted,
        ]);
    }
}
