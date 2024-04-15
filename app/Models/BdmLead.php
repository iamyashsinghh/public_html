<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Traits\HasAuthenticatedUser;
use Illuminate\Support\Facades\Auth;

class BdmLead extends Model
{
    use HasFactory, HasAuthenticatedUser,SoftDeletes,LogsActivity;
    protected $table = 'bdm_leads';
    protected $guarded = [];
    protected $primaryKey = 'lead_id';


    public function getActivitylogOptions(): LogOptions
    {
        $userId = $this->getAuthenticatedUserId();

        return LogOptions::defaults()
            ->logOnly(['*'])
            ->setDescriptionForEvent(function (string $eventName) use ($userId) {
                return "This model has been {$eventName} by User ID: {$userId}";
            });
    }
    public function get_lead_cat() {
        $category = $this->hasOne(VendorCategory::class, "id","business_cat");
        return $category;
    }
    
    public function get_created_by() {
        return $this->hasOne(TeamMember::class, 'id', 'created_by');
    }
    public function get_bdm_tasks() {
        $bdm_tasks =  $this->hasMany(BdmTask::class, 'lead_id', 'lead_id');
        return $bdm_tasks->where('created_by', Auth::guard('bdm')->user()->id);
    }
    public function get_bdm_meetings() {
        $bdm_tasks =  $this->hasMany(BdmMeeting::class, 'lead_id', 'lead_id');
        return $bdm_tasks->where('created_by', Auth::guard('bdm')->user()->id);
    }
    public function get_meetings() {
        return $this->hasMany(BdmMeeting::class, 'lead_id', 'lead_id');
    }
    public function get_tasks() {
        return $this->hasMany(BdmTask::class, 'lead_id', 'lead_id');
    }


}
