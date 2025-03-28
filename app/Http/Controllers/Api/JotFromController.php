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
use App\Models\JotForm;
use Illuminate\Support\Facades\Auth;
use App\Services\JotFormService;
use App\Services\DashboardService;

class JotFromController extends Controller
{
    protected $JotFormService;
    protected $UserService;
    protected $DashboardService;

    public function __construct(JotFormService $JotFormService, DashboardService $DashboardService)
    {
        $this->JotFormService = $JotFormService;
        $this->DashboardService = $DashboardService;
    }

    public function createForm(Request $request)
    {
        // Use Validator for detailed error handling
        $validator = Validator::make($request->all(), [
            // 'first_name'      => 'required|string|max:255',
            // 'last_name'       => 'nullable|string|max:255',
            'dba'           => 'required|string',
            // 'phone'           => 'required|string|max:15',
            'description'     => 'required|string',
            'address2'     => 'required|string',
            'city'     => 'required|string',
            'state'     => 'required|string',
            'pincode'     => 'required|string',
            'is_same_shipping_address'     => 'required|string',
            'signature_date'  => 'required|date',
            'signature'       => 'required|string',
            'unique_string'   => 'required|string'
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

        $form = $this->JotFormService->create($request, $userId);
        return ApiResponseService::success('Forms Created Successfully', $form);
    }

    public function jotFormcheckUniqueString($string)
    {
        // $encryptedData = encrypt(json_encode(['user_id' => '2', 'secret' => 'jotform_URD_!@#9823_secret$%DEC8901']));
        // dd($encryptedData);

        // Check if the string is provided
        if (!$string) {
            return ApiResponseService::error('Missing encrypted data', 400);
        }

        try {
            // Decrypt and decode the data from the URL
            $decryptedData = json_decode(decrypt(urldecode($string)), true);

            // Check if the decrypted data is valid
            if (!is_array($decryptedData) || !isset($decryptedData['user_id'])) {
                return ApiResponseService::error('Invalid encrypted data', 400);
            }

            // Check if secret verification is required
            if (!isset($decryptedData['secret']) || $decryptedData['secret'] !== 'jotform_URD_!@#9823_secret$%DEC8901') {
                return ApiResponseService::error('Invalid encrypted data', 400);
            }

            $userId = $decryptedData['user_id'];
            // Check if user_id exists in the users table
            $user = User::find($userId);

            if (!$user) {
                return ApiResponseService::error('Invalid encrypted data', 400);
            }

            // Return success with user data if everything is valid
            return ApiResponseService::success('Data verified successfully');
        } catch (\Exception $e) {
            // Handle decryption error or invalid string
            return ApiResponseService::error('Invalid encrypted data format', 400);
        }
    }

    public function getFormsList(Request $request)
    {
        $permission = 'jotform.view';
        $userPermission = $this->DashboardService->checkPermission($permission);

        if (!empty($userPermission)) {
            return $userPermission;
        }

        $query = JotForm::query();

        if ($request->id) {
            $query->where('id', $request->id);
        }
        $jotforms = $query->get();
        return ApiResponseService::success('Jotfrom lists fetched successfully', $jotforms);
    }

    public function getFromDetails($id)
    {
        $permission = 'jotform.view';
        $userPermission = $this->DashboardService->checkPermission($permission);

        if (!empty($userPermission)) {
            return $userPermission;
        }

        $query = JotForm::query();

        if ($id) {
            $query->where('id', $id);
        }
        $jotforms = $query->get();
        return ApiResponseService::success('Jotfrom details successfully', $jotforms);
    }
}
