<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Traits\HasAuthenticatedUser;
class nvLeadForward extends Model {
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
    protected $guarded = [];
    public function get_rm_messages() {
        return $this->hasMany(nvrmMessage::class, 'lead_id', 'lead_id')->where('vendor_category_id', Auth::guard('vendor')->user()->category_id);
    }
    public function get_events() {
        return $this->hasMany(nvEvent::class, 'lead_id', 'lead_id');
    }

    public function get_notes() {
        return nvNote::where(['lead_id' => $this->lead_id, 'created_by' => Auth::guard('vendor')->user()->id])
                     ->leftJoin('team_members', 'team_members.id', '=', 'nv_notes.done_by')
                     ->select('nv_notes.*', 'team_members.name as done_by_name')
                     ->get();
    }

    public function get_tasks() {
        return nvTask::where(['lead_id' => $this->lead_id, 'created_by' => Auth::guard('vendor')->user()->id])->orderBy('task_schedule_datetime', 'asc')->get();
    }
    public function get_meetings() {
        return nvMeeting::where(['lead_id' => $this->lead_id, 'created_by' => Auth::guard('vendor')->user()->id])->orderBy('meeting_schedule_datetime', 'asc')->get();
    }

}
