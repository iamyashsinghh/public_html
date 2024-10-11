<?php

namespace App\Http\Controllers\Team;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Lead;
use App\Models\LeadForward;
use App\Models\LeadForwardInfo;
use App\Models\nvEvent;
use App\Models\nvLead;
use App\Models\nvrmLeadForward;
use App\Models\TeamMember;
use App\Models\WhatsappCampain;
use App\Models\VmEvent;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LeadController extends Controller
{
    private $current_timestamp;
    public function __construct()
    {
        $this->current_timestamp = date('Y-m-d H:i:s');
    }

    public function list(Request $request, $dashboard_filters = null)
    {
        $getRm = TeamMember::select('id', 'name')->where('role_id', '4')->get();
        $filter_params = "";
        if ($request->lead_read_status != null) {
            $filter_params = ['lead_read_status' => $request->lead_read_status];
        }
        if ($request->service_status != null) {
            $filter_params = ['service_status' => $request->service_status];
        }
        if ($request->team_members != null) {
            $filter_params = ['team_members' => $request->team_members];
        }
        if ($request->lead_from_date != null) {
            $filter_params = ['lead_from_date' => $request->lead_from_date, 'lead_to_date' => $request->lead_to_date];
        }
        if ($request->lead_done_from_date != null) {
            $filter_params = ['lead_done_from_date' => $request->lead_done_from_date, 'lead_done_to_date' => $request->lead_done_to_date];
        }
        if ($request->lead_status != null) {
            $filter_params = ['lead_status' => $request->lead_status];
        }
        if ($request->has_rm_message != null) {
            $filter_params = ['has_rm_message' => $request->has_rm_message];
        }
        if ($request->event_from_date != null) {
            $filter_params = ['event_from_date' => $request->event_from_date, 'event_to_date' => $request->event_to_date];
        }
        $page_heading = $filter_params ? "Leads - Filtered" : "Leads";

        if ($dashboard_filters !== null) {
            $filter_params = ['dashboard_filters' => $dashboard_filters];
            $page_heading = ucwords(str_replace("_", " ", $dashboard_filters));
        }
        $auth_user = Auth::guard('team')->user();
        $whatsapp_campaigns = WhatsappCampain::where('status', 1)->where('assign_to', $auth_user->id)->get();

        return view('team.venueCrm.lead.list', compact('page_heading', 'filter_params', 'getRm', 'whatsapp_campaigns'));
    }

    public function ajax_list(Request $request)
    {
        $auth_user = Auth::guard('team')->user();
        $current_date = date('Y-m-d');
        if ($auth_user->role_id === 4) {
            $leads = DB::table('leads')->select(
                'leads.lead_id as lead_id',
                'leads.lead_datetime',
                'leads.name',
                'leads.mobile',
                'leads.event_datetime as event_date',
                'leads.lead_status',
                'leads.service_status',
                'leads.read_status',
                'leads.lead_color',
                'leads.assign_to',
                'leads.source',
                'leads.preference',
                'leads.locality',
                'tm.name as created_by',
                'leads.whatsapp_msg_time',
                'leads.last_forwarded_by',
                'leads.enquiry_count',
                'leads.is_whatsapp_msg',
                DB::raw("(select count(fwd.id) from lead_forward_infos as fwd where fwd.lead_id = leads.lead_id group by fwd.lead_id) as forwarded_count"),
                'leads.lead_catagory',
                'ne.pax as pax',
                'leads.lead_from',
            )->leftJoin('team_members as tm', 'tm.id', 'leads.created_by')
                ->leftJoin('events as ne', 'ne.lead_id', '=', 'leads.lead_id');
            $leads->where('leads.deleted_at', null)->groupBy('leads.mobile');

            if ($request->has('lead_from') && $request->lead_from != '') {
                if (in_array('weddingbanquets.in', $request->lead_from)) {
                    // If 'weddingbanquets.in' is in the array, include records with 'null' lead_from
                    $leads->where(function($query) use ($request) {
                        $query->whereIn('leads.lead_from', $request->lead_from)
                              ->orWhereNull('leads.lead_from');
                    });
                } else {
                    $leads->whereIn('leads.lead_from', $request->lead_from);
                }
            }

            if ($request->has('lead_status') && $request->lead_status != '') {
                $leads->whereIn('leads.lead_status', $request->lead_status);
            }

            if ($request->has('event_from_date') && $request->event_from_date != '') {
                $from = Carbon::make($request->event_from_date);
                $to = $request->has('event_to_date') && $request->event_to_date != '' ? Carbon::make($request->event_to_date)->endOfDay() : $from->copy()->endOfDay();
                $leads->whereBetween('leads.event_datetime', [$from, $to]);
            }

            if ($request->pax_min_value != null) {
                $min =  $request->pax_min_value;
                if ($request->pax_max_value != null) {
                    $max = $request->pax_max_value;
                } else {
                    $max = $request->pax_min_value;
                }
                $leads->whereBetween('ne.pax', [$min, $max]);
            }

            if ($request->has('lead_from_date') && $request->lead_from_date != '') {
                $from = Carbon::make($request->lead_from_date);
                $to = $request->has('lead_to_date') && $request->lead_to_date != '' ? Carbon::make($request->lead_to_date)->endOfDay() : $from->copy()->endOfDay();
                $leads->whereBetween('leads.lead_datetime', [$from, $to]);
            }

            if ($request->has('has_rm_message') && $request->has_rm_message != '') {
                if ($request->has_rm_message == "yes") {
                    $leads->whereExists(function ($query) {
                        $query->select(DB::raw(1))
                            ->from('rm_messages')
                            ->whereRaw('leads.lead_id = rm_messages.lead_id');
                    });
                } else {
                    $leads->whereNotExists(function ($query) {
                        $query->select(DB::raw(1))
                            ->from('rm_messages')
                            ->whereRaw('leads.lead_id = rm_messages.lead_id');
                    });
                }
            }

            if ($request->lead_read_status != null) {
                $leads->where('lead_read_status', $request->lead_read_status);
            }

            if ($request->service_status != null) {
                $leads->where('service_status', $request->service_status);
            }

            if ($request->lead_done_from_date != null) {
                $from = Carbon::make($request->lead_done_from_date);
                if ($request->lead_done_to_date != null) {
                    $to = Carbon::make($request->lead_done_to_date)->endOfDay();
                } else {
                    $to = Carbon::make($request->lead_done_from_date)->endOfDay();
                }
                $leads->where('leads.lead_status', 'Done')->whereBetween('leads.updated_at', [$from, $to]);
            }

            if ($request->team_members != null) {
                $leads->whereIn('leads.assign_id', $request->team_members);
            }
        } else {
            $leads = DB::table('lead_forwards as leads')->select(
                'leads.lead_id',
                'leads.lead_datetime',
                'leads.name',
                'leads.mobile',
                'leads.event_datetime as event_date',
                'leads.lead_status',
                'leads.service_status',
                'leads.read_status',
                'ne.pax as pax',
            )->leftJoin('vm_events as ne', 'ne.lead_id', '=', 'leads.lead_id')->where('leads.forward_to', $auth_user->id)->whereNull('leads.deleted_at')->groupBy('leads.lead_id');
            if ($request->has('lead_status') && $request->lead_status != '') {
                $leads->whereIn('leads.lead_status', $request->lead_status);
            }
            if ($request->pax_min_value != null) {
                $min =  $request->pax_min_value;
                if ($request->pax_max_value != null) {
                    $max = $request->pax_max_value;
                } else {
                    $max = $request->pax_min_value;
                }
                $leads->whereBetween('ne.pax', [$min, $max]);
            }


            if ($request->has('event_from_date') && $request->event_from_date != '') {
                $from = Carbon::make($request->event_from_date);
                $to = $request->has('event_to_date') && $request->event_to_date != '' ? Carbon::make($request->event_to_date)->endOfDay() : $from->copy()->endOfDay();
                $leads->whereBetween('leads.event_datetime', [$from, $to]);
            }

            if ($request->has('lead_from_date') && $request->lead_from_date != '') {
                $from = Carbon::make($request->lead_from_date);
                $to = $request->has('lead_to_date') && $request->lead_to_date != '' ? Carbon::make($request->lead_to_date)->endOfDay() : $from->copy()->endOfDay();
                $leads->whereBetween('leads.lead_datetime', [$from, $to]);
            }

            if ($request->lead_read_status != null) {
                $leads->where('read_status', $request->lead_read_status);
            }

            if ($request->service_status != null) {
                $leads->where('service_status', $request->service_status);
            }

            if ($request->lead_done_from_date != null) {
                $from = Carbon::make($request->lead_done_from_date);
                if ($request->lead_done_to_date != null) {
                    $to = Carbon::make($request->lead_done_to_date)->endOfDay();
                } else {
                    $to = Carbon::make($request->lead_done_from_date)->endOfDay();
                }
                $leads->where('leads.lead_status', 'Done')->whereBetween('leads.updated_at', [$from, $to]);
            }
        }

        if ($auth_user->role_id === 4) {
            $seven_days_ago = Carbon::now()->subDays(7)->format('Y-m-d H:i:s');
            if ($request->dashboard_filters != null) {
                if ($request->dashboard_filters == "leads_received_this_month") {
                    $from = Carbon::today()->startOfMonth();
                    $to = Carbon::today()->endOfMonth();
                    $leads->whereBetween('leads.lead_datetime', [$from, $to])->where('assign_id', $auth_user->id);
                } elseif ($request->dashboard_filters == "leads_received_today") {
                    $from = Carbon::today()->startOfDay();
                    $to = Carbon::today()->endOfDay();
                    $leads->whereBetween('leads.lead_datetime', [$from, $to])->where('assign_id', $auth_user->id);
                } elseif ($request->dashboard_filters == "rm_unfollowed_leads") {
                    $currentDateTime = Carbon::now();
                    $leads = DB::table('leads')
                        ->select(
                            'leads.lead_id as lead_id',
                            'leads.lead_datetime',
                            'leads.name',
                            'leads.mobile',
                            'leads.event_datetime as event_date',
                            'leads.lead_status',
                            'leads.service_status',
                            'leads.read_status',
                            'leads.lead_color',
                            'leads.assign_to',
                            'leads.source',
                            'leads.preference',
                            'leads.locality',
                            'tm.name as created_by',
                            'leads.whatsapp_msg_time',
                            'leads.last_forwarded_by',
                            'leads.enquiry_count',
                            'leads.is_whatsapp_msg',
                            DB::raw("(select count(fwd.id) from lead_forward_infos as fwd where fwd.lead_id = leads.lead_id group by fwd.lead_id) as forwarded_count"),
                        )
                        ->leftJoin('team_members as tm', 'tm.id', '=', 'leads.created_by')
                        ->whereNull('leads.deleted_at')
                        ->leftJoin(DB::raw("
                        (SELECT tasks.lead_id
                        FROM tasks
                        WHERE tasks.deleted_at IS NULL
                        AND tasks.created_by = $auth_user->id
                        GROUP BY tasks.lead_id
                        HAVING COUNT(CASE WHEN tasks.done_datetime IS NULL THEN 1 END) = 0) as completed_tasks
                    "), 'completed_tasks.lead_id', '=', 'leads.lead_id')
                        ->whereNotNull('completed_tasks.lead_id')
                        ->where('leads.lead_status', '!=', 'Done');
                } elseif ($request->dashboard_filters == "vm_recce_today") {
                    $threeDaysAgo = Carbon::now()->subDays(3);
                    $startDate = Carbon::createFromDate(2024, 8, 1);
                    $leads = DB::table('leads')->select(
                        'leads.lead_id as lead_id',
                        'leads.lead_datetime',
                        'leads.name',
                        'leads.mobile',
                        'leads.event_datetime as event_date',
                        'leads.lead_status',
                        'leads.service_status',
                        'leads.read_status',
                        'leads.lead_color',
                        'leads.assign_to',
                        'leads.source',
                        'leads.preference',
                        'leads.locality',
                        'tm.name as created_by',
                        'leads.whatsapp_msg_time',
                        'leads.last_forwarded_by',
                        'leads.enquiry_count',
                        'leads.is_whatsapp_msg',
                        DB::raw("(select count(fwd.id) from lead_forward_infos as fwd where fwd.lead_id = leads.lead_id group by fwd.lead_id) as forwarded_count"),
                        'leads.lead_catagory',
                        'ne.pax as pax',
                    )->leftJoin('team_members as tm', 'tm.id', 'leads.created_by')
                        ->leftJoin('events as ne', 'ne.lead_id', '=', 'leads.lead_id');
                    $leads->where('leads.deleted_at', null)
                        ->groupBy('leads.mobile')
                        ->join('visits', 'visits.lead_id', '=', 'leads.lead_id')
                        ->leftJoin('rm_messages', 'rm_messages.lead_id', '=', 'leads.lead_id')
                        ->leftJoin('tasks', function ($join) {
                            $join->on('tasks.lead_id', '=', 'leads.lead_id');
                        })
                        ->whereDate('visits.done_datetime', '>=', $startDate)
                        ->whereNotNull('visits.done_datetime')
                        ->whereDate(DB::raw('DATE(DATE_ADD(visits.done_datetime, INTERVAL 3 DAY))'), '>', DB::raw('tasks.created_at'))
                        ->whereDate(DB::raw('DATE(DATE_ADD(visits.done_datetime, INTERVAL 3 DAY))'), '=', $current_date)
                        ->where('rm_messages.created_by', '=', $auth_user->id)
                        ->orderBy('rm_messages.updated_at', 'desc')
                        ->groupBy('leads.lead_id')
                        ->where('leads.lead_status', '!=', 'Done');
                } elseif ($request->dashboard_filters == "vm_recce_overdue") {
                    $startDate = Carbon::createFromDate(2024, 8, 1);
                    $leads = DB::table('leads')->select(
                        'leads.lead_id as lead_id',
                        'leads.lead_datetime',
                        'leads.name',
                        'leads.mobile',
                        'leads.event_datetime as event_date',
                        'leads.lead_status',
                        'leads.service_status',
                        'leads.read_status',
                        'leads.lead_color',
                        'leads.assign_to',
                        'leads.source',
                        'leads.preference',
                        'leads.locality',
                        'tm.name as created_by',
                        'leads.whatsapp_msg_time',
                        'leads.last_forwarded_by',
                        'leads.enquiry_count',
                        'leads.is_whatsapp_msg',
                        DB::raw("(select count(fwd.id) from lead_forward_infos as fwd where fwd.lead_id = leads.lead_id group by fwd.lead_id) as forwarded_count"),
                        'leads.lead_catagory',
                        'ne.pax as pax',
                    )->leftJoin('team_members as tm', 'tm.id', 'leads.created_by')
                        ->leftJoin('events as ne', 'ne.lead_id', '=', 'leads.lead_id');
                    $leads->where('leads.deleted_at', null)
                        ->groupBy('leads.mobile')
                        ->join('visits', 'visits.lead_id', '=', 'leads.lead_id')
                        ->leftJoin('rm_messages', 'rm_messages.lead_id', '=', 'leads.lead_id')
                        ->leftJoin('tasks', function ($join) {
                            $join->on('tasks.lead_id', '=', 'leads.lead_id');
                        })
                        ->whereNotNull('visits.done_message')
                        ->whereDate('visits.done_datetime', '>=', $startDate)
                        ->whereDate(DB::raw('DATE(DATE_ADD(visits.done_datetime, INTERVAL 3 DAY))'), '<', $current_date)
                        ->where(function ($query) {
                            $query->whereNull('tasks.created_at')
                                ->orWhere(DB::raw('DATE(DATE_ADD(visits.done_datetime, INTERVAL 3 DAY))'), '>', DB::raw('tasks.created_at'));
                        })
                        ->where('rm_messages.created_by', '=', $auth_user->id)
                        ->orderBy('rm_messages.updated_at', 'desc')
                        ->orderBy('visits.done_datetime', 'desc')
                        ->groupBy('leads.lead_id')
                        ->where('leads.lead_status', '!=', 'Done');
                } elseif ($request->dashboard_filters == "unread_leads_this_month") {
                    $from = Carbon::today()->startOfMonth();
                    $to = Carbon::today()->endOfMonth();
                    $leads->whereBetween('leads.lead_datetime', [$from, $to])
                        ->where('assign_id', $auth_user->id)
                        ->where('leads.read_status', false)
                        ->where(function ($query) use ($seven_days_ago) {
                            $query->whereNull('last_forwarded_by')
                                ->orWhere('last_forwarded_by', '<=', $seven_days_ago);
                        });
                } elseif ($request->dashboard_filters == "unread_leads_today") {
                    $from = Carbon::today()->startOfDay();
                    $to = Carbon::today()->endOfDay();
                    $leads->whereBetween('leads.lead_datetime', [$from, $to])
                        ->where('assign_id', $auth_user->id)
                        ->where('leads.read_status', false)
                        ->where(function ($query) use ($seven_days_ago) {
                            $query->whereNull('last_forwarded_by')
                                ->orWhere('last_forwarded_by', '<=', $seven_days_ago);
                        });
                } elseif ($request->dashboard_filters == "total_unread_leads_overdue") {
                    $leads->where('leads.read_status', false)
                        ->where('leads.lead_datetime', '<', Carbon::today())
                        ->where('assign_id', $auth_user->id)
                        ->where('leads.lead_id', '>', '54262')
                        ->where(function ($query) use ($seven_days_ago) {
                            $query->whereNull('last_forwarded_by')
                                ->orWhere('last_forwarded_by', '<=', $seven_days_ago);
                        });
                } elseif ($request->dashboard_filters == "not_contacted_lead") {
                    $leads->where('leads.service_status', 0)
                        ->where('leads.lead_id', '>', '54262')
                        ->where('leads.assign_id', $auth_user->id);
                } elseif ($request->dashboard_filters == "forward_leads_this_month") {
                    $from = Carbon::today()->startOfMonth();
                    $to = Carbon::today()->endOfMonth();
                    $leads = DB::table('lead_forward_infos as fwd_info')->select(
                        'leads.lead_id as lead_id',
                        'leads.lead_datetime',
                        'leads.name',
                        'leads.mobile',
                        'leads.event_datetime as event_date',
                        'leads.lead_status',
                        'leads.service_status',
                        'leads.read_status',
                        'leads.lead_color',
                        'leads.assign_to',
                        'leads.source',
                        'leads.preference',
                        'leads.locality',
                        'tm.name as created_by',
                        'leads.whatsapp_msg_time',
                        'leads.last_forwarded_by',
                        'leads.enquiry_count',
                        'leads.is_whatsapp_msg',
                        DB::raw("(select count(fwd.id) from lead_forward_infos as fwd where fwd.lead_id = leads.lead_id group by fwd.lead_id) as forwarded_count"),
                    )->join('leads', ['leads.lead_id' => 'fwd_info.lead_id'])
                        ->leftJoin('team_members as tm', 'tm.id', 'leads.created_by')
                        ->where(['fwd_info.forward_from' => $auth_user->id])->groupBy('fwd_info.lead_id');
                    $leads->whereBetween('fwd_info.updated_at', [$from, $to]);
                } elseif ($request->dashboard_filters == "forward_leads_today") {
                    $from = Carbon::today()->startOfDay();
                    $to = Carbon::today()->endOfDay();
                    $leads = DB::table('lead_forward_infos as fwd_info')->select(
                        'leads.lead_id as lead_id',
                        'leads.lead_datetime',
                        'leads.name',
                        'leads.mobile',
                        'leads.event_datetime as event_date',
                        'leads.lead_status',
                        'leads.service_status',
                        'leads.read_status',
                        'leads.lead_color',
                        'leads.assign_to',
                        'leads.source',
                        'leads.preference',
                        'leads.locality',
                        'tm.name as created_by',
                        'leads.whatsapp_msg_time',
                        'leads.last_forwarded_by',
                        'leads.enquiry_count',
                        'leads.is_whatsapp_msg',
                        DB::raw("(select count(fwd.id) from lead_forward_infos as fwd where fwd.lead_id = leads.lead_id group by fwd.lead_id) as forwarded_count"),
                    )->join('leads', ['leads.lead_id' => 'fwd_info.lead_id'])
                        ->leftJoin('team_members as tm', 'tm.id', 'leads.created_by')
                        ->where(['fwd_info.forward_from' => $auth_user->id])->groupBy('fwd_info.lead_id');
                    $leads->whereBetween('fwd_info.updated_at', [$from, $to]);
                }
            }
        } else {
            if ($request->dashboard_filters != null) {
                if ($request->dashboard_filters == "leads_received_this_month") {
                    $from = Carbon::today()->startOfMonth();
                    $to = Carbon::today()->endOfMonth();
                    $leads->whereBetween('leads.lead_datetime', [$from, $to]);
                } elseif ($request->dashboard_filters == "leads_received_today") {
                    $from = Carbon::today()->startOfDay();
                    $to = Carbon::today()->endOfDay();
                    $leads->whereBetween('leads.lead_datetime', [$from, $to]);
                } elseif ($request->dashboard_filters == "unread_leads_this_month") {
                    $from = Carbon::today()->startOfMonth();
                    $to = Carbon::today()->endOfMonth();
                    $leads->whereBetween('leads.lead_datetime', [$from, $to])->where('leads.read_status', false);
                } elseif ($request->dashboard_filters == "unread_leads_today") {
                    $from = Carbon::today()->startOfDay();
                    $to = Carbon::today()->endOfDay();
                    $leads->whereBetween('leads.lead_datetime', [$from, $to])->where('leads.read_status', false);
                } elseif ($request->dashboard_filters == "total_unread_leads_overdue") {
                    $leads->where('leads.read_status', false)->where('leads.lead_datetime', '<', Carbon::today());
                } elseif ($request->dashboard_filters == "forward_leads_this_month") {
                    $from = Carbon::today()->startOfMonth();
                    $to = Carbon::today()->endOfMonth();
                    $leads = DB::table('lead_forward_infos as fwd_info')->select(
                        'leads.lead_id as lead_id',
                        'leads.lead_datetime',
                        'leads.name',
                        'leads.mobile',
                        'leads.event_datetime as event_date',
                        'leads.lead_status',
                        'leads.service_status',
                        'leads.read_status',
                        'leads.lead_color',
                        'leads.assign_to',
                        'leads.source',
                        'leads.preference',
                        'leads.locality',
                        'tm.name as created_by',
                        'leads.last_forwarded_by',
                        'leads.enquiry_count',
                        DB::raw("(select count(fwd.id) from lead_forwards as fwd where fwd.lead_id = leads.lead_id) as forwarded_count")
                    )->join('leads', ['leads.lead_id' => 'fwd_info.lead_id'])
                        ->leftJoin('team_members as tm', 'tm.id', 'leads.created_by')
                        ->where(['fwd_info.forward_from' => $auth_user->id])->groupBy('fwd_info.lead_id');
                    $leads->whereBetween('fwd_info.updated_at', [$from, $to]);
                } elseif ($request->dashboard_filters == "forward_leads_today") {
                    $from = Carbon::today()->startOfDay();
                    $to = Carbon::today()->endOfDay();
                    $leads = DB::table('lead_forward_infos as fwd_info')->select(
                        'leads.lead_id as lead_id',
                        'leads.lead_datetime',
                        'leads.name',
                        'leads.mobile',
                        'leads.event_datetime as event_date',
                        'leads.lead_status',
                        'leads.service_status',
                        'leads.read_status',
                        'leads.lead_color',
                        'leads.assign_to',
                        'leads.source',
                        'leads.preference',
                        'leads.locality',
                        'tm.name as created_by',
                        'leads.last_forwarded_by',
                        'leads.enquiry_count',
                        DB::raw("(select count(fwd.id) from lead_forwards as fwd where fwd.lead_id = leads.lead_id) as forwarded_count")
                    )->join('leads', ['leads.lead_id' => 'fwd_info.lead_id'])
                        ->leftJoin('team_members as tm', 'tm.id', 'leads.created_by')->where(['fwd_info.forward_from' => $auth_user->id])->groupBy('fwd_info.lead_id');
                    $leads->whereBetween('fwd_info.updated_at', [$from, $to]);
                } elseif ($request->dashboard_filters == "unfollowed_leads") {
                    $leads->join('tasks', ['tasks.id' => 'leads.task_id'])->where('leads.lead_status', '!=', 'Done')->whereNotNull('tasks.done_datetime')->whereNull('tasks.deleted_at')
                        ->whereNotExists(function ($query) {
                            $query->select(DB::raw(1))
                                ->from('bookings')
                                ->whereRaw('bookings.id = leads.booking_id');
                        })->get();
                }
            }
        }

        return datatables($leads)->make(false);
    }


    public function edit_process(Request $request, $lead_id_or_forward_id)
    {
        $auth_user = Auth::guard('team')->user();
        if ($auth_user->role_id === 4) {
            $lead = Lead::find($lead_id_or_forward_id);
        } else {
            $lead = LeadForward::where(['lead_id' => $lead_id_or_forward_id, 'forward_to' => $auth_user->id])->first();
        }

        if (!$lead) {
            abort(404);
        }

        $lead->name = $request->name;
        $lead->email = $request->email;
        $lead->alternate_mobile = $request->alternate_mobile_number;
        $lead->locality = $request->locality;
        $lead->lead_status = $request->lead_status;
        $lead->save();
        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Lead updated successfully.']);
        return redirect()->back();
    }

    public function add_process(Request $request)
    {
        $is_name_valid = $request->name !== null ? "required|string|max:255" : "";
        $is_email_valid = $request->email !== null ? "required|email" : "";
        $is_alt_mobile_valid = $request->alternate_mobile_number !== null ? "required|digits:10" : "";
        $is_budget_validate = $request->budget !== null ? "required|numeric" : "";
        $validate = Validator::make($request->all(), [
            'name' => $is_name_valid,
            'email' => $is_email_valid,
            'alternate_mobile_number' => $is_alt_mobile_valid,
            'mobile_number' => "required|digits:10",
            'lead_status' => 'required',
            'event_name' => 'required|string|max:255',
            'event_date' => 'required|date',
            'event_slot' => 'required|string|max:255',
            'food_Preference' => 'required|string|max:255',
            'number_of_guest' => 'required|int|max_digits:6',
            'budget' => $is_budget_validate,
        ]);

        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }

        $auth_user = Auth::guard('team')->user();
        $exist_lead = Lead::where('mobile', $request->mobile_number)->first();
        if ($exist_lead) {
            $exist_forward = LeadForward::where(['lead_id' => $exist_lead->lead_id, 'forward_to' => $auth_user->id])->first();
            if ($exist_forward) {
                $lead_link = route('team.lead.view', $exist_forward->lead_id);
                session()->flash('status', ['success' => true, 'alert_type' => 'info', 'message' => "Lead is already exist with this mobile number. Click on the link below to view the lead. <a href='$lead_link'><b>$lead_link</b></a>"]);
            } else {
                session()->flash('status', ['success' => true, 'alert_type' => 'warning', 'message' => "Lead is already exist with this mobile number, Please contact to the management."]);
            }
            return redirect()->back();
        }

        if ($auth_user->role_id == 4) {
            $source = "WB|Team";
        } else {
            $source = $request->lead_source;
        }

        $lead = new Lead();
        $lead->created_by = $auth_user->id;
        $lead->lead_datetime = $this->current_timestamp;
        $lead->name = $request->name;
        $lead->email = $request->email;
        $lead->mobile = $request->mobile_number;
        $lead->alternate_mobile = $request->alternate_mobile_number;
        $lead->source = $source;
        $lead->locality = $request->locality;
        $lead->lead_status = $request->lead_status;
        $lead->event_datetime = $request->event_date ? $request->event_date . " " . date('H:i:s') : '';
        $lead->pax = $request->number_of_guest;
        $lead->lead_color = "#0066ff33";
        $lead->save();

        if ($auth_user->role_id == 5) {
            $forward = new LeadForward();
            $forward->lead_id = $lead->lead_id;
            $forward->forward_to = $auth_user->id;
            $forward->lead_datetime = $lead->lead_datetime;
            $forward->name = $lead->name;
            $forward->email = $lead->email;
            $forward->mobile = $lead->mobile;
            $forward->alternate_mobile = $lead->alternate_mobile;
            $forward->source = $lead->source;
            $forward->locality = $lead->locality;
            $forward->lead_status = $lead->lead_status;
            $forward->event_datetime = $lead->event_datetime;
            $forward->save();

            $event = new VmEvent();
            $event->created_by = $auth_user->id;
            $event->lead_id = $lead->lead_id;
            $event->event_name = $request->event_name;
            $event->event_datetime = $request->event_date . " " . date('H:i:s');
            $event->pax = $request->number_of_guest;
            $event->budget = $request->budget;
            $event->food_Preference = $request->food_Preference;
            $event->event_slot = $request->event_slot;
            $event->save();
        } else {
            $event = new Event();
            $event->created_by = $auth_user->id;
            $event->lead_id = $lead->lead_id;
            $event->event_name = $request->event_name;
            $event->event_datetime = $request->event_date . " " . date('H:i:s');
            $event->pax = $request->number_of_guest;
            $event->budget = $request->budget;
            $event->food_Preference = $request->food_Preference;
            $event->event_slot = $request->event_slot;
            $event->save();
        }

        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => "Lead created successfully."]);
        return redirect()->back();
    }

    public function lead_forward_nvrm(Request $request)
    {
        $rm_lead_id = $request->lead_id;
        $auth_user = Auth::guard('team')->user();
        $leadData = Lead::where('lead_id', $rm_lead_id)->first();
        if (!$leadData) {
            return response()->json(['success' => false, 'message' => 'Lead data not found.'], 404);
        }
        $exist_lead = nvLead::where('mobile', $leadData->mobile)->first();
        if ($exist_lead) {
            return response()->json(['success' => true, 'alert_type' => 'info', 'message' => "Lead is already exist with this mobile number"], 200);
        } else {
            $lead = new nvLead();
            $lead->created_by = $auth_user->id;
            $lead->lead_datetime = $this->current_timestamp;
            $lead->name = $leadData->name;
            $lead->email = $leadData->email;
            $lead->mobile = $leadData->mobile;
            $lead->alternate_mobile = $leadData->alternate_mobile;
            $lead->event_datetime = $leadData->event_datetime;
            if ($lead->save()) {
                $exist_lead_forward = nvrmLeadForward::where(['lead_id' => $lead->id, 'forward_to' => $auth_user->nvrm_id])->first();
                if (!$exist_lead_forward) {
                    $lead_forward = new nvrmLeadForward();
                    $lead_forward->lead_id = $lead->id;
                    $lead_forward->forward_to = $auth_user->nvrm_id;
                    $lead_forward->lead_datetime = $this->current_timestamp;
                    $lead_forward->name = $lead->name;
                    $lead_forward->email = $lead->email;
                    $lead_forward->mobile = $lead->mobile;
                    $lead_forward->alternate_mobile = $lead->alternate_mobile;
                    $lead_forward->address = $lead->address;
                    $lead_forward->lead_status = "Active";
                    $lead_forward->read_status = false;
                    $lead_forward->done_title = null;
                    $lead_forward->done_message = null;
                    $lead_forward->event_datetime = $lead->event_datetime;
                    $lead_forward->save();
                }
                $leadEventsData = Event::where('lead_id', $rm_lead_id)->get();
                foreach ($leadEventsData as $leadEventData) {
                    $event = new NvEvent();
                    $event->created_by = $auth_user->id;
                    $event->lead_id = $lead->id;
                    $event->event_name = $leadEventData->event_name;
                    $event->event_datetime = $leadEventData->event_datetime . " " . date('H:i:s');
                    $event->pax = $leadEventData->pax;
                    $event->event_slot = $leadEventData->event_slot;
                    $event->save();
                }
                return response()->json(['success' => true, 'message' => 'Lead added and forwarded successfully.'], 200);
            } else {
                return response()->json(['success' => false, 'message' => 'Failed to save the lead.'], 500);
            }
        }
    }

    public function view($lead_id)
    {
        $auth_user = Auth::guard('team')->user();
        if ($auth_user->role_id == 4) {
            $all_vm_members = TeamMember::select('id', 'name', 'venue_name')->where(['role_id' => 5, 'status' => 1, 'is_active' => 1])->orderBy('venue_name', 'asc')->get();

            $current_lead_having_vm_members = LeadForward::select(
                'tm.id',
                'tm.name',
                'tm.venue_name',
            )->leftJoin('team_members as tm', 'lead_forwards.forward_to', 'tm.id')
                ->where(['lead_forwards.lead_id' => $lead_id])->whereNot('lead_forwards.lead_status', 'Done')->get();

            $lead = Lead::find($lead_id);
        } else {
            $all_vm_members = [];
            $current_lead_having_vm_members = [];
            $lead = LeadForward::where(['lead_id' => $lead_id, 'forward_to' => $auth_user->id])->first();
        }
        if (!$lead) {
            abort(404);
        }

        $total_events_count = Event::where('lead_id', $lead_id)->count();

        $vm_members_which_contains_common_venue_name = [];
        $vm_members_which_contains_uncommon_venue_name = [];
        $venue_name_arr = [];
        foreach ($all_vm_members as $member) {
            $venueName = $member->venue_name;
            $venueCounts[$venueName] = ($venueCounts[$venueName] ?? 0) + 1;
        }

        $commonVenue = [];
        $uncommonVenue = [];

        foreach ($all_vm_members as $venue) {
            $venueName = $venue['venue_name'];
            if ($venueCounts[$venueName] > 1) {
                $commonVenue[] = $venue;
            } else {
                $uncommonVenue[] = $venue;
            }
        }

        return view('team.venueCrm.lead.view', compact('lead', 'current_lead_having_vm_members', 'total_events_count', 'commonVenue', 'uncommonVenue'));
    }

    public function get_forward_info($lead_id = 0)
    {
        try {
            $lead_forwards = LeadForwardInfo::select(
                'tm.name as to_name',
                'tm.venue_name',
                'rm.name as from_name',
                'lead_forwards.read_status',
                'lead_forward_infos.updated_at',
            )->join('team_members as tm', 'lead_forward_infos.forward_to', '=', 'tm.id')
                ->join('team_members as rm', 'lead_forward_infos.forward_from', '=', 'rm.id')
                ->join('lead_forwards', function ($join) use ($lead_id) {
                    $join->on('lead_forwards.forward_to', '=', 'tm.id')
                        ->where('lead_forwards.lead_id', '=', $lead_id);
                })
                ->where('lead_forward_infos.lead_id', $lead_id)
                ->orderBy('lead_forwards.updated_at', 'desc')
                ->get();

            $lead_forward_info = LeadForwardInfo::where(['lead_id' => $lead_id])->orderBy('updated_at', 'desc')->first();
            if ($lead_forward_info) {
                $last_forwarded_info = "Last forwarded by: " . $lead_forward_info->get_forward_from->name . " (" . $lead_forward_info->get_forward_from->get_role->name . ") " . " @ " . date('d-M-Y h:i a', strtotime($lead_forward_info->updated_at));
            } else {
                $last_forwarded_info = "Last forwarded by: N/A";
            }

            return response()->json(['success' => true, 'lead_forwards' => $lead_forwards, 'last_forwarded_info' => $last_forwarded_info]);
        } catch (\Throwable $th) {
            return response()->json(['success' => true, 'alert_type' => 'error', 'message' => 'Something went wrong.', 'error' => $th]);
        }
    }

    public function service_status_update($lead_id_or_forward_id, $status)
    {
        $auth_user = Auth::guard('team')->user();
        if ($auth_user->role_id == 4) {
            $lead = Lead::find($lead_id_or_forward_id);
        } else {
            $lead = LeadForward::where(['lead_id' => $lead_id_or_forward_id, 'forward_to' => $auth_user->id])->first();
        }
        if (!$lead) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => "Something went wrong."]);
            return redirect()->back();
        }

        $lead->service_status = $status;
        $lead->save();
        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => "Service status updated."]);
        return redirect()->back();
    }

    public function status_update(Request $request, $lead_id_or_forward_id, $status = "Done")
    {
        $auth_user = Auth::guard('team')->user();
        if ($auth_user->role_id == 4) {
            $lead = Lead::find($lead_id_or_forward_id);
        } else {
            $lead = LeadForward::where(['lead_id' => $lead_id_or_forward_id, 'forward_to' => $auth_user->id])->first();
        }

        if (!$lead) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => "Something went wrong."]);
            return redirect()->back();
        }

        if ($status == "Active") {
            $lead->lead_datetime = $this->current_timestamp;
            $lead->lead_status = "Active";
            $lead->read_status = false;
            $lead->service_status = false;
            $lead->done_title = null;
            $lead->done_message = null;
            if ($auth_user->role_id == 4) {
                $lead->lead_color = "#4bff0033";
            }
            $lead->save();
        } else {
            $validate = Validator::make($request->all(), [
                'done_title' => 'required|string',
            ]);
            if ($validate->fails()) {
                session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
                return redirect()->back();
            }
            $lead->lead_status = "Done";
            $lead->read_status = true;
            $lead->done_title = $request->done_title;
            $lead->done_message = $request->done_message;
            if ($auth_user->role_id == 4) {
                $lead->created_by = $auth_user->id;
                $lead->lead_color = "#ff000066";
            }
            $lead->save();
        }

        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => "Lead status updated."]);
        return redirect()->back();
    }

    public function lead_forward(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'lead_id' => 'required|int|exists:leads,lead_id',
            'forward_vms_id' => 'required',
        ]);

        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }

        $auth_user = Auth::guard('team')->user();
        $lead = Lead::find($request->lead_id);
        if (!$lead && $auth_user->role_id !== 4) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong.']);
            return redirect()->back();
        }
        $primary_events = $lead->get_primary_events();
        foreach ($request->forward_vms_id as $vm_id) {
            $exist_lead_forward = LeadForward::where(['lead_id' => $lead->lead_id, 'forward_to' => $vm_id])->first();
            if ($exist_lead_forward) {
                $lead_forward = $exist_lead_forward;
            } else {
                $lead_forward = new LeadForward();
                $lead_forward->lead_id = $lead->lead_id;
                $lead_forward->forward_to = $vm_id;
            }
            $lead_forward->lead_datetime = $this->current_timestamp;
            $lead_forward->name = $lead->name;
            $lead_forward->email = $lead->email;
            $lead_forward->mobile = $lead->mobile;
            $lead_forward->alternate_mobile = $lead->alternate_mobile;
            $lead_forward->source = "WB|Team";
            $lead_forward->locality = $lead->locality;
            $lead_forward->lead_status = $lead->lead_status;
            $lead_forward->read_status = false;
            $lead_forward->service_status = false;
            $lead_forward->done_title = null;
            $lead_forward->done_message = null;
            $lead_forward->event_datetime = $lead->event_datetime;
            $lead_forward->save();

            foreach ($primary_events as $main_event) {
                $vm_events = VmEvent::where(['event_id' => $main_event->id, 'created_by' => $vm_id])->first();
                if (!$vm_events) {
                    $vm_events = new VmEvent();
                }
                $vm_events->lead_id = $lead->lead_id;
                $vm_events->created_by = $vm_id;
                $vm_events->event_name = $main_event->event_name;
                $vm_events->event_datetime = $main_event->event_datetime;
                $vm_events->pax = $main_event->pax;
                $vm_events->budget = $main_event->budget;
                $vm_events->food_preference = $main_event->food_preference;
                $vm_events->event_slot = $main_event->event_slot;
                $vm_events->event_id = $main_event->id;
                $vm_events->save();
            }

            //update or insert lead_forward_info table;
            // $lead_forward_info = LeadForwardInfo::where(['lead_id' => $lead->lead_id, 'forward_from' => $auth_user->id, 'forward_to' => $vm_id])->first();
            // if (!$lead_forward_info) {
            $lead_forward_info = new LeadForwardInfo();
            $lead_forward_info->lead_id = $lead->lead_id;
            $lead_forward_info->forward_from = $auth_user->id;
            $lead_forward_info->forward_to = $vm_id;
            // }
            $lead_forward_info->updated_at = $this->current_timestamp;
            $lead_forward_info->save();
        }

        $last_forwarded_info = $auth_user->name . " <br> " . date('d-M-Y h:i a');
        $lead->read_status = true;
        $lead->lead_color = "#ff00001f";
        $lead->last_forwarded_by = $last_forwarded_info;
        $lead->save();

        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Lead forwarded successfully.']);
        return redirect()->back();
    }
}
