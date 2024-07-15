<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Spatie\DbDumper\Databases\MySql;

class SqlDownloadController extends Controller
{
    public function downloadSql(Request $request)
    {
        $dbHost ='localhost';
        $dbName = 'wb_crm';
        $dbUser ='wb_crm';
        $dbPassword = '1234567890';

        $fileName = 'backup_' . now()->format('Y_m_d_H_i_s') . '.sql';
        $filePath = storage_path('app/sql/' . $fileName);

        if (!Storage::exists('sql')) {
            Storage::makeDirectory('sql');
        }

        try {
            MySql::create()
                ->setDbName($dbName)
                ->setUserName($dbUser)
                ->setPassword($dbPassword)
                ->setHost($dbHost)
                ->dumpToFile($filePath);

            $files = Storage::files('sql');

            if (count($files) > 15) {
                usort($files, function ($a, $b) {
                    return Storage::lastModified($a) - Storage::lastModified($b);
                });

                $filesToDelete = array_slice($files, 0, count($files) - 15);

                Storage::delete($filesToDelete);
            }

            return response()->json(['message' => 'File downloaded and stored successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to download the file: ' . $e->getMessage()], 500);
        }
    }
}
