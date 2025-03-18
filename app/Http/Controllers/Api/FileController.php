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

class FileController extends Controller
{
    protected $FileService;
    protected $DashboardService;

    public function __construct(FileService $FileService)
    {
        $this->FileService = $FileService;
    }

    public function fileUploads(Request $request)
    {
        dd('test');
        $request->validate([
            'files' => 'required',
            'files.*' => 'mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,csv,txt', // Each file max 5MB
            'unique_string' => 'required'
        ]);

        $queryData = $request->unique_string;

        if (!$queryData) {
            return ApiResponseService::error('Missing encrypted data', 400);
        }
 
        try {
            // Decrypt and decode the data from the URL
            $decryptedData = json_decode(decrypt(urldecode($queryData)), true);
            $userId = $decryptedData['user_id'] ?? null;
            $name = $decryptedData['name'] ?? null;
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return ApiResponseService::error('Invalid encrypted data', 400);
        } 
         
        $fileUploades = $this->FileService->uploadFiles($request, $userId, $name);

        if ($fileUploades) {
            return ApiResponseService::success('Files uploaded successfully!', $fileUploades);
        }
        return ApiResponseService::error('No file uploaded', 400);
    }

    public function getProspectFiles($id)
    {
        $files = UploadFiles::where('user_id', $id)->get();
        return ApiResponseService::success('Files lists fetched successfully', $files);
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
}
