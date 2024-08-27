<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\CssSelector\XPath\Extension\FunctionExtension;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Traits\HasAuthenticatedUser;
use Illuminate\Support\Facades\DB;

class nvrmLeadForward extends Model
{
    use HasFactory, HasAuthenticatedUser, SoftDeletes, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        $userId = $this->getAuthenticatedUserId();

        return LogOptions::defaults()
            ->logOnly(['*'])
            ->setDescriptionForEvent(function (string $eventName) use ($userId) {
                return "This model has been {$eventName} by User ID: {$userId}";
            });
    }
    public function nvrm_tasks()
    {
        return $this->hasMany(nvrmTask::class, 'lead_id', 'lead_id');
    }
    public function get_nvrm_messages()
    {
        return $this->hasMany(nvrmMessage::class, 'lead_id', 'lead_id');
    }

    public function get_events()
    {
        return $this->hasMany(nvEvent::class, 'lead_id', 'lead_id');
    }
    public function get_nvrm_help_messages()
    {
        return nvNote::where('lead_id', $this->lead_id)
            ->join('vendors', 'vendors.id', '=', 'nv_notes.created_by')
            ->join('vendor_categories', 'vendor_categories.id', '=', 'vendors.category_id')
            ->leftJoin('team_members', 'team_members.id', '=', 'nv_notes.done_by')
            ->select('nv_notes.*', 'vendors.name as created_by_name', 'vendor_categories.name as category_name',  'team_members.name as done_by_name')
            ->get();
    }
    public function get_nvrm_tasks()
    {
        $auth_user = Auth::guard('nonvenue')->user();
        return nvrmTask::select(
            'nvrm_tasks.id',
            'nvrm_tasks.task_schedule_datetime',
            'nvrm_tasks.follow_up',
            'nvrm_tasks.message',
            'nvrm_tasks.done_with',
            'nvrm_tasks.done_message',
            'nvrm_tasks.done_datetime',
        )->join('team_members as tm', ['tm.id' => 'nvrm_tasks.created_by'])->orderBy('task_schedule_datetime', 'asc')->where(['nvrm_tasks.lead_id' => $this->lead_id, 'tm.role_id' => 3, 'created_by' =>  $auth_user->id])->get();
    }

    public function get_vendors_for_lead()
    {
        return DB::table('nv_lead_forwards as lf')
            ->join('vendors as v', 'lf.forward_to', '=', 'v.id')
            ->leftJoin('nv_lead_forward_infos as lfi', function($join) {
                $join->on('lf.lead_id', '=', 'lfi.lead_id')
                     ->on('v.id', '=', 'lfi.forward_to');
            })
            ->where('lf.lead_id', $this->lead_id)
            ->select('v.name', 'v.category_id', 'lfi.updated_at')
            ->get();
    }
}
