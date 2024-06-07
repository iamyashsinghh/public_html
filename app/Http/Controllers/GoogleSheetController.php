<?php

namespace App\Http\Controllers;

use Google\Service\Sheets;
use Illuminate\Http\Request;
use Google_Client;
use App\Models\BdmLead;
use Google\Service\Sheets\BatchUpdateValuesRequest;

class GoogleSheetController extends Controller
{
    public function getSheetData()
    {
        $client = new Google_Client();
        $client->setApplicationName('Google Sheets API Laravel');
        $client->setScopes(Sheets::SPREADSHEETS);
        $client->setAuthConfig(storage_path('app/google/credentials.json'));
        $client->setAccessType('offline');

        $service = new Sheets($client);

        $spreadsheetId = '1sX5LdXiLjwMftTRnrS2dSkpY3xhTi62XwqG71Dh8zmA';
        $range = 'MakeupLeads!A1:S1000';

        try {
            $response = $service->spreadsheets_values->get($spreadsheetId, $range);
            $values = $response->getValues();

            if (empty($values)) {
                return response()->json(['message' => 'No data found.']);
            } else {
                $headers = array_shift($values);
                $nameIndex = array_search('full_name', $headers);
                $phoneIndex = array_search('phone_number', $headers);
                $cityIndex = array_search('enter_your_location_area_name_(only_within_delhi).', $headers);
                $processedIndex = array_search('Processed', $headers);

                $updatedValues = [];
                $updatedValue = [];
                foreach ($values as $index => $row) {
                    if (isset($row[$processedIndex]) && $row[$processedIndex] === 'Processed') {
                        continue;
                    }

                    $name = $row[$nameIndex] ?? null;
                    $phone = $row[$phoneIndex] ?? null;
                    $city = $row[$cityIndex] ?? null;

                    if ($phone) {
                        $phone = substr($phone, -10);
                    }

                    if ($name && $phone && $city) {
                        // $existingLead = BdmLead::where('phone', $phone)->first();

                        // if (!$existingLead) {
                            // BdmLead::create([
                            //     'name' => $name,
                            //     'phone' => $phone,
                            //     'city' => $city,
                            // ]);
                            $updatedValue[]=[
                                    'name' => $name,
                                    'phone' => $phone,
                                    'city' => $city,
                            ];

                            $updatedValues[] = [
                                'range' => "MakeupLeads!S" . ($index + 2),
                                'values' => [['Processed']]
                            ];
                        // }
                    }
                }

                if (!empty($updatedValues)) {
                    $body = new BatchUpdateValuesRequest([
                        'valueInputOption' => 'RAW',
                        'data' => $updatedValues
                    ]);
                    $service->spreadsheets_values->batchUpdate($spreadsheetId, $body);
                }

                // return response()->json(['message' => 'Data processed and saved successfully.']);
                return response()->json(['message' => $updatedValue]);

            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
