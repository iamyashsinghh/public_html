<?php

namespace App\Http\Controllers;

use Google\Service\Sheets;
use Illuminate\Http\Request;
use Google_Client;

class GoogleSheetController extends Controller
{
    public function getSheetData()
    {
        $client = new Google_Client();
        $client->setApplicationName('Google Sheets API Laravel');
        $client->setScopes(Sheets::SPREADSHEETS_READONLY);
        $client->setAuthConfig(storage_path('app/google/credentials.json'));
        $client->setAccessType('offline');

        $service = new Sheets($client);

        $spreadsheetId = '1sX5LdXiLjwMftTRnrS2dSkpY3xhTi62XwqG71Dh8zmA';
        $range = 'Sheet3!A1:R1000';

        try {
            $response = $service->spreadsheets_values->get($spreadsheetId, $range);
            $values = $response->getValues();

            if (empty($values)) {
                return response()->json(['message' => 'No data found.']);
            } else {
                return response()->json($values);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
