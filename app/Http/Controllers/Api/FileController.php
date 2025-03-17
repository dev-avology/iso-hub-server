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

    public function uploadFiles(Request $request)
    {
        $request->validate([
            'files' => 'required',
            'files.*' => 'mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,csv,txt', // Each file max 5MB
            'unique_string' => 'required'
        ]);

        // Decrypt and decode the data
        $decryptedData = json_decode(decrypt($request->unique_string), true);
        $userId = $decryptedData['user_id'] ?? null;
        $name = $decryptedData['name'] ?? null;

        // Check if the unique_string exists for the user in the database
        $user = User::where('id', $userId)
            ->where('unique_string', $request->unique_string)
            ->first();

        if (!$user) {
            return ApiResponseService::error('Invalid unique string for this user', 400);
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
