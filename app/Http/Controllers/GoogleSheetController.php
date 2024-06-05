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

        $authUrl = $client->createAuthUrl();

        return redirect($authUrl);

        // $client = new Google_Client();
        // $client->setApplicationName('Google Sheets API Laravel');
        // $client->setScopes(Sheets::SPREADSHEETS_READONLY);
        // $client->setAuthConfig(storage_path('app/google/credentials.json'));
        // $client->setAccessType('offline');

        // $service = new Sheets($client);

        // $spreadsheetId = 'your-spreadsheet-id';
        // $range = 'Sheet1!A1:D10';

        // $response = $service->spreadsheets_values->get($spreadsheetId, $range);
        // $values = $response->getValues();

        // if (empty($values)) {
        //     return response()->json(['message' => 'No data found.']);
        // } else {
        //     return response()->json($values);
        // }
    }

    public function handleGoogleCallback(Request $request)
    {
        $client = new Google_Client();
        $client->setApplicationName('Google Sheets API Laravel');
        $client->setScopes(Sheets::SPREADSHEETS_READONLY);
        $client->setAuthConfig(storage_path('app/google/credentials.json'));
        $client->setAccessType('offline');

        if ($request->input('code')) {
            $client->fetchAccessTokenWithAuthCode($request->input('code'));

            $service = new Sheets($client);

            $spreadsheetId = 'your-spreadsheet-id';
            $range = 'Sheet1!A1:D10';

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
        } else {
            return response()->json(['error' => 'Authorization code not provided.']);
        }
    }
}
