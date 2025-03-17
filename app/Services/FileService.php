<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\UploadFiles;

class FileService
{
    public function uploadFiles($request, $user_id, $name)
    {
        $paths = [];
        // Store each uploaded file
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $storedPath = $file->store('uploads', 'public');
                $images = [
                    'user_id' => $user_id,
                    'file_path' => asset('storage/' . $storedPath), // Correct path
                    'name' => $name // Correct path
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
        // Delete the user
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
