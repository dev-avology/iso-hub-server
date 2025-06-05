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
use ZipArchive;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
            return ApiResponseService::error('Missing data', 400);
        }

        $userId = null;
        $email_id = null;
        try {
            $parts = explode('&', $queryData);

            // Access individual parts
            $user_id = $parts[0]; // 2
            $clear_signature = $parts[1]; // clearance
            $personal_guarantee_required = $parts[2]; // yes
            $form_id = $parts[3]; // yes
            $email = $parts[4]; // yes

            // Decrypt and decode the data from the URL
            $userId = $user_id ?? null;
            $name = null;
            $form_id = $form_id ?? null;
            $personal_guarantee_required = $personal_guarantee_required ?? null;
            $clear_signature = $clear_signature ?? null;
            $email_id = $email ?? null;

        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return ApiResponseService::error('Invalid encrypted data', 400);
        }

        $fileUploades = $this->FileService->uploadFiles($request, $userId, $name, $email_id, $form_id, $personal_guarantee_required, $clear_signature);
        $message = 'New document submitted by ' . $email_id;
        if ($fileUploades) {
            $this->FileService->notifyUser($userId, $message);
            return ApiResponseService::success('Files uploaded successfully!', $fileUploades);
        }
        return ApiResponseService::error('No file uploaded', 400);
    }

    public function uploadUserFiles(Request $request)
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
            return ApiResponseService::error('Missing data', 400);
        }

        $userId = null;
        $email_id = null;
        try {
            $parts = explode('&', $queryData);

            // Access individual parts
            $user_id = $parts[0]; // 2
            $name = $parts[1]; // clearance
            $email = $parts[2]; // yes
        

            // Decrypt and decode the data from the URL
            $userId = $user_id ?? null;
            $name = $name ?? null;
            $email_id = $email ?? null;

        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return ApiResponseService::error('Invalid encrypted data', 400);
        }

        $fileUploades = $this->FileService->uploadUserFiles($request, $userId, $name, $email_id);
        $message = 'New document submitted by ' . $email_id;
        if ($fileUploades) {
            $this->FileService->notifyUser($userId, $message);
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
            ? UploadFiles::orderBy('created_at', 'desc')->get()
            : UploadFiles::where('user_id', $id)->orderBy('created_at', 'desc')->get();


        return ApiResponseService::success('Files list fetched successfully', $files);
    }

    public function getUserFiles($id)
    {
        $user_id = Auth::id();

        if ($id != $user_id) {
            return ApiResponseService::error('Unauthorized user.', 401);
        }

        $files = Auth::user()->hasRole(['admin', 'superadmin'])
        ? UploadFiles::where(function($query) {
            $query->whereNull('form_id')
                ->orWhere('form_id', '');
        })->orderBy('created_at', 'desc')->get()
        : UploadFiles::where(function($query) use ($id) {
            $query->whereNull('form_id')
                ->orWhere('form_id', '');
        })->where('user_id', $id)
        ->orderBy('created_at', 'desc')->get();


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
            return ApiResponseService::error('Missing data', 400);
        }

        try {
            // Decrypt and decode the data from the URL
            // $decryptedData = json_decode(decrypt(urldecode($string)), true);

            // // Check if the decrypted data is valid
            // if (!is_array($decryptedData) || !isset($decryptedData['user_id'])) {
            //     return ApiResponseService::error('Invalid encrypted data', 400);
            // }

            $parts = explode('&', $string);

            // Access individual parts
            $user_id = $parts[0]; // 2
            $clear_signature = $parts[1]; // clearance
            $personal_guarantee_required = $parts[2]; // yes
            $form_id = $parts[3]; // yes
            $email = $parts[4]; // yes

            $userId = $user_id;
            $personal_guarantee_required = $personal_guarantee_required ?? '';
            $clear_signature = $clear_signature ?? '';
            $form_id = $form_id ?? '';
            $email = $email ?? '';

            $data = [
                'personal_guarantee_required' => $personal_guarantee_required,
                'clear_signature' => $clear_signature,
                'form_id' => $form_id,
                'email' => $email,
            ];

            // Check if user_id exists in the users table
            $user = User::find($userId);

            if (!$user) {
                return ApiResponseService::error('Invalid encrypted data', 400);
            }

            // Return success with user data if everything is valid
            return ApiResponseService::success('Data verified successfully', $data);
        } catch (\Exception $e) {
            // Handle decryption error or invalid string
            return ApiResponseService::error('Invalid encrypted data format', 400);
        }
    }

      public function checkUniqueStringForUser($string)
    {
        // Check if the string is provided
        if (!$string) {
            return ApiResponseService::error('Missing data', 400);
        }

        try {
            // Decrypt and decode the data from the URL
            // $decryptedData = json_decode(decrypt(urldecode($string)), true);

            // // Check if the decrypted data is valid
            // if (!is_array($decryptedData) || !isset($decryptedData['user_id'])) {
            //     return ApiResponseService::error('Invalid encrypted data', 400);
            // }

            $parts = explode('&', $string);

            // Access individual parts
            $user_id = $parts[0]; // 2
            $name = $parts[1]; // clearance
            $email = $parts[2]; // yes

            $userId = $user_id;
            $name = $name ?? '';
            $email = $email ?? '';

            $data = [
                'userId' => $userId,
                'name' => $name,
                'email' => $email,
            ];

            // Check if user_id exists in the users table
            $user = User::find($userId);

            if (!$user) {
                return ApiResponseService::error('Invalid encrypted data', 400);
            }

            // Return success with user data if everything is valid
            return ApiResponseService::success('Data verified successfully', $data);
        } catch (\Exception $e) {
            // Handle decryption error or invalid string
            return ApiResponseService::error('Invalid encrypted data format', 400);
        }
    }

    public function downloadZipFile($id)
    {
        $files = UploadFiles::where('form_id', $id)->get();

        if ($files->isEmpty()) {
            return response()->json(['error' => 'No files found'], 404);
        }

        $zip = new \ZipArchive;
        $zipName = 'jotform_' . $id . '_files.zip';

        // Create a folder inside storage to store the temporary zip file
        $tempDirectory = storage_path('app/public/zip_files');  // Path where the zip file will be stored

        // Check if the directory exists, if not, create it
        if (!file_exists($tempDirectory)) {
            mkdir($tempDirectory, 0777, true);  // Create the directory if it doesn't exist
        }

        // Full path for the ZIP file
        $tempFile = $tempDirectory . DIRECTORY_SEPARATOR . $zipName;

        if ($zip->open($tempFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {

            foreach ($files as $file) {
                $modifiedString = str_replace('/storage/uploads/', '', $file->file_path);
                $filePath = public_path('storage/uploads/' . ltrim($modifiedString, '/'));

                // Check if the file exists before adding to the ZIP
                if (file_exists($filePath)) {
                    $zip->addFile($filePath, basename($filePath));
                } else {
                    \Log::error("File not found: " . $filePath); // Log missing file path for debugging
                }
            }

            // Close the zip file
            $zip->close();

            // Check if the zip file was created successfully
            if (file_exists($tempFile)) {
                // Trigger the download of the zip file
                return response()->download($tempFile, $zipName, [
                    'Content-Type' => 'application/zip',
                    'Content-Disposition' => 'attachment; filename="' . $zipName . '"'
                ])->deleteFileAfterSend(true);
            } else {
                \Log::error("Failed to create the zip file: " . $tempFile); // Log if the file doesn't exist
                return response()->json(['error' => 'Failed to create the zip file'], 500);
            }
        } else {
            return response()->json(['error' => 'Unable to create zip archive'], 500);
        }
    }
}
