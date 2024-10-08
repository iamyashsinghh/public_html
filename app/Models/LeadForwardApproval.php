<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Traits\HasAuthenticatedUser;

class LeadForwardApproval extends Model
{
    use HasFactory, HasAuthenticatedUser, LogsActivity;
    public function getActivitylogOptions(): LogOptions
    {
        $userId = $this->getAuthenticatedUserId();

        return LogOptions::defaults()
            ->logOnly(['*'])
            ->setDescriptionForEvent(function (string $eventName) use ($userId) {
                return "This model has been {$eventName} by User ID: {$userId}";
            });
    }
    protected $table = 'lead_forward_approval';
    protected $guarded = [];

    public static function getTeamMemberName($teamMemberId)
    {
        $teamMember = TeamMember::withTrashed()->find($teamMemberId);
        return "$teamMember->name / $teamMember->venue_name ";
    }
}
