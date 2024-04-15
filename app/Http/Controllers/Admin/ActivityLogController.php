<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ActivityLog;

class ActivityLogController extends Controller
{
    public function activity_log()
    {
        $ActivityLog = ActivityLog::select(
            'activity_log.id',
            'activity_log.description',
            'activity_log.event',
            'activity_log.subject_type',
            'activity_log.subject_id',
            'activity_log.created_at',
        )->get();
        return datatables($ActivityLog)->toJson();
    }

    public function get_activity_log_property($id)
{
    $ActivityLog = ActivityLog::select('properties')->where('id', $id)->first();

    if (!$ActivityLog) {
        return response()->json([
            'error' => 'Activity Log not found',
        ], 404);
    }
    return response()->json([
        'properties' => json_decode($ActivityLog->properties, true),
    ]);
}

}
