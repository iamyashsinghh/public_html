@extends('admin.layouts.app')
@section('title', 'Dashboard | Admin')
@section('header-css')
    <link rel="stylesheet" href="{{ asset('plugins/charts/chart.css') }}">
@endsection
@section('main')
    <div class="content-wrapper pb-5">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Dashboard</h1>
                    </div>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="container-fluid">
                <div class="row" id="admin-dashboard-cards">
                    <div class="col-lg-3 col-6">
                        <div class="small-box text-sm text-light" style="background: var(--wb-renosand);">
                            <div class="inner">
                                <h3>{{ $total_vendors }}</h3>
                                <p>Total Vendors</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>
                            <a href="{{ route('admin.vendor.list') }}" class="small-box-footer">More info <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box text-sm text-light" style="background: var(--wb-dark-red);">
                            <div class="inner">
                                <h3>{{ $total_team }}</h3>
                                <p>Total Team Members</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-stats-bars"></i>
                            </div>
                            <a href="{{ route('admin.team.list') }}" class="small-box-footer">More info <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box text-sm text-light" style="background: var(--wb-renosand);">
                            <div class="inner">
                                <h3>{{ $total_venue_leads }}</h3>
                                <p>Total Venue Leads</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>
                            <a href="{{ route('admin.lead.list') }}" class="small-box-footer">More info <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box text-sm text-light" style="background: var(--wb-dark-red);">
                            <div class="inner">
                                <h3>{{ $total_nv_leads }}</h3>
                                <p>Total NV Leads</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>
                            <a href="{{ route('admin.nvlead.list') }}" class="small-box-footer">More info <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="card text-xs">
                            <div class="card-header border-0 text-light" style="background-color: var(--wb-renosand);">
                                <h3 class="card-title row">
                                    <i class="fas fa-th mr-1"></i>
                                    Venue Leads of&nbsp;<div id="selected-month">{{ date('F') }}</div>&nbsp;Month ||
                                    Average: <div id="avarageLeadId">{{ round($average_leads_for_month) }}</div>
                                </h3>
                                <div class="card-tools">
                                    <select id="month-selector" class="form-control form-control-sm"
                                        style="width: auto; display: inline;">
                                        <option value="Current Month">
                                            Current Month
                                        </option>
                                        @foreach (range(1, 12) as $i)
                                            <option value="{{ now()->subMonthsNoOverflow($i)->format('F Y') }}"
                                                {{ $i === 0 ? 'selected' : '' }}>
                                                {{ now()->subMonthsNoOverflow($i)->format('F Y') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="card-body">
                                <canvas class="chart" id="venue_chart_months"
                                    style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card text-xs">
                            <div class="card-header border-0 text-light" style="background-color: var(--wb-renosand);">
                                <h3 class="card-title">
                                    <i class="fas fa-th mr-1"></i>
                                    Venue Leads of Year {{ date('Y', strtotime('-1 Year')) }} - {{ date('Y') }}
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-xs text-light" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <button type="button" class="btn btn-xs text-light" data-card-widget="remove">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <canvas class="chart" id="venue_chart_years"
                                    style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="card text-xs">
                            <div class="card-header border-0 text-light" style="background-color: var(--wb-renosand);">
                                <h3 class="card-title">
                                    <i class="fas fa-th mr-1"></i>
                                    NV Leads of {{ date('F') }} Month
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-xs text-light" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <button type="button" class="btn btn-xs text-light" data-card-widget="remove">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <canvas class="chart" id="nv_chart_months"
                                    style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card text-xs">
                            <div class="card-header border-0 text-light" style="background-color: var(--wb-renosand);">
                                <h3 class="card-title">
                                    <i class="fas fa-th mr-1"></i>
                                    NV Leads of Year {{ date('Y', strtotime('-1 Year')) }} - {{ date('Y') }}
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-xs text-light" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <button type="button" class="btn btn-xs text-light" data-card-widget="remove">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <canvas class="chart" id="nv_chart_years"
                                    style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="my-2">
                    <div class="card text-xs mb-5">
                        <div class="card-header card-header-mod text-light"
                            style="background: linear-gradient(48deg, #8e0000e6, #dfa930b5);">
                            <h6 class="mb-0 text-bold">VM Productivity - {{ date('F') }}</h6>
                        </div>
                        <div class="card-body p-0">
                            <table class="table-bordered">
                                <thead>
                                    <tr class="text-center">
                                        <th class="text-nowrap text-left px-2">CLH Name</th>
                                        <th class="text-left text-nowrap px-2">VM Name /<br /> Venue Name</th>
                                        <th class="px-2">WB Recce Target</th>
                                        <th class="px-2">Recce this Month</th>
                                        <th class="px-2">WB Recce %</th>
                                        <th class="px-2">Bookings this Month</th>
                                        <th class="px-4">L2R %</th>
                                        <th class="px-4">R2C %</th>
                                        <th class="px-2">Leads this Month</th>
                                        <th class="px-2">Leads Overdue</th>
                                        <th class="px-2">Task Overdue</th>
                                        <th class="px-2">Unfollowed Leads</th>
                                        <th class="px-2">Total Unactioned</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $total_unread_leads_overdue = 0;
                                        $total_task_overdue = 0;
                                        $total_unfollowed_leads = 0;
                                        $grand_total_unactioned = 0;
                                        $total_recce_done_this_month = 0;
                                        $total_bookings_this_month = 0;
                                        $total_leads_received_this_month = 0;
                                        $total_wb_targets = 0;
                                        $total_wb_recce_percentage = 0;
                                        $total_l2r = 0;
                                        $total_r2c = 0;
                                        $number_of_vms = 0;
                                    @endphp
                                    @foreach ($vm_members as $vm)
                                        @php
                                            $number_of_vms += 1;
                                            $total_recce_done_this_month += $vm->recce_done_this_month;
                                            $total_bookings_this_month += $vm->bookings_this_month;
                                            $total_leads_received_this_month += $vm->leads_received_this_month;
                                            $total_wb_targets += $vm->wb_recce_target;

                                            $total_unactioned =
                                                $vm->unread_leads_overdue + $vm->task_overdue + $vm->unfollowed_leads;
                                            $grand_total_unactioned += $total_unactioned;

                                            $total_unread_leads_overdue += $vm->unread_leads_overdue;
                                            $total_task_overdue += $vm->task_overdue;
                                            $total_unfollowed_leads += $vm->unfollowed_leads;
                                            $total_wb_recce_percentage += $vm->wb_recce_percentage;
                                            $total_l2r += $vm->l2r;
                                            $total_r2c += $vm->r2c;
                                        @endphp
                                        <tr class="text-center" style="font-weight: bold;">
                                            <td class="text-nowrap text-left px-2">
                                                {{ $vm->get_manager ? $vm->get_manager->name : 'N/A' }}
                                            </td>
                                            <td class="text-left text-nowrap px-2">
                                                {{ $vm->name }} /<br /> {{ $vm->venue_name }}
                                            </td>
                                            <td>
                                                <input type="number" style="width: 50px;"
                                                    data-vm_id="{{ $vm->id }}" value="{{ $vm->wb_recce_target }}"
                                                    onchange="wb_recce_target(this)">
                                            </td>
                                            <td class="recce_done_this_month_td">{{ $vm->recce_done_this_month }}</td>
                                            @php
                                                $val = $vm->wb_recce_percentage;
                                                if ($val < 50) {
                                                    $bg_color = 'red';
                                                } elseif ($val == 50) {
                                                    $bg_color = 'darkgoldenrod';
                                                } elseif ($val > 50 && $val < 100) {
                                                    $bg_color = 'green';
                                                } elseif ($val >= 100) {
                                                    $bg_color = 'darkgreen';
                                                } else {
                                                    $bg_color = null;
                                                }
                                            @endphp
                                            <td class="text-nowrap wb_recce_percentage_td {{ $bg_color != null ? 'text-white' : '' }}"
                                                style="background-color: {{ $bg_color }}">
                                                {{ $vm->wb_recce_percentage }} %
                                            </td>
                                            <td>{{ $vm->bookings_this_month }}</td>
                                            <td class="text-nowrap">{{ $vm->l2r }} %</td>
                                            <td class="text-nowrap">{{ $vm->r2c }} %</td>
                                            <td>{{ $vm->leads_received_this_month }}</td>
                                            <td data-value="{{ $vm->unread_leads_overdue }}"
                                                class="unread_leads_overdue_td">{{ $vm->unread_leads_overdue }}</td>
                                            <td data-value="{{ $vm->task_overdue }}" class="task_overdue_td">
                                                {{ $vm->task_overdue }}</td>
                                            <td data-value="{{ $vm->unfollowed_leads }}" class="unfollowed_leads_td">
                                                {{ $vm->unfollowed_leads }}</td>
                                            <td data-value="{{ $total_unactioned }}" class="total_unactioned_td">
                                                {{ $total_unactioned }}</td>
                                        </tr>
                                    @endforeach
                                    @php
                                        $average_wb_recce_percentage =
                                            $total_wb_targets > 0
                                                ? ($total_recce_done_this_month / $total_wb_targets) * 100
                                                : 0;
                                        $average_l2r = $number_of_vms > 0 ? $total_l2r / $number_of_vms : 0;
                                        $average_r2c = $number_of_vms > 0 ? $total_r2c / $number_of_vms : 0;
                                    @endphp
                                    <tr class="text-center" style="font-weight: bold;">
                                        <td class="text-nowrap text-left px-2">Summary</td>
                                        <td class="text-left text-nowrap px-2">All VMs /<br /> All Venues</td>
                                        <td class="px-2">{{ $total_wb_targets }}</td>
                                        <td class="recce_done_this_month_td">{{ $total_recce_done_this_month }}</td>
                                        <td class="px-2">{{ round($average_wb_recce_percentage, 2) }} %</td>
                                        <td>{{ $total_bookings_this_month }}</td>
                                        <td class="px-2">{{ round($average_l2r, 2) }} %</td>
                                        <td class="px-2">{{ round($average_r2c, 2) }} %</td>
                                        <td>{{ $total_leads_received_this_month }}</td>
                                        <td>{{ $total_unread_leads_overdue }}</td>
                                        <td>{{ $total_task_overdue }}</td>
                                        <td>{{ $total_unfollowed_leads }}</td>
                                        <td>{{ $grand_total_unactioned }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>


                <div class="my-2">
                    <div class="card text-xs mb-5">
                        <div class="card-header card-header-mod text-light"
                            style="background: linear-gradient(48deg, #8e0000e6, #dfa930b5);">
                            <h6 class="mb-0 text-bold">Photography - {{ date('F') }}</h5>
                        </div>
                        <div class="card-body p-0" style="max-width: 100%; overflow-x: auto;">
                            <table class="table-bordered" style="width: 100%;">
                                <thead>
                                    <tr class="text-center">
                                        <th class="text-left px-1">Vendor Name /<br /> Business Name</th>
                                        <th class="px-1">Total Leads Recieved</th>
                                        <th class="px-1">Leads Between Subscription Period</th>
                                        <th class="px-1">Leads Recieved this Month</th>
                                        <th class="px-1">Leads Recieved Today</th>
                                        <th class="px-1">Unread Leads this Month</th>
                                        <th class="px-1">Unread Leads Overdue</th>
                                        <th class="px-1">Schedule Task this Month</th>
                                        <th class="px-1">Schedule Task Today</th>
                                        <th class="px-1">Task Overdue</th>
                                        <th class="px-1">Schedule Meeting this Month</th>
                                        <th class="px-1">Schedule Meeting Today</th>
                                        <th class="px-1">Meeting Overdue</th>
                                        <th class="px-1">Self-Lead Created</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($v_members as $v)
                                        @if ($v->category_id == 1)
                                            <tr class="text-center" style="font-weight: bold;">
                                                <td class="text-nowrap text-left p-1">{{ $v->name }}
                                                    /<br />{{ $v->business_name }}</td>
                                                <td class="p-1">{{ $v->total_leads_received }}</td>
                                                <td class="p-1">{{ $v->time_period_lead }}</td>
                                                <td class="p-1">{{ $v->leads_received_this_month }}</td>
                                                <td class="p-1">{{ $v->leads_received_today }}</td>
                                                <td class="p-1">{{ $v->unread_leads_this_month }}</td>
                                                <td class="p-1">{{ $v->unread_leads_overdue }}</td>
                                                <td class="p-1">{{ $v->task_schedule_this_month }}</td>
                                                <td class="p-1">{{ $v->task_schedule_today }}</td>
                                                <td class="p-1">{{ $v->task_overdue }}</td>
                                                <td class="p-1">{{ $v->meeting_schedule_this_month }}</td>
                                                <td class="p-1">{{ $v->meeting_schedule_today }}</td>
                                                <td class="p-1">{{ $v->meeting_overdue }}</td>
                                                <td class="p-1">{{ $v->created_lead }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="my-2">
                    <div class="card text-xs mb-5">
                        <div class="card-header card-header-mod text-light"
                            style="background: linear-gradient(48deg, #8e0000e6, #dfa930b5);">
                            <h6 class="mb-0 text-bold">Makeup Artist - {{ date('F') }}</h5>
                        </div>
                        <div class="card-body p-0" style="max-width: 100%; overflow-x: auto;">
                            <table class="table-bordered" style="width: 100%;">
                                <thead>
                                    <tr class="text-center">
                                        <th class="text-left px-1">Vendor Name /<br /> Business Name</th>
                                        <th class="px-1">Total Leads Recieved</th>
                                        <th class="px-1">Leads Between Subscription Period</th>
                                        <th class="px-1">Leads Recieved this Month</th>
                                        <th class="px-1">Leads Recieved Today</th>
                                        <th class="px-1">Unread Leads this Month</th>
                                        <th class="px-1">Unread Leads Overdue</th>
                                        <th class="px-1">Schedule Task this Month</th>
                                        <th class="px-1">Schedule Task Today</th>
                                        <th class="px-1">Task Overdue</th>
                                        <th class="px-1">Schedule Meeting this Month</th>
                                        <th class="px-1">Schedule Meeting Today</th>
                                        <th class="px-1">Meeting Overdue</th>
                                        <th class="px-1">Self-Lead Created</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($v_members as $v)
                                        @if ($v->category_id == 2)
                                            <tr class="text-center" style="font-weight: bold;">
                                                <td class="text-nowrap text-left p-1">{{ $v->name }}
                                                    /<br />{{ $v->business_name }}</td>
                                                <td class="p-1">{{ $v->total_leads_received }}</td>
                                                <td class="p-1">{{ $v->time_period_lead }}</td>
                                                <td class="p-1">{{ $v->leads_received_this_month }}</td>
                                                <td class="p-1">{{ $v->leads_received_today }}</td>
                                                <td class="p-1">{{ $v->unread_leads_this_month }}</td>
                                                <td class="p-1">{{ $v->unread_leads_overdue }}</td>
                                                <td class="p-1">{{ $v->task_schedule_this_month }}</td>
                                                <td class="p-1">{{ $v->task_schedule_today }}</td>
                                                <td class="p-1">{{ $v->task_overdue }}</td>
                                                <td class="p-1">{{ $v->meeting_schedule_this_month }}</td>
                                                <td class="p-1">{{ $v->meeting_schedule_today }}</td>
                                                <td class="p-1">{{ $v->meeting_overdue }}</td>
                                                <td class="p-1">{{ $v->created_lead }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="my-2">
                    <div class="card text-xs mb-5">
                        <div class="card-header card-header-mod text-light"
                            style="background: linear-gradient(48deg, #8e0000e6, #dfa930b5);">
                            <h6 class="mb-0 text-bold">WB Venue - {{ date('F') }}</h5>
                        </div>
                        <div class="card-body p-0" style="max-width: 100%; overflow-x: auto;">
                            <table class="table-bordered" style="width: 100%;">
                                <thead>
                                    <tr class="text-center">
                                        <th class="text-left px-1">Vendor Name /<br /> Business Name</th>
                                        <th class="px-1">Total Leads Recieved</th>
                                        <th class="px-1">Leads Between Subscription Period</th>
                                        <th class="px-1">Leads Recieved this Month</th>
                                        <th class="px-1">Leads Recieved Today</th>
                                        <th class="px-1">Unread Leads this Month</th>
                                        <th class="px-1">Unread Leads Overdue</th>
                                        <th class="px-1">Schedule Task this Month</th>
                                        <th class="px-1">Schedule Task Today</th>
                                        <th class="px-1">Task Overdue</th>
                                        <th class="px-1">Schedule Meeting this Month</th>
                                        <th class="px-1">Schedule Meeting Today</th>
                                        <th class="px-1">Meeting Overdue</th>
                                        <th class="px-1">Self-Lead Created</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($v_members as $v)
                                        @if ($v->category_id == 4)
                                            <tr class="text-center" style="font-weight: bold;">
                                                <td class="text-nowrap text-left p-1">{{ $v->name }}
                                                    /<br />{{ $v->business_name }}</td>
                                                <td class="p-1">{{ $v->total_leads_received }}</td>
                                                <td class="p-1">{{ $v->time_period_lead }}</td>
                                                <td class="p-1">{{ $v->leads_received_this_month }}</td>
                                                <td class="p-1">{{ $v->leads_received_today }}</td>
                                                <td class="p-1">{{ $v->unread_leads_this_month }}</td>
                                                <td class="p-1">{{ $v->unread_leads_overdue }}</td>
                                                <td class="p-1">{{ $v->task_schedule_this_month }}</td>
                                                <td class="p-1">{{ $v->task_schedule_today }}</td>
                                                <td class="p-1">{{ $v->task_overdue }}</td>
                                                <td class="p-1">{{ $v->meeting_schedule_this_month }}</td>
                                                <td class="p-1">{{ $v->meeting_schedule_today }}</td>
                                                <td class="p-1">{{ $v->meeting_overdue }}</td>
                                                <td class="p-1">{{ $v->created_lead }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="my-2">
                    <div class="card text-xs mb-5">
                        <div class="card-header card-header-mod text-light"
                            style="background: linear-gradient(48deg, #8e0000e6, #dfa930b5);">
                            <h6 class="mb-0 text-bold">Mehndi Artist - {{ date('F') }}</h5>
                        </div>
                        <div class="card-body p-0" style="max-width: 100%; overflow-x: auto;">
                            <table class="table-bordered" style="width: 100%;">
                                <thead>
                                    <tr class="text-center">
                                        <th class="text-left px-1">Vendor Name /<br /> Business Name</th>
                                        <th class="px-1">Total Leads Recieved</th>
                                        <th class="px-1">Leads Between Subscription Period</th>
                                        <th class="px-1">Leads Recieved this Month</th>
                                        <th class="px-1">Leads Recieved Today</th>
                                        <th class="px-1">Unread Leads this Month</th>
                                        <th class="px-1">Unread Leads Overdue</th>
                                        <th class="px-1">Schedule Task this Month</th>
                                        <th class="px-1">Schedule Task Today</th>
                                        <th class="px-1">Task Overdue</th>
                                        <th class="px-1">Schedule Meeting this Month</th>
                                        <th class="px-1">Schedule Meeting Today</th>
                                        <th class="px-1">Meeting Overdue</th>
                                        <th class="px-1">Self-Lead Created</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($v_members as $v)
                                        @if ($v->category_id == 3)
                                            <tr class="text-center" style="font-weight: bold;">
                                                <td class="text-nowrap text-left p-1">{{ $v->name }}
                                                    /<br />{{ $v->business_name }}</td>
                                                <td class="p-1">{{ $v->total_leads_received }}</td>
                                                <td class="p-1">{{ $v->time_period_lead }}</td>
                                                <td class="p-1">{{ $v->leads_received_this_month }}</td>
                                                <td class="p-1">{{ $v->leads_received_today }}</td>
                                                <td class="p-1">{{ $v->unread_leads_this_month }}</td>
                                                <td class="p-1">{{ $v->unread_leads_overdue }}</td>
                                                <td class="p-1">{{ $v->task_schedule_this_month }}</td>
                                                <td class="p-1">{{ $v->task_schedule_today }}</td>
                                                <td class="p-1">{{ $v->task_overdue }}</td>
                                                <td class="p-1">{{ $v->meeting_schedule_this_month }}</td>
                                                <td class="p-1">{{ $v->meeting_schedule_today }}</td>
                                                <td class="p-1">{{ $v->meeting_overdue }}</td>
                                                <td class="p-1">{{ $v->created_lead }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="my-2">
                    <div class="card text-xs mb-5">
                        <div class="card-header card-header-mod text-light"
                            style="background: linear-gradient(48deg, #8e0000e6, #dfa930b5);">
                            <h6 class="mb-0 text-bold">Band Baja - {{ date('F') }}</h5>
                        </div>
                        <div class="card-body p-0" style="max-width: 100%; overflow-x: auto;">
                            <table class="table-bordered" style="width: 100%;">
                                <thead>
                                    <tr class="text-center">
                                        <th class="text-left px-1">Vendor Name /<br /> Business Name</th>
                                        <th class="px-1">Total Leads Recieved</th>
                                        <th class="px-1">Leads Between Subscription Period</th>
                                        <th class="px-1">Leads Recieved this Month</th>
                                        <th class="px-1">Leads Recieved Today</th>
                                        <th class="px-1">Unread Leads this Month</th>
                                        <th class="px-1">Unread Leads Overdue</th>
                                        <th class="px-1">Schedule Task this Month</th>
                                        <th class="px-1">Schedule Task Today</th>
                                        <th class="px-1">Task Overdue</th>
                                        <th class="px-1">Schedule Meeting this Month</th>
                                        <th class="px-1">Schedule Meeting Today</th>
                                        <th class="px-1">Meeting Overdue</th>
                                        <th class="px-1">Self-Lead Created</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($v_members as $v)
                                        @if ($v->category_id == 5)
                                            <tr class="text-center" style="font-weight: bold;">
                                                <td class="text-nowrap text-left p-1">{{ $v->name }}
                                                    /<br />{{ $v->business_name }}</td>
                                                <td class="p-1">{{ $v->total_leads_received }}</td>
                                                <td class="p-1">{{ $v->time_period_lead }}</td>
                                                <td class="p-1">{{ $v->leads_received_this_month }}</td>
                                                <td class="p-1">{{ $v->leads_received_today }}</td>
                                                <td class="p-1">{{ $v->unread_leads_this_month }}</td>
                                                <td class="p-1">{{ $v->unread_leads_overdue }}</td>
                                                <td class="p-1">{{ $v->task_schedule_this_month }}</td>
                                                <td class="p-1">{{ $v->task_schedule_today }}</td>
                                                <td class="p-1">{{ $v->task_overdue }}</td>
                                                <td class="p-1">{{ $v->meeting_schedule_this_month }}</td>
                                                <td class="p-1">{{ $v->meeting_schedule_today }}</td>
                                                <td class="p-1">{{ $v->meeting_overdue }}</td>
                                                <td class="p-1">{{ $v->created_lead }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <h2>RM Statics</h2>
                <div class="my-2">
                    <div class="card text-xs mb-5">
                        <div class="card-header card-header-mod text-light"
                            style="background: linear-gradient(48deg, #8e0000e6, #dfa930b5);">
                            <h6 class="mb-0 text-bold">RM Statics - {{ date('F') }}</h5>
                        </div>
                        <div class="card-body p-0" style="max-width: 100%; overflow-x: auto;">
                            <table class="table-bordered" style="width: 100%;">
                                <thead>
                                    <tr class="text-center">
                                        <th class="text-left px-1">Rm Name</th>
                                        <th class="px-1">Leads Recieved this Month</th>
                                        <th class="px-1">Leads Recieved Today</th>
                                        <th class="px-1">Unread Leads Today</th>
                                        <th class="px-1">Unread Leads this Month</th>
                                        <th class="px-1">Unread Leads Overdue</th>
                                        <th class="px-1">RM Unfollowed Leads</th>
                                        <th class="px-1">Schedule Task this Month</th>
                                        <th class="px-1">Schedule Task Today</th>
                                        <th class="px-1">Task Overdue</th>
                                        <th class="px-1">Forword Lead This Month</th>
                                        <th class="px-1">Forword Lead Today</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($rm_members as $v)
                                        <tr class="text-center" style="font-weight: bold;">
                                            <td class="text-nowrap text-left p-1">{{ $v->name }}</td>
                                            <td class="p-1">{{ $v->total_leads_received_this_month }}</td>
                                            <td class="p-1">{{ $v->total_leads_received_today }}</td>
                                            <td class="p-1">{{ $v->unread_leads_today }}</td>
                                            <td class="p-1">{{ $v->unread_leads_this_month }}</td>
                                            <td class="p-1">{{ $v->total_unread_leads_overdue }}</td>
                                            <td class="p-1">{{ $v->rm_unfollowed_leads }}</td>
                                            <td class="p-1">{{ $v->rm_month_task_leads }}</td>
                                            <td class="p-1">{{ $v->rm_today_task_leads }}</td>
                                            <td class="p-1">{{ $v->rm_task_overdue_leads }}</td>
                                            <td class="p-1">{{ $v->forward_leads_this_month }}</td>
                                            <td class="p-1">{{ $v->forward_leads_today }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>


                <h2>NVRM Statics</h2>
                <div class="my-2">
                    <div class="card text-xs mb-5">
                        <div class="card-header card-header-mod text-light"
                            style="background: linear-gradient(48deg, #8e0000e6, #dfa930b5);">
                            <h6 class="mb-0 text-bold">NVRM Statics - {{ date('F') }}</h5>
                        </div>
                        <div class="card-body p-0" style="max-width: 100%; overflow-x: auto;">
                            <table class="table-bordered" style="width: 100%;">
                                <thead>
                                    <tr class="text-center">
                                        <th class="text-left px-1">Rm Name</th>
                                        <th class="px-1">Leads Recieved this Month</th>
                                        <th class="px-1">Leads Recieved Today</th>
                                        <th class="px-1">Unread Leads Today</th>
                                        <th class="px-1">Unread Leads this Month</th>
                                        <th class="px-1">Unread Leads Overdue</th>
                                        <th class="px-1">NVRM Unfollowed Leads</th>
                                        <th class="px-1">Schedule Task this Month</th>
                                        <th class="px-1">Schedule Task Today</th>
                                        <th class="px-1">Task Overdue</th>
                                        <th class="px-1">Forword Lead This Month</th>
                                        <th class="px-1">Forword Lead Today</th>
                                        @foreach ($categories as $category)
                                            <th class="px-1">{{ $category->name }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($nv_members as $v)
                                        <tr class="text-center" style="font-weight: bold;">
                                            <td class="text-nowrap text-left p-1">{{ $v->name }}</td>
                                            <td class="p-1">{{ $v->leads_received_this_month }}</td>
                                            <td class="p-1">{{ $v->leads_received_today }}</td>
                                            <td class="p-1">{{ $v->unread_leads_today }}</td>
                                            <td class="p-1">{{ $v->unread_leads_this_month }}</td>
                                            <td class="p-1">{{ $v->unread_leads_overdue }}</td>
                                            <td class="p-1">{{ $v->nvrm_unfollowed_leads }}</td>
                                            <td class="p-1">{{ $v->task_schedule_this_month }}</td>
                                            <td class="p-1">{{ $v->task_schedule_today }}</td>
                                            <td class="p-1">{{ $v->task_overdue }}</td>
                                            <td class="p-1">{{ $v->forward_leads_this_month }}</td>
                                            <td class="p-1">{{ $v->forward_leads_today }}</td>
                                            @foreach ($categories as $category)
                                                <td class="px-1">{{ $v->forward_leads_by_category->{$category->name} }}
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="vm-statics my-2">
                    <h2>VM Statics</h2>
                    @foreach ($vm_members as $vm)
                        <div class="card text-xs mb-5">
                            <div class="card-header card-header-mod text-light"
                                style="background: linear-gradient(48deg, #8e0000e6, #dfa930b5);">
                                <h6 class="mb-0 text-bold">{{ $vm->venue_name }}</h5>
                            </div>
                            <div class="card-body p-0">
                                <table class="table-bordered">
                                    <thead>
                                        <tr class="text-center">
                                            <th>VM Name</th>
                                            <th>Leads Received this Month</th>
                                            <th>Leads Received Today</th>
                                            <th>Unread Leads this Month</th>
                                            <th>Unread Leads Today</th>
                                            <th>Unread Leads Overdue</th>
                                            <th>Unfollowed Leads</th>
                                            <th>Schedule Task this Month</th>
                                            <th>Schedule Task Today</th>
                                            <th>Task Overdue</th>
                                            <th>Recce Schedule this Month</th>
                                            <th>Recce Schedule Today</th>
                                            <th>Recce Overdue</th>
                                            <th>Recce (Visits Done) - L2R {{ $vm->l2r }}%</th>
                                            <th>Bookings this Month - R2C {{ $vm->r2c }} %</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr style="font-weight: bold;" class="text-center">
                                            <td>{{ $vm->name }}</td>
                                            <td>{{ $vm->leads_received_this_month }}</td>
                                            <td>{{ $vm->leads_received_today }}</td>
                                            <td>{{ $vm->unread_leads_this_month }}</td>
                                            <td>{{ $vm->unread_leads_today }}</td>
                                            <td>{{ $vm->unread_leads_overdue }}</td>
                                            <td>{{ $vm->unfollowed_leads }}</td>
                                            <td>{{ $vm->task_schedule_this_month }}</td>
                                            <td>{{ $vm->task_schedule_today }}</td>
                                            <td>{{ $vm->task_overdue }}</td>
                                            <td>{{ $vm->recce_schedule_this_month }}</td>
                                            <td>{{ $vm->recce_schedule_today }}</td>
                                            <td>{{ $vm->recce_overdue }}</td>
                                            <td>{{ $vm->recce_done_this_month }}</td>
                                            <td>{{ $vm->bookings_this_month }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    </div>
@section('footer-script')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        (function unfollowed_columns_color_handling() {
            //unread leads
            const unread_leads_overdue_td = document.querySelectorAll('.unread_leads_overdue_td');
            data_arr = [];
            for (let item of unread_leads_overdue_td) {
                data_arr.push(Number(item.innerText));
            }
            data_arr.sort(function(a, b) {
                return a - b
            });
            data_arr.reverse();
            data_arr[0] ? document.querySelector(`.unread_leads_overdue_td[data-value="${data_arr[0]}"]`).style =
                `background-color: rgb(255 51 51 / 80%); color: white` : '';
            data_arr[1] ? document.querySelector(`.unread_leads_overdue_td[data-value="${data_arr[1]}"]`).style =
                `background-color: rgb(255 51 51 / 70%); color: white` : '';
            data_arr[2] ? document.querySelector(`.unread_leads_overdue_td[data-value="${data_arr[2]}"]`).style =
                `background-color: rgb(255 51 51 / 60%); color: white` : '';
            data_arr[3] ? document.querySelector(`.unread_leads_overdue_td[data-value="${data_arr[3]}"]`).style =
                `background-color: rgb(255 51 51 / 50%); color: white` : '';
            data_arr[4] ? document.querySelector(`.unread_leads_overdue_td[data-value="${data_arr[4]}"]`).style =
                `background-color: rgb(255 51 51 / 40%); color: white` : '';

            //task_overdue
            const task_overdue_td = document.querySelectorAll('.task_overdue_td');
            data_arr = [];
            for (let item of task_overdue_td) {
                data_arr.push(Number(item.innerText));
            }
            data_arr.sort(function(a, b) {
                return a - b
            });
            data_arr.reverse();
            data_arr[0] ? document.querySelector(`.task_overdue_td[data-value="${data_arr[0]}"]`).style =
                `background-color: rgb(255 51 51 / 80%); color: white` : '';
            data_arr[1] ? document.querySelector(`.task_overdue_td[data-value="${data_arr[1]}"]`).style =
                `background-color: rgb(255 51 51 / 70%); color: white` : '';
            data_arr[2] ? document.querySelector(`.task_overdue_td[data-value="${data_arr[2]}"]`).style =
                `background-color: rgb(255 51 51 / 60%); color: white` : '';
            data_arr[3] ? document.querySelector(`.task_overdue_td[data-value="${data_arr[3]}"]`).style =
                `background-color: rgb(255 51 51 / 50%); color: white` : '';
            data_arr[4] ? document.querySelector(`.task_overdue_td[data-value="${data_arr[4]}"]`).style =
                `background-color: rgb(255 51 51 / 40%); color: white` : '';

            //unfollowed_leads
            const unfollowed_leads_td = document.querySelectorAll('.unfollowed_leads_td');
            data_arr = [];
            for (let item of unfollowed_leads_td) {
                data_arr.push(Number(item.innerText));
            }
            data_arr.sort(function(a, b) {
                return a - b
            });
            data_arr.reverse();
            data_arr[0] ? document.querySelector(`.unfollowed_leads_td[data-value="${data_arr[0]}"]`).style =
                `background-color: rgb(255 51 51 / 80%); color: white` : '';
            data_arr[1] ? document.querySelector(`.unfollowed_leads_td[data-value="${data_arr[1]}"]`).style =
                `background-color: rgb(255 51 51 / 70%); color: white` : '';
            data_arr[2] ? document.querySelector(`.unfollowed_leads_td[data-value="${data_arr[2]}"]`).style =
                `background-color: rgb(255 51 51 / 60%); color: white` : '';
            data_arr[3] ? document.querySelector(`.unfollowed_leads_td[data-value="${data_arr[3]}"]`).style =
                `background-color: rgb(255 51 51 / 50%); color: white` : '';
            data_arr[4] ? document.querySelector(`.unfollowed_leads_td[data-value="${data_arr[4]}"]`).style =
                `background-color: rgb(255 51 51 / 40%); color: white` : '';

            //total_unactioned_td
            const total_unactioned_td = document.querySelectorAll('.total_unactioned_td');
            data_arr = [];
            for (let item of total_unactioned_td) {
                data_arr.push(Number(item.innerText));
            }
            data_arr.sort(function(a, b) {
                return a - b
            });
            data_arr.reverse();
            data_arr[0] ? document.querySelector(`.total_unactioned_td[data-value="${data_arr[0]}"]`).style =
                `background-color: rgb(255 51 51 / 80%); color: white` : '';
            data_arr[1] ? document.querySelector(`.total_unactioned_td[data-value="${data_arr[1]}"]`).style =
                `background-color: rgb(255 51 51 / 70%); color: white` : '';
            data_arr[2] ? document.querySelector(`.total_unactioned_td[data-value="${data_arr[2]}"]`).style =
                `background-color: rgb(255 51 51 / 60%); color: white` : '';
            data_arr[3] ? document.querySelector(`.total_unactioned_td[data-value="${data_arr[3]}"]`).style =
                `background-color: rgb(255 51 51 / 50%); color: white` : '';
            data_arr[4] ? document.querySelector(`.total_unactioned_td[data-value="${data_arr[4]}"]`).style =
                `background-color: rgb(255 51 51 / 40%); color: white` : '';
        }())

        function wb_recce_target(elem) {
            const current_elem_parent = elem.parentElement.parentElement;
            const recce_done_this_month_value = Number(current_elem_parent.querySelector('.recce_done_this_month_td')
                .innerText);
            const data_vm_id = elem.getAttribute('data-vm_id');
            const wb_recce_target_value = Number(elem.value);

            let formBody = JSON.stringify({
                vm_id: data_vm_id,
                wb_recce_target: wb_recce_target_value
            });
            common_ajax(`{{ route('vm_productivity.manage_process') }}`, 'post', formBody).then(response => response
                .json()).then(data => {
                const wb_recce_percentage_td = current_elem_parent.querySelector('.wb_recce_percentage_td');
                if (recce_done_this_month_value > 0 && wb_recce_target_value > 0) {
                    val = (recce_done_this_month_value / wb_recce_target_value) * 100;
                    val = val.toString().split(".")[0]
                    if (val < 50) {
                        bg_color = "red";
                    } else if (val == 50) {
                        bg_color = "darkgoldenrod";
                    } else if (val > 50 && val < 100) {
                        bg_color = "green";
                    } else if (val >= 100) {
                        bg_color = "darkgreen";
                    } else {
                        bg_color = null;
                    }
                    wb_recce_percentage_td.innerText = `${val} %`;
                    wb_recce_percentage_td.style.backgroundColor = bg_color;

                } else {
                    wb_recce_percentage_td.innerText = `0 %`;
                }
                toastr[`${data.alert_type}`](`${data.message}`);
            })


        }

        const get_last_day_of_the_month = Number("{{ date('t') }}");

        const current_month_days_arr = [];
        for (let i = 1; i <= get_last_day_of_the_month; i++) {
            current_month_days_arr.push(`${i}-{{ date('M') }}`)
        }

        //VanuesChart
        new Chart("venue_chart_months", {
            type: "line",
            data: {
                labels: current_month_days_arr,
                datasets: [{
                        label: 'Total Leads',
                        fill: false,
                        tension: 0, // Use `tension` instead of `lineTension` in Chart.js v3+
                        backgroundColor: "#891010",
                        borderColor: "#891010",
                        data: "{{ $venue_leads_for_this_month }}".split(","),
                    },
                    {
                        label: 'Call',
                        fill: false,
                        tension: 0,
                        backgroundColor: "#a06b14",
                        borderColor: "#a06b14",
                        data: "{{ $venue_call_leads_for_this_month }}".split(","),
                    },
                    {
                        label: 'Form',
                        fill: false,
                        tension: 0,
                        backgroundColor: "#aa559f",
                        borderColor: "#aa559f",
                        data: "{{ $venue_form_leads_for_this_month }}".split(","),
                    },
                    {
                        label: 'WhatsApp',
                        fill: false,
                        tension: 0,
                        backgroundColor: "#618200",
                        borderColor: "#618200",
                        data: "{{ $venue_whatsapp_leads_for_this_month }}".split(","),
                    },
                    {
                        label: 'Ad Data',
                        fill: false,
                        tension: 0,
                        backgroundColor: "#4497bb",
                        borderColor: "#4497bb",
                        data: "{{ $venue_ads_leads_for_this_month }}".split(","),
                    },
                    {
                        label: 'Organic',
                        fill: false,
                        tension: 0,
                        backgroundColor: "#cbe21d",
                        borderColor: "#cbe21d",
                        data: "{{ $venue_organic_leads_for_this_month }}".split(","),
                    },
                ],
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: "top",
                    },
                    tooltip: {
                        mode: "index",
                        intersect: false,
                        callbacks: {
                            label: function(tooltipItem) {
                                const label = tooltipItem.dataset.label || "";
                                const value = tooltipItem.raw;
                                return `${label}: ${value}`;
                            },
                        },
                    },
                },
                scales: {
                    x: {
                        type: "category",
                        title: {
                            // display: true,
                            // text: "Days",
                        },
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            // display: true,
                            // text: "Leads Count",
                        },
                        ticks: {
                            min: 1, // Minimum value on the y-axis
                        },
                    },
                },
            },
        });


        // console.log("{{ $venue_leads_for_this_year }}");

        new Chart("venue_chart_years", {
            type: "bar",
            data: {
                labels: "{{ $yearly_calendar }}".split(','), // Ensure this outputs a valid string
                datasets: [{
                        label: 'Ad Data',
                        backgroundColor: "#4497bb",
                        borderColor: "#4497bb",
                        borderWidth: 1,
                        data: "{{ $venue_ads_leads_for_this_year }}".split(
                            ","), // Ensure this outputs a valid string
                    },
                    {
                        label: 'Organic',
                        backgroundColor: "#cbe21d",
                        borderColor: "#cbe21d",
                        borderWidth: 1,
                        data: "{{ $venue_organic_leads_for_this_year }}".split(
                            ","), // Ensure this outputs a valid string
                    },
                    {
                        label: 'Total Leads',
                        backgroundColor: "#891010",
                        borderColor: "#891010",
                        borderWidth: 1,
                        data: "{{ $venue_leads_for_this_year }}".split(
                            ","), // Ensure this outputs a valid string
                    },
                ],
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: "top",
                    },
                    tooltip: {
                        mode: "index",
                        intersect: false,
                        callbacks: {
                            label: function(tooltipItem) {
                                const label = tooltipItem.dataset.label || '';
                                const value = tooltipItem.raw;
                                return `${label}: ${value}`;
                            },
                        },
                    },
                },
                scales: {
                    x: {
                        type: "category",
                        title: {
                            display: false,
                            text: "Months",
                        },
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: false,
                            text: "Leads Count",
                        },
                        ticks: {
                            min: 1, // Minimum value on the y-axis
                        },
                    },
                },
            },
        });


        new Chart("nv_chart_months", {
    type: "line",
    data: {
        labels: current_month_days_arr,
        datasets: [{
            fill: false,
            tension: 0,
            backgroundColor: "#891010",
            borderColor: "rgba(0,0,255,0.1)",
            data: ("{{ $nv_leads_for_this_month }}").split(",")
        }]
    },
    options: {
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: false,
                min: 1
            }
        }
    }
});

new Chart("nv_chart_years", {
    type: "bar",
    data: {
        labels: ("{{ $yearly_calendar }}").split(','),
        datasets: [{
            fill: false,
            tension: 0,
            backgroundColor: "#891010",
            borderColor: "rgba(0,0,255,0.1)",
            data: ("{{ $nv_leads_for_this_year }}").split(',')
        }]
    },
    options: {
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: false,
                min: 1
            }
        }
    }
});




        document.getElementById('month-selector').addEventListener('change', function() {
            const selectedMonth = this.value.split(' ')[0];
            const selectedYear = this.value.split(' ')[1];
            if (selectedMonth == 'Current' && selectedYear == 'Month') {
        location.reload();
        return;
    }
            document.getElementById('selected-month').innerText = `${selectedMonth} ${selectedYear}`;

            fetch(`{{ route('admin.dashboard.data') }}?month=${selectedMonth}&year=${selectedYear}`)
                .then(response => response.json())
                .then(data => {
                    const chart = Chart.getChart("venue_chart_months");
                    chart.data.datasets[0].data = data.venue_leads.split(",");
                    chart.data.datasets[1].data = data.call_leads.split(",");
                    chart.data.datasets[2].data = data.form_leads.split(",");
                    chart.data.datasets[3].data = data.whatsapp_leads.split(",");
                    chart.data.datasets[4].data = data.ads_leads.split(",");
                    chart.data.datasets[5].data = data.organic_leads.split(",");

                    const venueLeads = data.venue_leads.split(",").map(Number);
            const count = venueLeads.length;
            const sum = venueLeads.reduce((acc, val) => acc + val, 0);
            const average = parseFloat((sum / count).toFixed(2));
            document.getElementById('avarageLeadId').innerText = `${average}`;
                    chart.update();
                })
                .catch(err => console.error('Error fetching chart data:', err));
        });
    </script>
@endsection

@endsection
