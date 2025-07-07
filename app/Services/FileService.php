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
use Illuminate\Support\Facades\Mail;
use App\Mail\NotifyUserMail;
use App\Models\JotForm;
use App\Models\Notification;
use App\Notifications\UserFileUploaded;
// use Illuminate\Support\Facades\Notification;

class FileService
{
    public function uploadFiles($request, $user_id, $name, $email_id,$form_id, $personal_guarantee_required, $clear_signature)
    {
        $paths = [];
        // Store each uploaded file
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $storedPath = $file->store('uploads', 'public');
                $original_name = $file->getClientOriginalName();
                $images = [
                    'user_id' => $user_id,
                    'form_id' => $form_id,
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

        $jotform_data = [
            'signature' => $request->signature,
            'signature_date' => $request->signature_date,
            'personal_guarantee_required' => $personal_guarantee_required,
            'clear_signature' => $clear_signature,
            'mail_status' => 2
        ];

       $form = JotForm::find($form_id);
       $form->update($jotform_data);
       return $uploaded_files;
    }

    public function uploadUserFiles($request, $user_id, $name, $email_id)
    {
        $paths = [];

        $user = User::find($user_id);
        $created_by_id = null;
        if($user){
           $created_by_id = $user->created_by_id;
        }
        // Store each uploaded file
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $storedPath = $file->store('uploads', 'public');
                $original_name = $file->getClientOriginalName();
                $images = [
                    'user_id' => $user_id,
                    'created_by_id' => $created_by_id,
                    'form_id' => null,
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

        // $jotform_data = [
        //     'signature' => $request->signature,
        //     'signature_date' => $request->signature_date,
        //     'personal_guarantee_required' => null,
        //     'clear_signature' => null,
        //     'mail_status' => 2
        // ];

    //    $form = JotForm::find($form_id);
    //    $form->update($jotform_data);
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

    // public function notifyUser($user_id, $message)
    // {
    //     $user = User::where('id', $user_id)->first();
    //     $name = "";
    //     if ($user) {
    //         $name = $user->first_name;
    //     }
    //     $data = [
    //         'name' => $name,
    //         'message' => $message
    //     ];

    //     Mail::to($user->email)->send(new NotifyUserMail($data));

    //     // Increase notify_count for the main user
    //     if ($user && !in_array($user->role_id,[1,2])) {
    //         $user->increment('notify_count');
    //     }
    //     $adminAndSuperadminIds = User::whereIn('role_id', [1, 2])->pluck('id');

    //     User::whereIn('id', $adminAndSuperadminIds)->each(function ($adminUser) {
    //         $adminUser->increment('notify_count');
    //     });

    //     $noti_data = ['user_id' => $user_id, 'message' => $message];
    //     Notification::create($noti_data );
    //     return true;
    // }


    public function notifyUser($user_id, $message)
    {
        $user = User::find($user_id);
        if (!$user) return false;

        // Notify the original user
        $user->notify(new UserFileUploaded($message));
        $user->increment('notify_count');

        // Create database notification for the original user
        Notification::create([
            'user_id' => $user->id,
            'message' => $message,
        ]);

        // Get all parent users recursively
        $parentUsers = $this->getAllParentUsers($user);

        foreach ($parentUsers as $parentUser) {
            $parentUser->notify(new UserFileUploaded($message));
            // \Log::info($parentUser);
            $parentUser->increment('notify_count');

            // Create database notification
            Notification::create([
                'user_id' => $parentUser->id,
                'message' => $message,
            ]);
        }
        return true;
    }


    private function getAllParentUsers($user)
    {
        $parents = [];

        while ($user && $user->created_by_id) {
            $parent = User::find($user->created_by_id);
            if (!$parent) break;

            $parents[] = $parent;
            $user = $parent; // Move one level up
        }

        return $parents;
    }

}
