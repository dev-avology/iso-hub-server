<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\UserService;
use App\Services\FileService;
use App\Services\DashboardService;
use App\Services\ApiResponseService; // Import API response service
use App\Models\User;
use App\Models\UploadFiles;
use Illuminate\Support\Facades\Mail;
use App\Models\Vendor;
use App\Mail\ProspectMail;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class FileController extends Controller
{
    protected $FileService;
    protected $DashboardService;

    public function __construct(FileService $FileService)
    {
        $this->FileService = $FileService;
    }

    public function uploadFiles(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'files' => 'required',
            'files.*' => 'mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,csv,txt', // Each file max 5MB
            'unique_string' => 'required',
            // 'signature_date' => 'required',
            // 'signature' => 'required'
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
        $email_id = null;        
        try {
            // Decrypt and decode the data from the URL
            $decryptedData = json_decode(decrypt(urldecode($queryData)), true);
            $userId = $decryptedData['user_id'] ?? null;
            $name = $decryptedData['name'] ?? null;
            $form_id = $decryptedData['form_id'] ?? null;
            $personal_guarantee_required = $decryptedData['personal_guarantee_required'] ?? null;
            $clear_signature = $decryptedData['clear_signature'] ?? null;
            $email_id = $decryptedData['email_id'] ?? null;
            
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return ApiResponseService::error('Invalid encrypted data', 400);
        }
        
        $fileUploades = $this->FileService->uploadFiles($request, $userId, $name, $email_id, $form_id, $personal_guarantee_required, $clear_signature);
        $message = 'New document submitted by '.$name;
        if ($fileUploades) {
            $this->FileService->notifyUser($userId,$message);
            return ApiResponseService::success('Files uploaded successfully!', $fileUploades);
        }
        return ApiResponseService::error('No file uploaded', 400);
    }

    public function getProspectFiles($id)
    {
        $user_id = Auth::id();

        if ($id != $user_id) {
            return ApiResponseService::error('Unauthorized user.', 401);
        }

        $files = Auth::user()->hasRole(['admin', 'superadmin'])
            ? UploadFiles::all()
            : UploadFiles::where('user_id', $id)->get();

        return ApiResponseService::success('Files list fetched successfully', $files);
    }


    public function destroyFile($id)
    {
        $file = UploadFiles::find($id);
        if (!$file) {
            return ApiResponseService::error('File not found', 404);
        }
        $this->FileService->destroyFile($id);
        return ApiResponseService::success('File deleted successfully');
    }

    public function downloadFile($id)
    {
        return $this->FileService->downloadFile($id);
    }

    public function checkUniqueString($string)
    {
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

            $userId = $decryptedData['user_id'];
            $personal_guarantee_required = $decryptedData['personal_guarantee_required'] ?? '';
            $clear_signature = $decryptedData['clear_signature'] ?? '';

            $data = [
               'personal_guarantee_required' => $personal_guarantee_required,
               'clear_signature' => $clear_signature
            ];

            // Check if user_id exists in the users table
            $user = User::find($userId);

            if (!$user) {
                return ApiResponseService::error('Invalid encrypted data', 400);
            }

            // Return success with user data if everything is valid
            return ApiResponseService::success('Data verified successfully',$data);
        } catch (\Exception $e) {
            // Handle decryption error or invalid string
            return ApiResponseService::error('Invalid encrypted data format', 400);
        }
    }
}
