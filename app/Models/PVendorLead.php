<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Traits\HasAuthenticatedUser;

class PVendorLead extends Model
{
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
    protected $table = 'p_vendor_leads';
    protected $guarded = [];

    public function get_events() {
        return $this->hasMany(PVendorEvent::class, 'lead_id', 'id');
    }

    public function get_tasks() {
        return PVendorTask::where(['lead_id' => $this->id, 'created_by' => Auth::guard('vendor')->user()->id])->orderBy('task_schedule_datetime', 'asc')->get();
    }
    public function get_meetings() {
        return PVendorMeeting::where(['lead_id' => $this->id, 'created_by' => Auth::guard('vendor')->user()->id])->orderBy('meeting_schedule_datetime', 'asc')->get();
    }
}
