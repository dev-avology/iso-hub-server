<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UploadFiles;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class DeleteOldFiles extends Command
{
    protected $signature = 'files:cleanup';
    protected $description = 'Delete files older than 180 days from storage and database';

    public function handle()
    {
        // Get all files older than 180 days
        $oldFiles = UploadFiles::where('created_at', '<', Carbon::now()->subDays(180))->get();

        foreach ($oldFiles as $file) {
            // Extract the storage path
            $filePath = str_replace(asset('storage/'), '', $file->file_path);

            // Delete file from storage
            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
                $this->info("Deleted file: " . $filePath);
            }

            // Delete record from database
            $file->delete();
        }

        $this->info('Old files cleanup completed.');
    }
}
