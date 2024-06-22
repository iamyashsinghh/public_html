<?php
// app/Http/Controllers/SqlDownloadController.php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Spatie\DbDumper\Databases\MySql;

class SqlDownloadController extends Controller
{
    public function downloadSql(Request $request)
    {
        // Database connection details
        $dbHost ='localhost';
        $dbName = 'wb_crm';
        $dbUser ='wb';
        $dbPassword = '1234567890';

        // Generate a unique file name
        $fileName = 'backup_' . now()->format('Y_m_d_H_i_s') . '.sql';
        $filePath = storage_path('app/sql/' . $fileName);

        // Create the storage directory if it doesn't exist
        if (!Storage::exists('sql')) {
            Storage::makeDirectory('sql');
        }

        // Use spatie/db-dumper to create the database dump
        try {
            MySql::create()
                ->setDbName($dbName)
                ->setUserName($dbUser)
                ->setPassword($dbPassword)
                ->setHost($dbHost)
                ->dumpToFile($filePath);

            // Get all files in the storage/app/sql folder
            $files = Storage::files('sql');

            // If there are more than 15 files, delete the oldest ones
            if (count($files) > 15) {
                // Sort files by last modified time in ascending order
                usort($files, function ($a, $b) {
                    return Storage::lastModified($a) - Storage::lastModified($b);
                });

                // Get the files to be deleted (all but the latest 15)
                $filesToDelete = array_slice($files, 0, count($files) - 15);

                // Delete the old files
                Storage::delete($filesToDelete);
            }

            return response()->json(['message' => 'File downloaded and stored successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to download the file: ' . $e->getMessage()], 500);
        }
    }
}
