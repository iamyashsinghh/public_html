<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::view('/', 'admin.login');
Route::get('/fool', function () {
    Artisan::call('storage:link');
});
Route::post('/store-token', [Controllers\NotificationSendController::class, 'updateDeviceToken'])->name('store.token');
Route::get('/sendfcm', [Controllers\NotificationSendController::class, 'hi'])->name('send.token');

Route::get('/optimize', function () {
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    return redirect()->route('admin.editEnv');
})->name('admin.optimize.crm');

// send and get
Route::get('admin/ajax_tasks', [Controllers\WhatsappMsgController::class, 'ajax_tasks'])->name('whatsapp_chat.ajax');
Route::get('admin/ajax_templates', [Controllers\WhatsappMsgController::class, 'fetchTemplates'])->name('whatsapp_chat.ajax_templates');

Route::get('whatsapp_chat/{id}', [Controllers\WhatsappMsgController::class, 'whatsapp_msg_get'])->name('whatsapp_chat.get');
Route::get('whatsapp_chat_new/{id}', [Controllers\WhatsappMsgController::class, 'whatsapp_msg_get_new'])->name('whatsapp_chat.get_new');
Route::post('whatsapp_msg_send', [Controllers\WhatsappMsgController::class, 'whatsapp_msg_send'])->name('whatsapp_chat.send');
// create task
Route::post('create_task_by_number', [Controllers\WhatsappMsgController::class, 'create_task_by_number'])->name('whatsapp_chat.create_task_by_number');
Route::post('create_task_by_id', [Controllers\WhatsappMsgController::class, 'create_task_by_id'])->name('whatsapp_chat.create_task_by_id');
// send msg
Route::get('whatsapp_msg_send_multiple', [Controllers\WhatsappMsgController::class, 'whatsapp_msg_send_multiple'])->name('whatsapp_chat.send_multi');
// send hey msg or hi
Route::post('whatsapp_msg_send_hey', [Controllers\WhatsappMsgController::class, 'whatsapp_msg_send_hey'])->name('whatsapp_chat.send.hey');
Route::post('whatsapp_msg_send_hello', [Controllers\WhatsappMsgController::class, 'whatsapp_msg_send_hello'])->name('whatsapp_chat.send.hello');
// send a hi lead_name i am rm name i am your wedding panner assistant
Route::post('whatsapp_msg_send_greet_btn', [Controllers\WhatsappMsgController::class, 'whatsapp_msg_send_greet_btn'])->name('whatsapp_chat.send.greet_btn');

// update status
Route::post('whatsapp_msg_status', [Controllers\WhatsappMsgController::class, 'whatsapp_msg_status'])->name('whatsapp_chat.status');
Route::post('whatsapp_msg_status_vendor', [Controllers\WhatsappMsgController::class, 'whatsapp_msg_status_vendor'])->name('whatsapp_chat.status_nv');
Route::post('whatsapp_msg_status_nv_team', [Controllers\WhatsappMsgController::class, 'whatsapp_msg_status_nv_team'])->name('whatsapp_chat.status_nv_team');
Route::post('whatsapp_msg_status_bdm', [Controllers\WhatsappMsgController::class, 'whatsapp_msg_status_bdm'])->name('whatsapp_chat.status.bdm');



Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::group(['middleware' => 'AuthCheck'], function () {
    Route::get('/', [AuthController::class, 'login'])->name('login');
    Route::view('/vendor', 'vendor.vendor_login')->name('vendor.login');
    Route::post('/login/verify', [AuthController::class, 'login_verify'])->name('login.verify');
    Route::post('/login/process', [AuthController::class, 'login_process'])->name('login.process');
});

Route::get('venue-lead/phone-number/validate/{number?}', [Controller::class, 'validate_venue_lead_phone_number'])->name('venue.lead.phoneNumber.validate');
Route::get('nonvenue-lead/phone-number/validate/{number?}', [Controller::class, 'validate_nonvenue_lead_phone_number'])->name('nonvenue.lead.phoneNumber.validate');
Route::get('bdm-lead/phone-number/validate/{number?}', [Controller::class, 'validate_bdm_lead_phone_number'])->name('bdm.lead.phoneNumber.validate');


Route::get('/notify_vendor_lead_mail', function () {
    $data = ['lead_name' => 'Hello lead', 'event_name' => 'Hello Event', 'event_date' => 'Hello event date', 'event_slot' => 'Hello event slot', 'lead_email' => 'Hello lead email', 'lead_mobile' => 'Hello lead mobile'];
    return view('mail.notify_vendor_lead', compact('data'));
});
Route::get('/login_mail', function () {
    $member = ['name' => 'Hello lead', 'otp' => 'Hello Event', 'event_date' => 'Hello event date', 'event_slot' => 'Hello event slot', 'lead_email' => 'Hello lead email', 'lead_mobile' => 'Hello lead mobile'];
    return view('mail.login', compact('member'));
});



