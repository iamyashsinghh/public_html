<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Traits\HasAuthenticatedUser;
class TeamMember extends Authenticable {
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

    public function get_role() {
        return $this->hasOne(Role::class, 'id', 'role_id');
    }

    public function get_manager() {
        return $this->hasOne($this::class, 'id', 'parent_id');
    }

    // public function get_login_info() {
    //     return $this->hasOne(LoginInfo::class, 'member_id', 'id');
    // }

    public function get_party_areas() {
        return $this->hasMany(partyArea::class, 'member_id', 'id');
    }
    public function get_food_preferences() {
        return $this->hasMany(foodPreference::class, 'member_id', 'id');
    }
}
