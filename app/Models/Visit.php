<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Traits\HasAuthenticatedUser;
class Visit extends Model {
    use HasFactory, HasAuthenticatedUser, SoftDeletes,LogsActivity;
    protected $guarded = [];
    public function getActivitylogOptions(): LogOptions
    {
        $userId = $this->getAuthenticatedUserId();

        return LogOptions::defaults()
            ->logOnly(['*'])
            ->setDescriptionForEvent(function (string $eventName) use ($userId) {
                return "This model has been {$eventName} by User ID: {$userId}";
            });
    }

    public function get_created_by(){
        return $this->hasOne(TeamMember::class, 'id', 'created_by');
    }

    public function get_referred_by(){
        return $this->hasOne(TeamMember::class, 'id', 'referred_by');
    }
}
