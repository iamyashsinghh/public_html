<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Lead;
use Illuminate\Support\Carbon;
use DB;

class DeleteOldData extends Command
{
    protected $signature = 'data:delete-old';
    protected $description = 'Deletes data older than one year from specified tables.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
{
    DB::beginTransaction();
    try {

        $elevenMonthsAgo = Carbon::now();
        $leadIds = Lead::where('updated_at', '<', $elevenMonthsAgo)->pluck('lead_id');
        $modelsToDelete = [
            \App\Models\LeadForward::class,
            \App\Models\Event::class,
            \App\Models\Booking::class,
            \App\Models\Note::class,
            \App\Models\Task::class,
            \App\Models\Visit::class,
            \App\Models\RmMessage::class,
            \App\Models\LeadForwardInfo::class,
        ];

        foreach ($modelsToDelete as $model) {
            // Process in chunks to efficiently manage memory
            $model::whereIn('lead_id', $leadIds)->chunkById(100, function ($records) {
                foreach ($records as $record) {
                    $record->forceDelete();
                }
            }, $column = 'lead_id'); // Ensure you specify the correct primary key column if it's not 'id'
        }

        // Delete the leads
        $affectedLeads = Lead::whereIn('lead_id', $leadIds)->forceDelete();

        DB::commit();
        $this->info("Successfully deleted $affectedLeads leads and their related records.");
    } catch (\Exception $e) {
        DB::rollBack();
        $this->error("An error occurred: " . $e->getMessage());
    }
}

}
