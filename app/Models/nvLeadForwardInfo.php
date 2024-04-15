<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Traits\HasAuthenticatedUser;
class nvLeadForwardInfo extends Model {
    use HasFactory, HasAuthenticatedUser,LogsActivity;
    public function getActivitylogOptions(): LogOptions
    {
        $userId = $this->getAuthenticatedUserId();

        return LogOptions::defaults()
            ->logOnly(['*'])
            ->setDescriptionForEvent(function (string $eventName) use ($userId) {
                return "This model has been {$eventName} by User ID: {$userId}";
            });
    }

    public function get_forward_from(){
        return $this->hasOne(TeamMember::class, 'id', 'forward_from');
    }

    public function get_forward_to(){
        return $this->hasOne(Vendor::class, 'id', 'forward_to');
    }
}
