<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\UploadFiles; // Change this to your actual model name
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class DeleteOldFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    public function __construct()
    {
        parent::__construct();
    }
    protected $signature = 'app:delete-old-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete files that are older than 180 days from storage and database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get the date 180 days ago
        $date = Carbon::now()->subDays(180);

        // Fetch files older than 180 days
        $oldFiles = UploadFiles::where('updated_at', '<', $date)->get();

        foreach ($oldFiles as $file) {
            // Delete file from storage
            if (Storage::exists($file->file_path)) {
                Storage::delete($file->file_path);
            }

            // Delete record from database
            $file->delete();
        }

        $this->info(count($oldFiles) . ' old files deleted successfully.');
    }
}
