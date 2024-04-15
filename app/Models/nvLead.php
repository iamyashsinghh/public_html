<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Traits\HasAuthenticatedUser;
class nvLead extends Model {
    use HasFactory, HasAuthenticatedUser, SoftDeletes,LogsActivity;
    public function getActivitylogOptions(): LogOptions
    {
        $userId = $this->getAuthenticatedUserId();

        return LogOptions::defaults()
            ->logOnly(['*'])
            ->setDescriptionForEvent(function (string $eventName) use ($userId) {
                return "This model has been {$eventName} by User ID: {$userId}";
            });
    }

    public function get_nvrm_messages() {
        return $this->hasMany(nvrmMessage::class, 'lead_id', 'id');
    }
    public function get_nvrm_help_messages() {
        return nvNote::where('lead_id', $this->id)
            ->leftJoin('vendors', 'vendors.id', '=', 'nv_notes.created_by')
            ->leftJoin('vendor_categories', 'vendor_categories.id', '=', 'vendors.category_id')
            ->leftJoin('team_members', 'team_members.id', '=', 'nv_notes.done_by')
            ->select('nv_notes.*', 'vendors.name as created_by_name', 'vendor_categories.name as category_name', 'team_members.name as done_by_name')
            ->get();
    }
    public function get_events(){
        return $this->hasMany(nvEvent::class, 'lead_id', 'id');
    }
    public function get_tasks() {
        return $this->hasMany(nvrmTask::class, 'lead_id', 'id');
    }
    public function get_tasks_vendor() {
        return $this->hasMany(nvTask::class, 'lead_id', 'id');
    }


}