/*
|--------------------------------------------------------------------------
| For Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware('verify_token')->group(function () {
    Route::post('/bookings/manage_process/{booking_id}', [Controllers\Admin\BookingController::class, 'manage_process'])->name('booking.manage_process');
    Route::get('/bookings/fetch/{booking_id}', [Controllers\Admin\BookingController::class, 'fetch_booking'])->name('booking.fetch');
    Route::get('/vm_event/fetch/{event_id?}', [Controllers\Admin\BookingController::class, 'fetch_vm_event'])->name('vm_event.fetch');

    Route::prefix('/admin')->middleware('admin')->group(function () {
        Route::get('/dashboard', [Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');

        //Team member Routes
        Route::prefix('/venue-crm')->group(function () {
            Route::get('/team', [Controllers\Admin\TeamMemberController::class, 'list'])->name('admin.team.list');
            Route::get('/team/ajax_list', [Controllers\Admin\TeamMemberController::class, 'ajax_list'])->name('admin.team.list.ajax');
            Route::get('/team/new', [Controllers\Admin\TeamMemberController::class, 'manage'])->name('admin.team.new');
            Route::get('/team/edit/{member_id?}', [Controllers\Admin\TeamMemberController::class, 'manage'])->name('admin.team.edit');
            Route::get('/team/view/{member_id?}', [Controllers\Admin\TeamMemberController::class, 'view'])->name('admin.team.view');
            Route::post('/team/manage-process/{member_id}', [Controllers\Admin\TeamMemberController::class, 'manage_process'])->name('admin.team.manage.process');
            Route::get('/team/update-status/{member_id?}/{status?}', [Controllers\Admin\TeamMemberController::class, 'update_status'])->name('admin.team.update.status');
            Route::get('/team/delete/{member_id?}', [Controllers\Admin\TeamMemberController::class, 'delete'])->name('admin.team.delete');
            Route::post('/team/update-profile-image/{member_id?}', [Controllers\Admin\TeamMemberController::class, 'update_profile_image'])->name('admin.team.updateProfileImage');
            Route::get('/team_login_info', [Controllers\Admin\TeamMemberController::class, 'get_member_login_info'])->name('admin.team.login_info');

            //Role Routes
            Route::get('/roles', [Controllers\Admin\RoleController::class, 'list'])->name('admin.role.list');
            Route::get('/roles/manage_ajax/{role_id?}', [Controllers\Admin\RoleController::class, 'manage_ajax'])->name('admin.role.edit');
            Route::post('/roles/manage-process/{role_id?}', [Controllers\Admin\RoleController::class, 'manage_process'])->name('admin.role.manage.process');

            //Lead Routes
            Route::match (['get', 'post'], '/leads', [Controllers\Admin\LeadController::class, 'list'])->name('admin.lead.list');
            Route::get('/leads/ajax_list', [Controllers\Admin\LeadController::class, 'ajax_list'])->name('admin.lead.list.ajax');
            Route::get('/leads/get_forward_info/{lead_id?}', [Controllers\Admin\LeadController::class, 'get_forward_info'])->name('admin.lead.getForwardInfo');
            Route::get('/leads/view/{lead_id?}', [Controllers\Admin\LeadController::class, 'view'])->name('admin.lead.view');
            // Route::get('leads/manage_ajax/{lead_id?}', [Controllers\Admin\LeadController::class, 'manage_ajax'])->name('admin.lead.edit');
            Route::get('/leads/delete/{lead_id?}', [Controllers\Admin\LeadController::class, 'delete'])->name('admin.lead.delete');
            Route::post('/leads/add-process', [Controllers\Admin\LeadController::class, 'add_process'])->name('admin.lead.add.process');
            Route::post('/leads/edit-process/{lead_id}', [Controllers\Admin\LeadController::class, 'edit_process'])->name('admin.lead.edit.process');
            Route::post('/leads/forward', [Controllers\Admin\LeadController::class, 'lead_forward'])->name('admin.lead.forward');
            Route::post('/leads/nvrmforward', [Controllers\Admin\LeadController::class, 'lead_forward_nvrm'])->name('admin.lead.forwardnvrm');
            //this route for filters. it is same as get route
            // Route::post('/leads', [Controllers\Admin\LeadController::class, 'list'])->name('admin.lead.list');

            //Party area routes
            Route::post('party-area/manage-process/{area_id?}', [Controllers\Admin\PartyAreaController::class, 'manage_process'])->name('admin.partyArea.manage.process');
            Route::get('party-area/delete/{area_id}', [Controllers\Admin\PartyAreaController::class, 'delete'])->name('admin.partyArea.delete');

            //Food preference routes
            Route::post('food-preference/manage-process/{id?}', [Controllers\Admin\FoodPreferenceController::class, 'manage_process'])->name('admin.foodPreference.manage.process');
            Route::get('food-preference/delete/{id}', [Controllers\Admin\FoodPreferenceController::class, 'delete'])->name('admin.foodPreference.delete');

            //Bypass login: Via admin to team crm
            Route::get('/bypass-login/{team_id?}', [AuthController::class, 'team_login_via_admin'])->name('admin.bypass.login');

            //Availability list
            Route::get('/availability/list', [Controllers\Admin\AvailabilityController::class, 'list'])->name('admin.availability.list');

            //Booking routes
            Route::match (['get', 'post'], '/bookings/list/{dashboard_filters?}', [Controllers\Admin\BookingController::class, 'list'])->name('admin.bookings.list');
            Route::get('/bookings/ajax_list/', [Controllers\Admin\BookingController::class, 'ajax_list'])->name('admin.bookings.list.ajax');
            Route::get('booking/delete/{booking_id}', [Controllers\Admin\BookingController::class, 'delete'])->name('booking.delete');

            //VM Productivity
            Route::post('/vm_productivity/manage_process', [Controllers\Admin\VmProductivityController::class, 'manage_process'])->name('vm_productivity.manage_process');
        });

        Route::prefix('nonvenue-crm')->group(function () {
            //Vendor routes
            Route::get('/vendors/edit', [Controllers\Admin\VendorController::class, 'listedit'])->name('admin.vendor.list.edit');
            Route::get('/vendors', [Controllers\Admin\VendorController::class, 'list'])->name('admin.vendor.list');
            Route::get('/vendors/ajax_list/{vendor_cat_id}', [Controllers\Admin\VendorController::class, 'ajax_list'])->name('admin.vendor.list.ajax');
            Route::post('/vendors/vendor_list_update', [Controllers\Admin\VendorController::class, 'vendor_list_update'])->name('admin.vendor.vendorlistupdate');
            Route::get('/vendors/manage_ajax/{id?}', [Controllers\Admin\VendorController::class, 'manage_ajax'])->name('admin.vendor.edit');
            Route::post('/vendors/manage-process/{id?}', [Controllers\Admin\VendorController::class, 'manage_process'])->name('admin.vendor.manage.process');
            Route::get('/vendors/update-status/{vendor_id?}/{status?}', [Controllers\Admin\VendorController::class, 'update_status'])->name('admin.vendor.update.status');
            Route::get('/vendors/delete/{vendor_id?}', [Controllers\Admin\VendorController::class, 'delete'])->name('admin.vendor.delete');
            Route::post('/vendors/update-profile-image/{vendor_id?}', [Controllers\Admin\VendorController::class, 'update_profile_image'])->name('admin.vendor.updateProfileImage');

            //Vendor category routes
            Route::get('/vendor-categories', [Controllers\Admin\VendorCategoryController::class, 'list'])->name('admin.vendorCategory.list');
            Route::post('/vendor-categories/manage-process/{id?}', [Controllers\Admin\VendorCategoryController::class, 'manage_process'])->name('admin.vendorCategory.manage.process');

            // vendor locality routes
            //Vendor category routes
            Route::get('/vendor-localities', [Controllers\Admin\VendorLocalityController::class, 'list'])->name('admin.vendorLocality.list');
            Route::post('/vendor-localities/manage-process/{id?}', [Controllers\Admin\VendorLocalityController::class, 'manage_process'])->name('admin.vendorLocality.manage.process');

            //NvLead Routes
            Route::match (['get', 'post'], '/nv-leads', [Controllers\Admin\NvLeadController::class, 'list'])->name('admin.nvlead.list');
            Route::get('/nv-leads/ajax_list', [Controllers\Admin\NvLeadController::class, 'ajax_list'])->name('admin.nvlead.list.ajax');
            // Route::get('/nv-leads/manage_ajax/{id}', [Controllers\Admin\NvLeadController::class, 'ajax_list'])->name('admin.nvlead.list.ajax');
            Route::post('/nv-leads/add-process', [Controllers\Admin\NvLeadController::class, 'add_process'])->name('admin.nvlead.add.process');
            Route::post('/nv-leads/edit-process/{id}', [Controllers\Admin\NvLeadController::class, 'edit_process'])->name('admin.nvlead.edit.process');
            Route::get('/nv-leads/view/{lead_id?}', [Controllers\Admin\NvLeadController::class, 'view'])->name('admin.nvlead.view');
            Route::get('/nv-leads/delete/{lead_id?}', [Controllers\Admin\NvLeadController::class, 'delete'])->name('admin.nvlead.delete');
            Route::post('/nv-leads/forward', [Controllers\Admin\NvLeadController::class, 'lead_forward'])->name('admin.nvlead.forward');
            Route::get('/nv-leads/get_forward_info/{lead_id?}', [Controllers\Admin\NvLeadController::class, 'get_forward_info'])->name('admin.nvlead.getForwardInfo');
            //Bypass login: Via admin to vendor crm
            Route::get('/vendor/bypass-login/{team_id?}', [AuthController::class, 'vendor_login_via_admin'])->name('admin.vendor.bypass.login');
        });
        Route::prefix('whastsapp-crm')->group(function () {
            //whastsapp routes
            Route::view('/tasks', 'admin.whatsapp.campain.list')->name('whatsapp.campain.list');
            Route::view('/templates', 'admin.whatsapp.campain.templates')->name('whatsapp.campain.templates');
            Route::view('/logs', 'admin.whatsapp.logs.list')->name('whatsapp.campain.logs');
            Route::get('/campaign', [Controllers\Admin\WhatsappController::class, 'index'])->name('whatsapp.campain.campaign');
            Route::get('/campain_ajax', [Controllers\Admin\WhatsappController::class, 'whatsappCampain_ajax'])->name('whatsapp_chat.campain_ajax');
            Route::post('/campaign/manage-process/{id?}', [Controllers\Admin\WhatsappController::class, 'manage_process'])->name('admin.campaign.manage.process');
            Route::get('/campaign/manage_ajax/{id?}', [Controllers\Admin\WhatsappController::class, 'manage_ajax'])->name('admin.campaign.edit');
            Route::get('/campaign/update-status/{campaign_id?}/{status?}', [Controllers\Admin\WhatsappController::class, 'manageWhatsappCampainStatus'])->name('admin.campaign.update.status');
            Route::get('/campaign/delete/{campaign_id?}', [Controllers\Admin\WhatsappController::class, 'delete'])->name('admin.campaign.delete');
            Route::get('/logs_ajax', [Controllers\Admin\WhatsappController::class, 'whatsappLogs_ajax'])->name('whatsapp_chat.logs_ajax');

            // tempupdate
            Route::get('/get_temp_img/{temp_name?}', [Controllers\Admin\WhatsappController::class, 'get_temo_img'])->name('get.whatsapp.temp.img');
            Route::post('/update_temp_img/update', [Controllers\Admin\WhatsappController::class, 'update_temp_image'])->name('set.whatsapp.temp.img');
        });

        // activity routes
        Route::view('/activity_logs', 'admin.activitylog')->name('admin.activity.logs');
        Route::get('/activity_logs_ajax', [Controllers\Admin\ActivityLogController::class, 'activity_log'])->name('admin.activity.logs_ajax');
        ROute::get('/activity_logs_property/{id}', [Controllers\Admin\ActivityLogController::class, 'get_activity_log_property'])->name('admin.activity.logs_ajax_property');

        // Edit Env Route
        Route::get('/admin/edit-env', [Controllers\Admin\EnvController::class, 'editEnv'])->name('admin.editEnv');
        Route::post('/admin/update-env', [Controllers\Admin\EnvController::class, 'updateEnv'])->name('admin.updateEnv');

        // Bdm Crm
        Route::prefix('bdm-crm')->group(function () {
            Route::match (['get', 'post'], '/leads/list/{dashboard_filters?}', [Controllers\Admin\BdmLeadController::class, 'list'])->name('admin.bdm.lead.list');
            Route::get('/leads/ajax_list/', [Controllers\Admin\BdmLeadController::class, 'ajax_list'])->name('admin.bdm.lead.list.ajax');
            Route::get('/leads/view/{lead_id?}', [Controllers\Admin\BdmLeadController::class, 'view'])->name('admin.bdm.lead.view');
            Route::post('/leads/edit-process/{lead_id}', [Controllers\Admin\BdmLeadController::class, 'edit_process'])->name('admin.bdm.lead.edit.process');
            Route::post('/leads/add-process', [Controllers\Admin\BdmLeadController::class, 'add_process'])->name('admin.bdm.lead.add.process');
            Route::get('/delete_lead/{lead_id?}', [Controllers\Admin\BdmLeadController::class, 'delete'])->name('admin.bdm.lead.delete');
            Route::post('/assign_to_bdm_lead', [Controllers\Admin\BdmLeadController::class, 'lead_forward'])->name('admin.bdm.lead.forward');


            Route::match (['get', 'post'], '/leads/status-update/{lead_id}/{status?}', [Controllers\Admin\BdmLeadController::class, 'status_update'])->name('admin.bdm.lead.status.update');

            // bdm booking routes
            Route::get('/get_booking/{booking_id?}', [Controllers\Admin\BdmBookingController::class, 'get_booking'])->name('admin.bdm.booking.get');
            Route::post('/add_booking', [Controllers\Admin\BdmBookingController::class, 'add_process'])->name('admin.bdm.booking.add');
            Route::post('/edit_booking/{booking_id?}', [Controllers\Admin\BdmBookingController::class, 'edit_process'])->name('admin.bdm.booking.edit');
            Route::get('/delete_booking/{booking_id?}', [Controllers\Admin\BdmBookingController::class, 'delete'])->name('admin.bdm.booking.delete');
            Route::post('/update_payment_img/update', [Controllers\Admin\BdmBookingController::class, 'update_payment_image'])->name('admin.bdm.update.bookingPayment.img');
            Route::get('/images/manage/{bdm_booking_id?}', [Controllers\Admin\BdmBookingController::class, 'manage_agreement_image'])->name('admin.bdm.aggrement.manage_images');
            Route::post('/images/manage_process/{bdm_booking_id}', [Controllers\Admin\BdmBookingController::class, 'manage_agreement_image_process'])->name('admin.bdm.aggrement_images.manage_process');
            Route::post('/images/delete/{booking_id?}', [Controllers\Admin\BdmBookingController::class, 'agreement_image_delete'])->name('admin.bdm.agreement_images.delete');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | For Manager Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('/manager')->middleware('manager')->group(function () {
        Route::get('/dashboard', [Controllers\Manager\DashboardController::class, 'index'])->name('manager.dashboard');
        Route::post('/update-profile-image', [Controllers\Manager\DashboardController::class, 'update_profile_image'])->name('manager.updateProfileImage');

        Route::prefix('/venue-crm')->group(function () {
            //Team member Routes
            Route::get('/my-team', [Controllers\Manager\TeamMemberController::class, 'list'])->name('manager.team.list');
            Route::get('/my-team/ajax_list', [Controllers\Manager\TeamMemberController::class, 'ajax_list'])->name('manager.team.list.ajax');

            //Lead Routes
            Route::match (['get', 'post'], '/leads', [Controllers\Manager\LeadController::class, 'list'])->name('manager.lead.list');
            Route::get('/leads/ajax_list', [Controllers\Manager\LeadController::class, 'ajax_list'])->name('manager.lead.list.ajax');
            Route::get('/leads/get_forward_info/{lead_id?}', [Controllers\Manager\LeadController::class, 'get_forward_info'])->name('manager.lead.getForwardInfo');
            Route::get('/leads/view/{lead_id?}', [Controllers\Manager\LeadController::class, 'view'])->name('manager.lead.view');
            Route::post('/leads/forward', [Controllers\Manager\LeadController::class, 'lead_forward'])->name('manager.lead.forward');

            //Bypass login: Via manager to vm crm
            Route::get('/bypass-login/{team_id?}', [AuthController::class, 'team_login_via_manager'])->name('manager.bypass.login');

            //Availability list
            Route::get('/availability/list', [Controllers\Manager\AvailabilityController::class, 'list'])->name('manager.availability.list');

            //Booking routes
            Route::match (['get', 'post'], '/bookings/list/{dashboard_filters?}', [Controllers\Manager\BookingController::class, 'list'])->name('manager.bookings.list');
            Route::get('/bookings/ajax_list/', [Controllers\Manager\BookingController::class, 'ajax_list'])->name('manager.bookings.list.ajax');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | For Vendor Manager Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('/vendormanager')->middleware('vendormanager')->group(function () {
        Route::get('/dashboard', [Controllers\VendorManager\DashboardController::class, 'index'])->name('vendormanager.dashboard');
        Route::post('/update-profile-image', [Controllers\VendorManager\DashboardController::class, 'update_profile_image'])->name('vendormanager.updateProfileImage');

        Route::prefix('/venue-crm')->group(function () {
            //Team member Routes
            Route::get('/my-team', [Controllers\VendorManager\TeamMemberController::class, 'list'])->name('vendormanager.team.list');
            Route::get('/my-team/ajax_list', [Controllers\VendorManager\TeamMemberController::class, 'ajax_list'])->name('vendormanager.team.list.ajax');

            //Lead Routes
            Route::match (['get', 'post'], '/leads', [Controllers\VendorManager\LeadController::class, 'list'])->name('vendormanager.lead.list');
            Route::get('/leads/ajax_list', [Controllers\VendorManager\LeadController::class, 'ajax_list'])->name('vendormanager.lead.list.ajax');
            Route::get('/leads/get_forward_info/{lead_id?}', [Controllers\VendorManager\LeadController::class, 'get_forward_info'])->name('vendormanager.lead.getForwardInfo');
            Route::get('/leads/view/{lead_id?}', [Controllers\VendorManager\LeadController::class, 'view'])->name('vendormanager.lead.view');
            Route::post('/leads/forward', [Controllers\VendorManager\LeadController::class, 'lead_forward'])->name('vendormanager.lead.forward');

            //Bypass login: Via vendormanager to vm crm
            Route::get('/bypass-login/{team_id?}', [AuthController::class, 'vendor_login_via_vendormanager'])->name('vendormanager.bypass.login');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | For Team Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('/team')->middleware('team')->group(function () {
        Route::get('/dashboard', [Controllers\Team\DashboardController::class, 'index'])->name('team.dashboard');
        Route::post('/update-profile-image', [Controllers\Team\DashboardController::class, 'update_profile_image'])->name('team.updateProfileImage');

        Route::prefix('venue-crm')->group(function () {
            //Lead Routes
            Route::match (['get', 'post'], '/leads/list/{dashboard_filters?}', [Controllers\Team\LeadController::class, 'list'])->name('team.lead.list');
            Route::get('/leads/ajax_list/', [Controllers\Team\LeadController::class, 'ajax_list'])->name('team.lead.list.ajax');
            Route::get('/leads/view/{lead_id?}', [Controllers\Team\LeadController::class, 'view'])->name('team.lead.view');
            Route::post('/leads/edit-process/{forward_id}', [Controllers\Team\LeadController::class, 'edit_process'])->name('team.lead.edit.process');
            Route::post('/leads/add-process', [Controllers\Team\LeadController::class, 'add_process'])->name('team.lead.add.process');
            Route::get('/leads/get-forward-info/{lead_id?}', [Controllers\Team\LeadController::class, 'get_forward_info'])->name('team.lead.getForwardInfo');
            Route::get('/leads/service-status-update/{forward_id}/{status?}', [Controllers\Team\LeadController::class, 'service_status_update'])->name('team.lead.serviceStatus.update');
            Route::match (['get', 'post'], '/leads/status-update/{forward_id}/{status?}', [Controllers\Team\LeadController::class, 'status_update'])->name('team.lead.status.update');
            Route::post('/leads/forward', [Controllers\Team\LeadController::class, 'lead_forward'])->name('team.lead.forward');
            Route::post('/leads/nvrmforward', [Controllers\Team\LeadController::class, 'lead_forward_nvrm'])->name('team.lead.forwardnvrm');

            //RM message Routes
            Route::post('/rm_messages/manage-process', [Controllers\Team\RmMessageController::class, 'manage_process'])->name('team.rm_message.manage.process');

            //Event Routes
            Route::get('events/manage_ajax/{event_id?}', [Controllers\Team\EventController::class, 'manage_ajax'])->name('team.event.manage_ajax');
            Route::post('/events/add-process/', [Controllers\Team\EventController::class, 'add_process'])->name('team.event.add.process');
            Route::post('/events/edit-process/{event_id?}', [Controllers\Team\EventController::class, 'edit_process'])->name('team.event.edit.process');

            //Note Routes
            Route::get('/notes/manage_ajax/{note_id?}', [Controllers\Team\NoteController::class, 'manage_ajax'])->name('team.note.edit');
            Route::post('/notes/manage-process/{note_id?}', [Controllers\Team\NoteController::class, 'manage_process'])->name('team.note.manage.process');
            Route::get('/notes/delete/{note_id}', [Controllers\Team\NoteController::class, 'delete'])->name('team.note.delete');

            //Task Routes
            Route::match (['get', 'post'], '/tasks/list/{dashboard_filters?}', [Controllers\Team\TaskController::class, 'list'])->name('team.task.list');
            Route::get('/tasks/ajax_list/', [Controllers\Team\TaskController::class, 'ajax_list'])->name('team.task.list.ajax');
            Route::post('/tasks/add-process/', [Controllers\Team\TaskController::class, 'add_process'])->name('team.task.add.process');
            Route::post('/tasks/status-update/{task_id?}', [Controllers\Team\TaskController::class, 'status_update'])->name('team.task.status.update');
            Route::get('/tasks/delete/{task_id}', [Controllers\Team\TaskController::class, 'delete'])->name('team.task.delete');

            //Visit Routes
            Route::match (['get', 'post'], '/visits/list/{dashboard_filters?}', [Controllers\Team\VisitController::class, 'list'])->name('team.visit.list');
            Route::get('/visits/ajax_list', [Controllers\Team\VisitController::class, 'ajax_list'])->name('team.visit.list.ajax');
            Route::post('/visits/add-process/', [Controllers\Team\VisitController::class, 'add_process'])->name('team.visit.add.process');
            // Route::get('/visits/manage_ajax/{visit_id?}', [Controllers\Team\VisitController::class, 'manage_ajax'])->name('team.visit.edit');
            Route::get('/visits/get_forward_info/{visit_id?}', [Controllers\Team\VisitController::class, 'get_forward_info'])->name('team.visit.getForwardInfo');
            Route::get('/visits/delete/{visit_id}', [Controllers\Team\VisitController::class, 'delete'])->name('team.visit.delete');
            Route::post('/visits/status-update/{visit_id?}', [Controllers\Team\VisitController::class, 'status_update'])->name('team.visit.status.update');
            //Below this route is only use for RM members
            Route::get('/visits/rm-visit-status-update/{visit_id}', [Controllers\Team\VisitController::class, 'rm_visit_status_update'])->name('team.RmVisit.status.update');

            Route::get('/availability/manage', [Controllers\Team\AvailabilityController::class, 'manage'])->name('team.availability.manage');
            Route::post('/availability/manage-process', [Controllers\Team\AvailabilityController::class, 'manage_process'])->name('team.availability.manage_process');
            Route::get('/availability/reset-calendar/{datetime}', [Controllers\Team\AvailabilityController::class, 'reset_calendar'])->name('team.availability.reset_calendar');

            //Booking routes
            Route::match (['get', 'post'], '/bookings/list/{dashboard_filters?}', [Controllers\Team\BookingController::class, 'list'])->name('team.bookings.list');
            Route::get('/bookings/ajax_list/', [Controllers\Team\BookingController::class, 'ajax_list'])->name('team.bookings.list.ajax');
            Route::post('/bookings/add-process', [Controllers\Team\BookingController::class, 'add_process'])->name('team.booking.add_process');
            Route::post('/bookings/add-more-advance-amount/{booking_id?}', [Controllers\Team\BookingController::class, 'add_more_advance_amount'])->name('team.booking.add_more_advance_amount');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | For Nonvenue Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('/nv-team')->middleware('nonvenue')->group(function () {
        Route::get('/dashboard', [Controllers\NonVenue\DashboardController::class, 'index'])->name('nonvenue.dashboard');
        Route::post('/update-profile-image', [Controllers\NonVenue\DashboardController::class, 'update_profile_image'])->name('nonvenue.updateProfileImage');

        Route::prefix('nonvenue-crm')->group(function () {
            //Lead Routes
            Route::match (['get', 'post'], '/leads/list/{dashboard_filters?}', [Controllers\NonVenue\NvLeadController::class, 'list'])->name('nonvenue.lead.list');
            Route::get('/leads/ajax_list', [Controllers\NonVenue\NvLeadController::class, 'ajax_list'])->name('nonvenue.lead.list.ajax');
            Route::post('/leads/add-process', [Controllers\NonVenue\NvLeadController::class, 'add_process'])->name('nonvenue.lead.add.process');
            Route::post('/leads/edit-process/{lead_id}', [Controllers\NonVenue\NvLeadController::class, 'edit_process'])->name('nonvenue.lead.edit.process');
            Route::get('/leads/view/{lead_id?}', [Controllers\NonVenue\NvLeadController::class, 'view'])->name('nonvenue.lead.view');
            Route::post('/leads/forward', [Controllers\NonVenue\NvLeadController::class, 'lead_forward'])->name('nonvenue.lead.forward');
            Route::get('/leads/service-status-update/{forward_id}/{status?}', [Controllers\NonVenue\NvLeadController::class, 'service_status_update'])->name('nonvenue.lead.serviceStatus.update');
            Route::get('/leads/get-forward-info/{lead_id?}', [Controllers\NonVenue\NvLeadController::class, 'get_forward_info'])->name('nonvenue.lead.getForwardInfo');
            Route::match (['get', 'post'], '/leads/status-update/{forward_id}/{status?}', [Controllers\NonVenue\NvLeadController::class, 'status_update'])->name('nonvenue.lead.status.update');

            //RM message Routes
            Route::post('/rm_messages/manage-process', [Controllers\NonVenue\NvrmMessageController::class, 'manage_process'])->name('nonvenue.rm_message.manage.process');

            //Event Routes
            Route::post('/events/add-process/', [Controllers\NonVenue\NvEventController::class, 'add_process'])->name('nonvenue.event.add.process');
            Route::get('/events/manage_ajax/{event_id?}', [Controllers\NonVenue\NvEventController::class, 'manage_ajax'])->name('nonvenue.event.edit');
            Route::post('/events/edit-process/{event_id?}', [Controllers\NonVenue\NvEventController::class, 'edit_process'])->name('nonvenue.event.edit.process');

            //Vendor Routes
            Route::get('/vendors/get-by-category/{category_id?}', [Controllers\NonVenue\NvLeadController::class, 'get_vendor_by_category'])->name('nonvenue.getVendorsByCategory');

            //Task Routes
            Route::match (['get', 'post'], '/tasks/list/{dashboard_filters?}', [Controllers\NonVenue\TaskController::class, 'list'])->name('nonvenue.task.list');
            Route::get('/tasks/ajax_list/', [Controllers\NonVenue\TaskController::class, 'ajax_list'])->name('nonvenue.task.list.ajax');
            Route::post('/tasks/add-process/', [Controllers\NonVenue\TaskController::class, 'add_process'])->name('nonvenue.task.add.process');
            Route::post('/tasks/status-update/{task_id?}', [Controllers\NonVenue\TaskController::class, 'status_update'])->name('nonvenue.task.status.update');
            Route::get('/tasks/delete/{task_id}', [Controllers\NonVenue\TaskController::class, 'delete'])->name('nonvenue.task.delete');

            //Vendor Routes
            Route::get('/vendors/{vendor_id}', [Controllers\NonVenue\VendorController::class, 'list'])->name('nonvenue.vendor.list');
            Route::get('/vendors/get_leads/{vendor_id}', [Controllers\NonVenue\VendorController::class, 'vedor_leads'])->name('nonvenue.vedor_leads.list');
            Route::get('/vendors/ajax/{vendor_id}', [Controllers\NonVenue\VendorController::class, 'ajax_list'])->name('nonvenue.vendor_ajax.list');

            // vendor help response routes
            Route::post('/vendors/help_update/{vendor_help_id?}', [Controllers\NonVenue\NvNotesController::class, 'vendor_update_help'])->name('nonvenue.vendor.help.update');
            Route::get('/vendors/help/ajax', [Controllers\NonVenue\NvNotesController::class, 'ajax_list'])->name('nonvenue.vendor.help.ajax');
            Route::match (['get', 'post'], '/vendors/help/list/{dashboard_filters?}', [Controllers\NonVenue\NvNotesController::class, 'list'])->name('nonvenue.vendor.help.list');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | For Bdm Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('/bdm')->middleware('bdm')->group(function () {
        Route::get('/dashboard', [Controllers\Bdm\DashboardController::class, 'index'])->name('bdm.dashboard');
        Route::post('/update-profile-image', [Controllers\Bdm\DashboardController::class, 'update_profile_image'])->name('bdm.updateProfileImage');

        Route::prefix('bdm-crm')->group(function () {
            //Lead Routes
            Route::match (['get', 'post'], '/leads/list/{dashboard_filters?}', [Controllers\Bdm\LeadController::class, 'list'])->name('bdm.lead.list');
            Route::get('/leads/ajax_list/', [Controllers\Bdm\LeadController::class, 'ajax_list'])->name('bdm.lead.list.ajax');
            Route::get('/leads/view/{lead_id?}', [Controllers\Bdm\LeadController::class, 'view'])->name('bdm.lead.view');
            Route::post('/leads/edit-process/{lead_id}', [Controllers\Bdm\LeadController::class, 'edit_process'])->name('bdm.lead.edit.process');
            Route::post('/leads/add-process', [Controllers\Bdm\LeadController::class, 'add_process'])->name('bdm.lead.add.process');
            Route::get('/leads/service-status-update/{lead_id}/{status?}', [Controllers\Bdm\LeadController::class, 'service_status_update'])->name('bdm.lead.serviceStatus.update');
            Route::match (['get', 'post'], '/leads/status-update/{lead_id}/{status?}', [Controllers\Bdm\LeadController::class, 'status_update'])->name('bdm.lead.status.update');

            //Note Routes
            Route::get('/notes/manage_ajax/{note_id?}', [Controllers\Bdm\NoteController::class, 'manage_ajax'])->name('bdm.note.edit');
            Route::post('/notes/manage-process/{note_id?}', [Controllers\Bdm\NoteController::class, 'manage_process'])->name('bdm.note.manage.process');
            Route::get('/notes/delete/{note_id}', [Controllers\Bdm\NoteController::class, 'delete'])->name('bdm.note.delete');

            // //Task Routes
            Route::match (['get', 'post'], '/tasks/list/{dashboard_filters?}', [Controllers\Bdm\TaskController::class, 'list'])->name('bdm.task.list');
            Route::get('/tasks/ajax_list/', [Controllers\Bdm\TaskController::class, 'ajax_list'])->name('bdm.task.list.ajax');
            Route::post('/tasks/add-process/', [Controllers\Bdm\TaskController::class, 'add_process'])->name('bdm.task.add.process');
            Route::post('/tasks/status-update/{task_id?}', [Controllers\Bdm\TaskController::class, 'status_update'])->name('bdm.task.status.update');
            Route::get('/tasks/delete/{task_id}', [Controllers\Bdm\TaskController::class, 'delete'])->name('bdm.task.delete');

            // Meeting Routes
            Route::match (['get', 'post'], '/meeting/list/{dashboard_filters?}', [Controllers\Bdm\MeetingController::class, 'list'])->name('bdm.meeting.list');
            Route::get('/meeting/ajax_list/', [Controllers\Bdm\MeetingController::class, 'ajax_list'])->name('bdm.meeting.list.ajax');
            Route::post('/meeting/add-process/', [Controllers\Bdm\MeetingController::class, 'add_process'])->name('bdm.meeting.add.process');
            Route::post('/meeting/status-update/{meeting_id?}', [Controllers\Bdm\MeetingController::class, 'status_update'])->name('bdm.meeting.status.update');
            Route::get('/meeting/delete/{meeting_id}', [Controllers\Bdm\MeetingController::class, 'delete'])->name('bdm.meeting.delete');

            // booking route
            Route::match (['get', 'post'], '/booking/list/{dashboard_filters?}', [Controllers\Bdm\BookingController::class, 'list'])->name('bdm.booking.list');
            Route::get('/booking/ajax_list/', [Controllers\Bdm\BookingController::class, 'ajax_list'])->name('bdm.booking.list.ajax');
            Route::post('/add_booking', [Controllers\Bdm\BookingController::class, 'add_process'])->name('bdm.booking.add');
            Route::get('/get_booking/{booking_id?}', [Controllers\Bdm\BookingController::class, 'get_booking'])->name('bdm.booking.get');
            Route::post('/edit_booking/{booking_id?}', [Controllers\Bdm\BookingController::class, 'edit_process'])->name('bdm.booking.edit');
            Route::get('/delete_booking/{booking_id?}', [Controllers\Bdm\BookingController::class, 'delete'])->name('bdm.booking.delete');
            Route::post('/update_payment_img/update', [Controllers\Bdm\BookingController::class, 'update_payment_image'])->name('bdm.update.bookingPayment.img');
            Route::get('/images/manage/{bdm_booking_id?}', [Controllers\Bdm\BookingController::class, 'manage_agreement_image'])->name('bdm.aggrement.manage_images');
            Route::post('/images/manage_process/{bdm_booking_id}', [Controllers\Bdm\BookingController::class, 'manage_agreement_image_process'])->name('bdm.aggrement_images.manage_process');
            Route::post('/images/delete/{booking_id?}', [Controllers\Bdm\BookingController::class, 'agreement_image_delete'])->name('bdm.agreement_images.delete');
        });
    });
});

/*
|--------------------------------------------------------------------------
| For vendor Routes
|--------------------------------------------------------------------------
*/
Route::middleware('verify_vendor_token')->group(function () {
    Route::prefix('/vendor')->middleware('vendor')->group(function () {
        Route::get('/dashboard', [Controllers\Vendor\DashboardController::class, 'index'])->name('vendor.dashboard');
        Route::post('/update-profile-image', [Controllers\Vendor\DashboardController::class, 'update_profile_image'])->name('vendor.updateProfileImage');

        //Lead Routes
        Route::match (['get', 'post'], '/leads/list/{dashboard_filters?}', [Controllers\Vendor\NvLeadController::class, 'list'])->name('vendor.lead.list');
        Route::get('/leads/ajax_list', [Controllers\Vendor\NvLeadController::class, 'ajax_list'])->name('vendor.lead.list.ajax');
        Route::get('/leads/view/{lead_id?}', [Controllers\Vendor\NvLeadController::class, 'view'])->name('vendor.lead.view');
        Route::match (['get', 'post'], '/leads/status-update/{forward_id}/{status?}', [Controllers\Vendor\NvLeadController::class, 'status_update'])->name('vendor.lead.status.update');

        //Note Routes
        Route::get('/notes/manage_ajax/{note_id?}', [Controllers\Vendor\NvNoteController::class, 'manage_ajax'])->name('vendor.note.edit');
        Route::post('/notes/manage-process/{note_id?}', [Controllers\Vendor\NvNoteController::class, 'manage_process'])->name('vendor.note.manage.process');
        Route::get('/notes/delete/{note_id}', [Controllers\Vendor\NvNoteController::class, 'delete'])->name('vendor.note.delete');

        //Task Routes
        Route::match (['get', 'post'], '/tasks', [Controllers\Vendor\NvTaskController::class, 'list'])->name('vendor.task.list');
        Route::get('/tasks/ajax_list/', [Controllers\Vendor\NvTaskController::class, 'ajax_list'])->name('vendor.task.list.ajax');
        Route::post('/tasks/add-process/', [Controllers\Vendor\NvTaskController::class, 'add_process'])->name('vendor.task.add.process');
        Route::post('/tasks/status-update/{task_id?}', [Controllers\Vendor\NvTaskController::class, 'status_update'])->name('vendor.task.status.update');
        Route::get('/tasks/delete/{task_id}', [Controllers\Vendor\NvTaskController::class, 'delete'])->name('vendor.task.delete');

        //Meeting Routes
        Route::match (['get', 'post'], '/meetings', [Controllers\Vendor\NvMeetingController::class, 'list'])->name('vendor.meeting.list');
        Route::get('/meetings/ajax_list/', [Controllers\Vendor\NvMeetingController::class, 'ajax_list'])->name('vendor.meeting.list.ajax');
        Route::post('/meetings/add-process/', [Controllers\Vendor\NvMeetingController::class, 'add_process'])->name('vendor.meeting.add.process');
        Route::get('/meetings/delete/{meeting_id?}', [Controllers\Vendor\NvMeetingController::class, 'delete'])->name('vendor.meeting.delete');
        Route::post('/meetings/status-update/{meeting_id?}', [Controllers\Vendor\NvMeetingController::class, 'status_update'])->name('vendor.meeting.status.update');

        // vendor help response routes
        Route::get('/vendors/help/ajax', [Controllers\Vendor\NvNoteController::class, 'ajax_list'])->name('vendor.help.ajax.list');
        Route::view('/vendors/help/list', 'vendor.rmhelpsupport.list')->name('vendor.help.list');

        // personal lead section
        //Lead Routes
        Route::prefix('/personal')->group(function () {
            Route::match (['get', 'post'], '/leads/list/{dashboard_filters?}', [Controllers\Vendor\PLeadController::class, 'list'])->name('pvendor.lead.list');
            Route::get('/leads/ajax_list', [Controllers\Vendor\PLeadController::class, 'ajax_list'])->name('pvendor.lead.list.ajax');
            Route::get('/leads/view/{lead_id?}', [Controllers\Vendor\PLeadController::class, 'view'])->name('pvendor.lead.view');
            Route::post('/leads/add-process', [Controllers\Vendor\PLeadController::class, 'add_process'])->name('pvendor.lead.add.process');
            Route::match (['get', 'post'], '/leads/status-update/{forward_id}/{status?}', [Controllers\Vendor\PLeadController::class, 'status_update'])->name('pvendor.lead.status.update');
            Route::post('/leads/edit-process/{lead_id}', [Controllers\Vendor\PLeadController::class, 'edit_process'])->name('pvendor.lead.edit.process');

            // Event Routes
            Route::post('/events/add-process/', [Controllers\Vendor\PEventController::class, 'add_process'])->name('pvendor.event.add.process');
            Route::get('/events/manage_ajax/{event_id?}', [Controllers\Vendor\PEventController::class, 'manage_ajax'])->name('pvendor.event.edit');
            Route::post('/events/edit-process/{event_id?}', [Controllers\Vendor\PEventController::class, 'edit_process'])->name('pvendor.event.edit.process');

            // Task Routes
            Route::post('/tasks/add-process/', [Controllers\Vendor\PTaskController::class, 'add_process'])->name('pvendor.task.add.process');
            Route::post('/tasks/status-update/{task_id?}', [Controllers\Vendor\PTaskController::class, 'status_update'])->name('pvendor.task.status.update');
            Route::get('/tasks/delete/{task_id}', [Controllers\Vendor\PTaskController::class, 'delete'])->name('pvendor.task.delete');

            // Meeting Routes
            Route::post('/meetings/add-process/', [Controllers\Vendor\PMeetingController::class, 'add_process'])->name('pvendor.meeting.add.process');
            Route::get('/meetings/delete/{meeting_id?}', [Controllers\Vendor\PMeetingController::class, 'delete'])->name('pvendor.meeting.delete');
            Route::post('/meetings/status-update/{meeting_id?}', [Controllers\Vendor\PMeetingController::class, 'status_update'])->name('pvendor.meeting.status.update');
        });
    });
});
