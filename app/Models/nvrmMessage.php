<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Traits\HasAuthenticatedUser;
class nvrmMessage extends Model {
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

    public function get_created_by() {
        return $this->hasOne(TeamMember::class, 'id', 'created_by');
    }

    public function get_service_category(){
        return $this->hasOne(VendorCategory::class, 'id', 'vendor_category_id');
    }
}
