<?php

namespace App\Http\Controllers;

use Google\Service\Sheets;
use Illuminate\Http\Request;
use Google_Client;
use App\Models\BdmLead;
use App\Models\TeamMember;
use Google\Service\Sheets\BatchUpdateValuesRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GoogleSheetController extends Controller
{
    public function getSheetDataMakeup()
    {
        $client = new Google_Client();
        $client->setApplicationName('Google Sheets API Laravel');
        $client->setScopes(Sheets::SPREADSHEETS);
        $client->setAuthConfig(storage_path('app/google/credentials.json'));
        $client->setAccessType('offline');

        $service = new Sheets($client);

        $spreadsheetId = '1sX5LdXiLjwMftTRnrS2dSkpY3xhTi62XwqG71Dh8zmA';
        $range = 'makeup-leads new!A1:T1000';

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
                $VendorbussinessnameIndex = array_search("what's_your_business_name_?_", $headers);

                $updatedValues = [];
                $updatedValue = [];
                $source = "WB|FBCampaign";
                $current_timestamp = date('Y-m-d H:i:s');
                foreach ($values as $index => $row) {
                    if (isset($row[$processedIndex]) && $row[$processedIndex] === 'Processed') {
                        continue;
                    }

                    $name = $row[$nameIndex] ?? null;
                    $phone = $row[$phoneIndex] ?? null;
                    $city = $row[$cityIndex] ?? null;
                    $bussnesName = $row[$VendorbussinessnameIndex] ?? null;

                    if ($phone) {
                        $phone = substr($phone, -10);
                    }

                    if ($name && $phone && $city) {
                        $updatedValue[] = [
                            'name' => $name,
                            'phone' => $phone,
                            'city' => $city,
                            'bussnesName' => $bussnesName,
                        ];

                        $lead = BdmLead::where('mobile', $phone)->first();
                        if ($lead) {
                            $lead->enquiry_count = $lead->enquiry_count + 1;
                        } else {
                            $lead = new BdmLead();
                            $lead->name = $name;
                            $lead->mobile = $phone;
                            $lead->business_cat = '2';
                            $lead->business_name = $bussnesName;
                            $get_bdm = getAssigningBdm();
                            $lead->assign_to = $get_bdm->name;
                            $lead->assign_id = $get_bdm->id;
                        }
                        $lead->lead_datetime = $current_timestamp;
                        $lead->source = $source;
                        $lead->lead_status = 'Hot';
                        $lead->city = $city;
                        $lead->whatsapp_msg_time = $current_timestamp;
                        $lead->lead_color = "#0066ff33";
                        $lead->save();

                        $updatedValues[] = [
                            'range' => "makeup-leads new!T" . ($index + 2),
                            'values' => [['Processed']]
                        ];
                    }
                }

                if (!empty($updatedValues)) {
                    $body = new BatchUpdateValuesRequest([
                        'valueInputOption' => 'RAW',
                        'data' => $updatedValues
                    ]);
                    $service->spreadsheets_values->batchUpdate($spreadsheetId, $body);
                }

                return response()->json(['message' => 'Data processed successfully.', 'data' => $updatedValue]);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function getSheetDataPhotograher()
    {
        $client = new Google_Client();
        $client->setApplicationName('Google Sheets API Laravel');
        $client->setScopes(Sheets::SPREADSHEETS);
        $client->setAuthConfig(storage_path('app/google/credentials.json'));
        $client->setAccessType('offline');

        $service = new Sheets($client);

        $spreadsheetId = '1sX5LdXiLjwMftTRnrS2dSkpY3xhTi62XwqG71Dh8zmA';
        $range = 'Photoghrapher-Leads!A1:T1000';

        try {
            $response = $service->spreadsheets_values->get($spreadsheetId, $range);
            $values = $response->getValues();

            if (empty($values)) {
                return response()->json(['message' => 'No data found.']);
            } else {
                $headers = array_shift($values);
                $nameIndex = array_search('full_name', $headers);
                $phoneIndex = array_search('enter_phone_number', $headers);
                $cityIndex = array_search('enter_your_location_area_name_(only_within_delhi).', $headers);
                $processedIndex = array_search('Processed', $headers);
                $VendorbussinessnameIndex = array_search("what's_your_business_name_?_", $headers);

                $updatedValues = [];
                $updatedValue = [];
                $source = "WB|FBCampaign";
                $current_timestamp = date('Y-m-d H:i:s');
                foreach ($values as $index => $row) {
                    if (isset($row[$processedIndex]) && $row[$processedIndex] === 'Processed') {
                        continue;
                    }

                    $name = $row[$nameIndex] ?? null;
                    $phone = $row[$phoneIndex] ?? null;
                    $city = $row[$cityIndex] ?? null;
                    $bussnesName = $row[$VendorbussinessnameIndex] ?? null;

                    if ($phone) {
                        $phone = substr($phone, -10);
                    }

                    if ($name && $phone && $city) {
                        $updatedValue[] = [
                            'name' => $name,
                            'phone' => $phone,
                            'city' => $city,
                            'bussnesName' => $bussnesName,
                        ];

                        $lead = BdmLead::where('mobile', $phone)->first();
                        if ($lead) {
                            $lead->enquiry_count = $lead->enquiry_count + 1;
                        } else {
                            $lead = new BdmLead();
                            $lead->name = $name;
                            $lead->mobile = $phone;
                            $lead->business_cat = '1';
                            $lead->business_name = $bussnesName;
                            $get_bdm = getAssigningBdm();
                        $lead->assign_to = $get_bdm->name;
                        $lead->assign_id = $get_bdm->id;
                        }
                        $lead->lead_datetime = $current_timestamp;
                        $lead->source = $source;
                        $lead->lead_status = 'Hot';
                        $lead->city = $city;
                        $lead->whatsapp_msg_time = $current_timestamp;
                        $lead->lead_color = "#0066ff33";
                        $lead->save();

                        $updatedValues[] = [
                            'range' => "Photoghrapher-Leads!T" . ($index + 2),
                            'values' => [['Processed']]
                        ];
                    }
                }

                if (!empty($updatedValues)) {
                    $body = new BatchUpdateValuesRequest([
                        'valueInputOption' => 'RAW',
                        'data' => $updatedValues
                    ]);
                    $service->spreadsheets_values->batchUpdate($spreadsheetId, $body);
                }

                return response()->json(['message' => 'Data processed successfully.', 'data' => $updatedValue]);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function processAllSheetData()
    {
        $makeupResponse = $this->getSheetDataMakeup();
        $photographerResponse = $this->getSheetDataPhotograher();

        return response()->json([
            'makeup_data' => $makeupResponse->getData(),
            'photographer_data' => $photographerResponse->getData()
        ]);
    }


    function getAssigningBdm()
    {
        DB::beginTransaction();
        try {
            $firstRmWithIsNext = TeamMember::where(['role_id' => 6, 'status' => 1, 'is_next' => 1])
                ->orderBy('id', 'asc')
                ->first();
            if ($firstRmWithIsNext) {
                TeamMember::where(['role_id' => 6, 'status' => 1])
                    ->where('id', '!=', $firstRmWithIsNext->id)
                    ->update(['is_next' => 0]);
            }
            if (!$firstRmWithIsNext) {
                $firstRmWithIsNext = TeamMember::where(['role_id' => 6, 'status' => 1])
                    ->orderBy('id', 'asc')
                    ->first();
                if (!$firstRmWithIsNext) {
                    throw new \Exception('No RM available');
                }
                $firstRmWithIsNext->is_next = 1;
                $firstRmWithIsNext->save();
                DB::commit();
                return $firstRmWithIsNext;
            } else {
                $firstRmWithIsNext->is_next = 0;
                $firstRmWithIsNext->save();
                $nextRm = TeamMember::where(['role_id' => 6, 'status' => 1])
                    ->where('id', '>', $firstRmWithIsNext->id)
                    ->orderBy('id', 'asc')
                    ->first();
                if (!$nextRm) {
                    $nextRm = TeamMember::where(['role_id' => 6, 'status' => 1])
                        ->orderBy('id', 'asc')
                        ->first();
                }
                $nextRm->is_next = 1;
                $nextRm->save();
            }
            DB::commit();
            return $firstRmWithIsNext;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}
