<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UploadFiles;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class DeleteOldFiles extends Command
{
    protected $signature = 'files:cleanup';
    protected $description = 'Delete files older than 180 days from storage and database';

    public function handle()
    {
        // Get all files older than 180 days
        $oldFiles = UploadFiles::where('created_at', '<', Carbon::now()->subDays(1))->get();

        foreach ($oldFiles as $file) {
            $filePath = public_path($file->file_path);
            // Check if the file exists and delete it
            if (File::exists($filePath)) {
                File::delete($filePath);
            }
            // Delete record from database
            $file->delete();
            Log::info('yes deleted file by cron job');
        }
        Log::info('yes not delted file by cron job');
    }
}
