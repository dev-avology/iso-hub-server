<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TeamMember;
use Illuminate\Support\Facades\Validator;
use App\Services\ApiResponseService; // Import API response service
use App\Models\User;
use App\Models\UploadFiles;
use Illuminate\Support\Facades\Mail;
use App\Models\Vendor;
use App\Mail\ProspectMail;
use Illuminate\Support\Facades\Auth;
use App\Services\JotFormService;

class JotFromController extends Controller
{
    protected $JotFormService;

    public function __construct(JotFormService $JotFormService)
    {
        $this->JotFormService = $JotFormService;
    }

    public function createForm(Request $request)
    {
        // Use Validator for detailed error handling
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => '',
            'email' => 'required|string|email|unique:jot_forms,email',
            'phone' => 'required',
            'description' => 'required',
            'signature_date' => 'required',
            'signature' => 'required',
            'unique_string' => 'required'
        ]);

        // Return validation errors if any
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $queryData = $request->unique_string;

        if (!$queryData) {
            return ApiResponseService::error('Missing encrypted data', 400);
        }
        $userId = null;
        try {
            // Decrypt and decode the data from the URL
            $decryptedData = json_decode(decrypt(urldecode($queryData)), true);
            $userId = $decryptedData['user_id'] ?? null;
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return ApiResponseService::error('Invalid encrypted data', 400);
        }

        $form = $this->JotFormService->create($request,$userId);
        return ApiResponseService::success('Forms Created Successfully', $form);
    }
}
