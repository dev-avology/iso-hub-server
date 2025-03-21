<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\UploadFiles;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class FileService
{
    public function uploadFiles($request, $user_id, $name, $email_id)
    {
        $paths = [];
        // Store each uploaded file
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $storedPath = $file->store('uploads', 'public');
                $original_name = $file->getClientOriginalName();
                $images = [
                    'user_id' => $user_id,
                    'file_path' => asset('storage/' . $storedPath), // Correct path
                    'prospect_name' => $name, // Correct path
                    'file_original_name' => $original_name,
                    'email' => $email_id
                ];
                UploadFiles::create($images);
                $paths[] = asset('storage/' . $storedPath);
            }
            $uploaded_files = array_map(fn($path) => asset($path), $paths);
        }
        return $uploaded_files;
    }

    public function destroyFile($id)
    {
        $file = UploadFiles::findOrFail($id);
        // Get the file path
        $filePath = public_path($file->file_path);
        // Check if the file exists and delete it
        if (File::exists($filePath)) {
            File::delete($filePath);
        }
        // Delete the record from the database
        $file->delete();
        return true;
    }

    public function downloadFile($id)
    {
        $file = UploadFiles::find($id);

        if (!$file || !$file->file_path) {
            return response()->json(['error' => 'File not found'], 404);
        }

        $filePath = public_path($file->file_path);

        if (!file_exists($filePath)) {
            return response()->json(['error' => 'File does not exist on the server'], 404);
        }

        return response()->download($filePath, basename($filePath));
    }
}
