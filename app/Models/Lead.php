<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Traits\HasAuthenticatedUser;

class Lead extends Model {
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
    protected $primaryKey = "lead_id";

    public function get_created_by() {
        return $this->hasOne(TeamMember::class, 'id', 'created_by');
    }

    public function get_rm_messages() {
        return $this->hasMany(RmMessage::class, 'lead_id', 'lead_id');
    }
    public function get_events() {
        return $this->hasMany(Event::class, 'lead_id', 'lead_id');
    }
    public function get_visits() {
        return $this->hasMany(Visit::class, 'lead_id', 'lead_id');
    }
    public function get_tasks() {
        return $this->hasMany(Task::class, 'lead_id', 'lead_id');
    }
    public function get_notes() {
        return $this->hasMany(Note::class, 'lead_id', 'lead_id');
    }
    public function get_bookings() {
        return $this->hasMany(Booking::class, 'lead_id', 'lead_id');
    }
    public function get_lead_forwards() {
        return $this->hasMany(LeadForward::class, 'lead_id', 'lead_id');
    }
    public function get_lead_forward_infos() {
        return $this->hasMany(LeadForwardInfo::class, 'lead_id', 'lead_id');
    }
    public function get_primary_events() {
        return Event::select(
            'events.id',
            'events.lead_id',
            'events.created_by',
            'events.event_name',
            'events.event_datetime',
            'events.pax',
            'events.budget',
            'events.food_preference',
            'events.event_slot',
            'events.created_at'
        )->join('team_members as tm', ['tm.id' => 'events.created_by'])->where(['events.lead_id' => $this->lead_id])->whereIn('tm.role_id', [1, 4])->get();
    }

    public function get_rm_visits() {
        return Visit::select(
            'visits.id',
            'visits.visit_schedule_datetime',
            'visits.message',
            'visits.event_name',
            'visits.event_datetime',
            'visits.menu_selected',
            'visits.party_area',
            'visits.price_quoted',
            'visits.done_message',
            'visits.done_datetime',
            'visits.referred_by',
            'visits.vm_visits_id',
        )->join('team_members as tm', ['tm.id' => 'visits.created_by'])->orderBy('visit_schedule_datetime', 'asc')->where(['visits.lead_id' => $this->lead_id, 'tm.role_id' => 4])->get();
    }
    public function get_rm_tasks() {
        $auth_user = Auth::guard('team')->user();
        return Task::select(
            'tasks.id',
            'tasks.task_schedule_datetime',
            'tasks.follow_up',
            'tasks.message',
            'tasks.done_with',
            'tasks.done_message',
            'tasks.done_datetime',
        )->join('team_members as tm', ['tm.id' => 'tasks.created_by'])->orderBy('task_schedule_datetime', 'asc')->where(['tasks.lead_id' => $this->lead_id, 'tm.role_id' => 4, 'created_by' =>  $auth_user->id])->get();
    }
    public function get_rm_notes() {
        return Note::select(
            'notes.id',
            'notes.created_by',
            'notes.message',
            'notes.created_at',
        )->join('team_members as tm', ['tm.id' => 'notes.created_by'])->where(['notes.lead_id' => $this->lead_id, 'tm.role_id' => 4])->get();
    }
}
