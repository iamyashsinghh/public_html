<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EnvController extends Controller
{
    public function editEnv()
    {
        $envValues =json_decode(json_encode([
            'MAIL_MAILER' => env('MAIL_MAILER'),
            'MAIL_HOST' => env('MAIL_HOST'),
            'MAIL_PORT' => env('MAIL_PORT'),
            'MAIL_EHLO_DOMAIN' => env('MAIL_EHLO_DOMAIN'),
            'MAIL_USERNAME' => env('MAIL_USERNAME'),
            'MAIL_PASSWORD' => env('MAIL_PASSWORD'),
            'MAIL_ENCRYPTION' => env('MAIL_ENCRYPTION'),
            'MAIL_STATUS' => filter_var(env('MAIL_STATUS', 'false'), FILTER_VALIDATE_BOOLEAN),
            'MAIL_FROM_ADDRESS' => env('MAIL_FROM_ADDRESS'),
            'SMTP2_MAIL_USERNAME' => env('SMTP2_MAIL_USERNAME'),
            'SMTP2_MAIL_PASSWORD' => env('SMTP2_MAIL_PASSWORD'),
            'MAIL_FROM_NAME' => env('MAIL_FROM_NAME'),
            'SMTP2_MAIL_FROM_ADDRESS' => env('SMTP2_MAIL_FROM_ADDRESS'),
            'TATA_WHATSAPP_MSG_STATUS' => filter_var(env('TATA_WHATSAPP_MSG_STATUS', 'false'), FILTER_VALIDATE_BOOLEAN),
            'TATA_AUTH_KEY' => env('TATA_AUTH_KEY'),
            'OPENAI_API_KEY' => env('OPENAI_API_KEY'),
        ]));
        return view('admin.env.manage', compact('envValues'));
    }

    public function updateEnv(Request $request)
    {
        $data = $request->except(['_token']);
        $envPath = base_path('.env');
        if (file_exists($envPath)) {
            $env = file_get_contents($envPath);
            foreach ($data as $key => $value) {
                if($key == 'MAIL_FROM_NAME'){
                    $env = preg_replace("/^{$key}=.*$/m", "{$key}=\"{$value}\"", $env);
                }else{
                    $env = preg_replace("/^{$key}=.*$/m", "{$key}={$value}", $env);
                }
            }
            file_put_contents($envPath, $env);
        }
        return back()->with('success', 'Environment settings updated successfully.');
    }

}
