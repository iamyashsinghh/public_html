<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Traits\HasAuthenticatedUser;
class LeadForward extends Model {
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
    public function get_forward_from() {
        return $this->hasOne(TeamMember::class, 'id', 'forward_from');
    }
    public function get_forward_to() {
        return $this->hasOne(TeamMember::class, 'id', 'forward_to')->withTrashed();
    }

    public function get_rm_messages() {
        return $this->hasMany(RmMessage::class, 'lead_id', 'lead_id');
    }

    public function get_vm_events() {
        return VmEvent::where(['lead_id' => $this->lead_id, 'created_by' => $this->forward_to])->get();
    }

    public function get_notes() {
        return Note::where(['lead_id' => $this->lead_id, 'created_by' => $this->forward_to])->get();
    }

    public function get_tasks() {
        return Task::where(['lead_id' => $this->lead_id, 'created_by' => $this->forward_to])->orderBy('task_schedule_datetime', 'asc')->get();
    }
    public function get_visits() {
        return Visit::where(['lead_id' => $this->lead_id, 'created_by' => $this->forward_to])->orderBy('visit_schedule_datetime', 'asc')->get();
    }

    public function get_party_areas() {
        return $this->hasMany(partyArea::class, 'member_id', 'forward_to');
    }
    public function get_food_preferences() {
        return $this->hasMany(foodPreference::class, 'member_id', 'forward_to',);
    }

    public function get_bookings() {
        return Booking::where(['lead_id' => $this->lead_id, 'created_by' => $this->forward_to])->get();
    }
}
