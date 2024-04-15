<?php

use App\Models\Availability;
use App\Models\Booking;
use App\Models\LoginInfo;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('update_login_infos', function () {
    try {
        LoginInfo::query()->update(['logout_at' => date('Y-m-d H:i:s'), 'status' => false]);
        echo "login info updated. \n";
    } catch (\Throwable $th) {
        echo $th->getMessage() . "\n";
    }
});

Artisan::command('remove_past_date_availabilities_and_bookings', function () {
    try {
        $previous_month = date('Y-m-d', strtotime('-1 month'));
        Booking::where('created_at', '<', $previous_month)->orderBy('created_at', 'desc')->get();
        Availability::where('date', '<', $previous_month)->orderBy('date', 'desc')->get();

        echo "Data updated successfully. \n";
    } catch (\Throwable $th) {
        echo $th->getMessage() . "\n";
    }
});
