<?php

namespace App\Http\Controllers;

use App\Mail\LoginMail;
use App\Models\Device;
use App\Models\LoginInfo;
use App\Models\Role;
use App\Models\TeamMember;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Jenssegers\Agent\Agent;

class AuthController extends Controller
{
    public function login()
    {
        return view('login');
    }

    public function login_verify(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'phone_number' => "required|digits:10",
            'login_type' => 'required|string|min:4|max:6',
        ]);

        if ($validate->fails()) {
            return response()->json(['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()], 400);
        }

        if ($request->login_type === "team") {
            $user = TeamMember::where('mobile', $request->phone_number)->first();
        } else if ($request->login_type === "vendor") {
            $user = Vendor::where('mobile', $request->phone_number)->first();
        }

        if (!$user) {
            return response()->json(['success' => false, 'alert_type' => 'error', 'message' => 'Invalid credentials.'], 400);
        } else if ($user->status == 0) {
            return response()->json(['success' => false, 'alert_type' => 'error', 'message' => 'Profile is inactive, kindly contact your manager.'], 400);
        }

        if($request->login_type === "team"){
            $role = Role::find($user->role_id);
        $currentTime = date('H:i:s');
        if ($role->is_all_time_login === 0) {
            if ($role->login_start_time && $role->login_end_time) {
                if ($currentTime < $role->login_start_time || $currentTime > $role->login_end_time) {
                    return response()->json(['success' => false, 'alert_type' => 'error', 'message' => 'You are not allowed to login at this time.'], 400);
                }
            }
        }
        }


        $device_id = Cookie::get("device_id_$request->login_type-$user->mobile");
        $datetime = date('Y-m-d H:i:s');
        $cookie_val = md5("$user->mobile-$datetime");
        $can_user_login = 0;

        $verified_device = Device::where(['device_id' => $device_id, 'type' => $request->login_type])->where('team_member_id', $user->id)->first();

        $device = Device::where('type', $request->login_type)->where('team_member_id', $user->id)->first();

        try {
            $verification_code = rand(111111, 999999);
            // $verification_code = 999999;

            $agent = new Agent();
            $browser_name = $agent->browser();
            $browser_version = $agent->version($browser_name);
            $platform = $agent->platform();
            $client_ip = $request->getClientIp();

            $login_info = LoginInfo::where(['login_type' => $request->login_type, 'user_id' => $user->id])->first();

            if (!$device) {
                if ($user->can_add_device === 1) {
                    $device = new Device();
                    $device->team_member_id = $user->id;
                    $device->type = $request->login_type;
                    $device->device_name = "$browser_name Ver:$browser_version /  Platform:$platform";
                    $device->device_id = $cookie_val;
                    if ($device->save()) {
                        $user->can_add_device = 0;
                        $user->save();
                        $can_user_login = 1;
                        Cookie::queue(Cookie::make("device_id_$request->login_type-$user->mobile", $cookie_val, 60 * 24 * 30));
                    }
                }
            } else {
                if ($user->can_add_device === 1) {
                    $device = new Device();
                    $device->team_member_id = $user->id;
                    $device->type = $request->login_type;
                    $device->device_name = "$browser_name Ver:$browser_version / Platform: $platform";
                    $device->device_id = $cookie_val;
                    $device->save();
                    if ($device->save()) {
                        $user->can_add_device = 0;
                        $user->save();
                        $can_user_login = 1;
                        Cookie::queue(Cookie::make("device_id_$request->login_type-$user->mobile", $cookie_val, 60 * 24 * 30));
                    }
                }
                if ($verified_device) {
                    $can_user_login = 1;
                    $verified_device->device_id  = $cookie_val;
                    $verified_device->save();
                    Cookie::queue(Cookie::make("device_id_$request->login_type-$user->mobile", $cookie_val, 60 * 24 * 30));
                }
            }

            if ($can_user_login === 1) {
                if ($login_info) {
                    $last_request_otp_date = date('Y-m-d', strtotime($login_info->request_otp_at));
                    $current_date = date('Y-m-d');
                    if ($current_date > $last_request_otp_date) {
                        $login_info->request_otp_count = 1;
                    } else {
                        $login_info->request_otp_count = $login_info->request_otp_count + 1;
                    }
                } else {
                    $login_info = new LoginInfo();
                    $login_info->user_id = $user->id;
                    $login_info->login_type = $request->login_type;
                    $login_info->request_otp_count = 1;
                }

                $login_info->otp_code = $verification_code;
                $login_info->request_otp_at = date('Y-m-d H:i:s');
                $login_info->ip_address = $client_ip;
                $login_info->browser = "$browser_name Ver:$browser_version";
                $login_info->platform = $platform;
                $login_info->status = 0;
                $login_info->save();

                $this->interakt_wa_msg_send($user->mobile, $user->name, $verification_code, 'login_otp_new');

                if ($user->email != null && env('MAIL_STATUS') === true) {
                    $res_data = ['name' => $user->name, 'otp' => $verification_code];
                    Mail::to($user->email)->send(new LoginMail($res_data));
                }
                return response()->json(['success' => true, 'alert_type' => 'success', 'message' => 'Verification code has been sent to your registered WhatsApp & Email.'], 200);
            } else {
                return response()->json(['success' => false, 'alert_type' => 'error', 'message' => 'Your device is not registed please ask admin for the registration'], 500);
            }
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong. Internal server error.', 'error' => $th->getMessage()], 500);
        }
    }

    public function login_process(Request $request)
    {
        $validate = validator::make($request->all(), [
            'verified_phone_number' => "required|digits:10",
            'verified_login_type' => 'required|string|min:4|max:6',
            'verification_code' => "required|integer|digits:6",
        ]);

        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Invalid credentials, Something went wrong.']);
            return redirect()->back();
        }

        if ($request->verified_login_type === "team") {
            $user = TeamMember::where('mobile', $request->verified_phone_number)->first();
        } else if ($request->verified_login_type === "vendor") {
            $user = Vendor::where('mobile', $request->verified_phone_number)->first();
        }

        if (!$user) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Invalid credentials']);
            return redirect()->back();
        } else if ($user->status == 0) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Profile is inactive, kindly contact to your manager.']);
            return redirect()->back();
        }

        if($request->verified_login_type === "team"){
            $role = Role::find($user->role_id);
        $currentTime = date('H:i:s');
        if ($role->is_all_time_login === 0) {
            if ($role->login_start_time && $role->login_end_time) {
                if ($currentTime < $role->login_start_time || $currentTime > $role->login_end_time) {
                    return response()->json(['success' => false, 'alert_type' => 'error', 'message' => 'You are not allowed to login at this time.'], 400);
                }
            }
        }
        }

        $login_info = LoginInfo::where([
            'login_type' => $request->verified_login_type,
            'user_id' => $user->id,
            'otp_code' => $request->verification_code,
        ])->first();

        if (!$login_info || $login_info == null) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Invalid credentials']);
            return redirect()->back();
        }

        $request_otp_at = date('YmdHis', strtotime($login_info->request_otp_at));
        $ten_minutes_ago = date('YmdHis', strtotime('-10 minutes'));
        if ($request_otp_at < $ten_minutes_ago) {
            if ($login_info !== null) {
                $login_info->otp_code = null;
                $login_info->save();
            }
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Timeout. Please try again.']);
            return redirect()->back();
        }
        $last_login_at = date('Y-m-d', strtotime($login_info->login_at));
        $current_date = date('Y-m-d');
        if ($current_date > $last_login_at) {
            $login_info->login_count = 1;
        } else {
            $login_info->login_count = $login_info->login_count + 1;
        }
        $login_info->otp_code = null;
        $login_info->login_at = date('Y-m-d H:i:s');
        $login_info->status = 1;
        $login_info->token = $request->_token;
        $login_info->logout_at = null;
        $login_info->save();
        if ($request->verified_login_type === "team") {
            if ($user->role_id == 1) {
                Auth::guard('admin')->login($user);
                return redirect()->route('admin.dashboard');
            } else if ($user->role_id == 2) {
                Auth::guard('manager')->login($user);
                return redirect()->route('manager.dashboard');
            } else if ($user->role_id == 3) {
                Auth::guard('nonvenue')->login($user);
                return redirect()->route('nonvenue.dashboard');
            } else if ($user->role_id == 6) {
                Auth::guard('bdm')->login($user);
                return redirect()->route('bdm.dashboard');
            } else if ($user->role_id == 7) {
                Auth::guard('vendormanager')->login($user);
                return redirect()->route('vendormanager.dashboard');
            } else if ($user->role_id == 8) {
                Auth::guard('seomanager')->login($user);
                return redirect()->route('seomanager.dashboard');
            } else {
                Auth::guard('team')->login($user);
                return redirect()->route('team.dashboard');
            }
        } else {
            Auth::guard('vendor')->login($user);
            return redirect()->route('vendor.dashboard');
        }
    }

    public function logout()
    {
        $login_route_name = "login";
        if (Auth::guard('admin')->check()) {
            $guard_authenticated = Auth::guard('admin');
        } else if (Auth::guard('manager')->check()) {
            $guard_authenticated = Auth::guard('manager');
        } else if (Auth::guard('nonvenue')->check()) {
            $guard_authenticated = Auth::guard('nonvenue');
        } else if (Auth::guard('team')->check()) {
            $guard_authenticated = Auth::guard('team');
        } else if (Auth::guard('bdm')->check()) {
            $guard_authenticated = Auth::guard('bdm');
        } else if (Auth::guard('vendormanager')->check()) {
            $guard_authenticated = Auth::guard('vendormanager');
        } else if (Auth::guard('seomanager')->check()) {
            $guard_authenticated = Auth::guard('seomanager');
        } else {
            $guard_authenticated = Auth::guard('vendor');
            $login_route_name = "vendor.login";
        }

        $user = $guard_authenticated->user();
        if (!$user) {
            return redirect()->back();
        }

        if (isset($user->role_id) && $user->role_id !== null) {
            $login_type = "team";
        } else {
            $login_type = "vendor";
        }

        $login_info = LoginInfo::where(['login_type' => $login_type, 'user_id' => $user->id])->first();
        $login_info->logout_at = date('Y-m-d H:i:s');
        $login_info->status = 0;
        $login_info->token = null;
        $login_info->save();

        $guard_authenticated->logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect()->route($login_route_name);
    }

    public function team_login_via_admin($team_id)
    {
        try {
            if (Auth::guard('admin')->hasUser()) {
                $member = TeamMember::find($team_id);
                if ($member->role_id == 2) {
                    Auth::guard('manager')->login($member);
                    return redirect()->route('manager.dashboard');
                } else if ($member->role_id == 3) {
                    Auth::guard('nonvenue')->login($member);
                    return redirect()->route('nonvenue.dashboard');
                } else if ($member->role_id == 7) {
                    Auth::guard('vendormanager')->login($member);
                    return redirect()->route('vendormanager.dashboard');
                } else if ($member->role_id == 8) {
                    Auth::guard('seomanager')->login($member);
                    return redirect()->route('seomanager.dashboard');
                } else if ($member->role_id == 6) {
                    Auth::guard('bdm')->login($member);
                    return redirect()->route('bdm.dashboard');
                } else {
                    Auth::guard('team')->login($member);
                    return redirect()->route('team.dashboard');
                }
            } else {
                session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong.']);
                return redirect()->back();
            }
        } catch (\Throwable $th) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong.']);
            return redirect()->back();
        }
    }

    public function vendor_login_via_admin($vendor_id)
    {
        if (Auth::guard('admin')->hasUser()) {
            $member = Vendor::find($vendor_id);
            Auth::guard('vendor')->login($member);
            return redirect()->route('vendor.dashboard');
        } else {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong.']);
            return redirect()->back();
        }
    }

    public function team_login_via_manager($team_id)
    {
        try {
            if (Auth::guard('manager')->hasUser()) {
                $member = TeamMember::find($team_id);
                if ($member->role_id === 5) {
                    Auth::guard('team')->login($member);
                    return redirect()->route('team.dashboard');
                }
            }
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong.']);
            return redirect()->back();
        } catch (\Throwable $th) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong.']);
            return redirect()->back();
        }
    }

    public function vendor_login_via_vendormanager($team_id)
    {
        try {
            if (Auth::guard('vendormanager')->hasUser()) {
                $member = Vendor::find($team_id);
                Auth::guard('vendor')->login($member);
                return redirect()->route('vendor.dashboard');
            }
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong.']);
            return redirect()->back();
        } catch (\Throwable $th) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong.']);
            return redirect()->back();
        }
    }

    public function mail_test($mail_id)
    {
        // $verification_code = rand(111111, 999999);
        // $res_data = ['name' => "Test Name", 'otp' => $verification_code];
        // Mail::to($mail_id)->send(new LoginMail($res_data));

        // $data = [
        //     'lead_name' => 'Test Name',
        //     'event_name' => 'Wedding',
        //     'event_date' => date('d-M-Y'),
        //     'event_slot' => 'Dinner',
        //     'lead_email' => 'test@gmail.com',
        //     'lead_mobile' => '9988776655',
        // ];
        // Mail::mailer('smtp2')->to($mail_id)->send(new NotifyVendorLead($data));

        // return response()->json(['success' => true, 'message' => 'Mail has been send, check your mail box.']);
    }
}
