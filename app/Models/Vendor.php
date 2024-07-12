<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Traits\HasAuthenticatedUser;
class Vendor extends Authenticable {
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

    public function get_category() {
        return $this->belongsTo(VendorCategory::class, 'category_id');
    }

    public function get_manager() {
        return $this->hasOne(TeamMember::class, 'id', 'parent_id');
    }

    public function get_registered_devices() {
        return $this->hasMany(Device::class, 'team_member_id', 'id')->where('type', 'vendor');
    }
}
