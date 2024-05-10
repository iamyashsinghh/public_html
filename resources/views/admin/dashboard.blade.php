@extends('admin.layouts.app')
@section('title', 'Dashboard | Admin')
@section('header-css')
<link rel="stylesheet" href="{{asset('plugins/charts/chart.css')}}">
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
                    <!-- small box -->
                    <div class="small-box text-sm text-light" style="background: var(--wb-renosand);">
                        <div class="inner">
                            <h3>{{$total_vendors}}</h3>
                            <p>Total Vendors</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                        <a href="{{route('admin.vendor.list')}}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box text-sm text-light" style="background: var(--wb-dark-red);">
                        <div class="inner">
                            <h3>{{$total_team}}</h3>
                            <p>Total Team Members</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                        </div>
                        <a href="{{route('admin.team.list')}}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box text-sm text-light" style="background: var(--wb-renosand);">
                        <div class="inner">
                            <h3>{{$total_venue_leads}}</h3>
                            <p>Total Venue Leads</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                        <a href="{{route('admin.lead.list')}}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box text-sm text-light" style="background: var(--wb-dark-red);">
                        <div class="inner">
                            <h3>{{$total_nv_leads}}</h3>
                            <p>Total NV Leads</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                        <a href="{{route('admin.nvlead.list')}}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="card text-xs">
                        <div class="card-header border-0 text-light" style="background-color: var(--wb-renosand);">
                            <h3 class="card-title">
                                <i class="fas fa-th mr-1"></i>
                                Venue Leads of {{date('F')}} Month
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
                            <canvas class="chart" id="venue_chart_months" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="card text-xs">
                        <div class="card-header border-0 text-light" style="background-color: var(--wb-renosand);">
                            <h3 class="card-title">
                                <i class="fas fa-th mr-1"></i>
                                Venue Leads of Year {{date('Y', strtotime('-1 Year'))}} - {{date('Y')}}
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
                            <canvas class="chart" id="venue_chart_years" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
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
                                NV Leads of {{date('F')}} Month
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
                            <canvas class="chart" id="nv_chart_months" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="card text-xs">
                        <div class="card-header border-0 text-light" style="background-color: var(--wb-renosand);">
                            <h3 class="card-title">
                                <i class="fas fa-th mr-1"></i>
                                NV Leads of Year {{date('Y', strtotime('-1 Year'))}} - {{date('Y')}}
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
                            <canvas class="chart" id="nv_chart_years" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="my-2">
                <div class="card text-xs mb-5">
                    <div class="card-header card-header-mod text-light" style="background: linear-gradient(48deg, #8e0000e6, #dfa930b5);">
                        <h6 class="mb-0 text-bold">VM Productivity - {{date('F')}}</h5>
                    </div>
                    <div class="card-body p-0">
                        <table class="table-bordered">
                            <thead>
                                <tr class="text-center">
                                    <th class="text-nowrap text-left px-2">CLH Name</th>
                                    <th class="text-left text-nowrap px-2">VM Name /<br/> Venue Name</th>
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
                                @endphp
                                @foreach ($vm_members as $vm)
                                    <tr class="text-center" style="font-weight: bold;">
                                        <td class="text-nowrap text-left px-2">{{$vm->get_manager ? $vm->get_manager->name : 'N/A'}}</td>
                                        <td class="text-left text-nowrap px-2">{{$vm->name}} /<br/> {{$vm->venue_name}}</td>
                                        <td><input type="number" style="width: 50px;" data-vm_id="{{$vm->id}}" value="{{$vm->wb_recce_target}}" onchange="wb_recce_target(this)"></td>
                                        <td class="recce_done_this_month_td">{{$vm->recce_done_this_month}}</td>
                                        @php
                                            $val = $vm->wb_recce_percentage;
                                            if($val < 50){
                                                $bg_color = "red";
                                            }elseif($val == 50){
                                                $bg_color = "darkgoldenrod";
                                            }elseif($val > 50 && $val < 100){
                                                $bg_color = "green";
                                            }elseif($val >= 100 ){
                                                $bg_color = "darkgreen";
                                            }else{
                                                $bg_color = null;
                                            }

                                            $total_unactioned = $vm->unread_leads_overdue+$vm->task_overdue+$vm->unfollowed_leads;
                                            $grand_total_unactioned += $total_unactioned;

                                            $total_unread_leads_overdue += $vm->unread_leads_overdue;
                                            $total_task_overdue += $vm->task_overdue;
                                            $total_unfollowed_leads += $vm->unfollowed_leads;
                                        @endphp
                                        <td class="text-nowrap wb_recce_percentage_td {{$bg_color != null ? 'text-white' : ''}}"
                                        style="background-color: {{$bg_color}}">{{$vm->wb_recce_percentage}} %</td>
                                        <td>{{$vm->bookings_this_month}}</td>
                                        <td class="text-nowrap">{{$vm->l2r}} %</td>
                                        <td class="text-nowrap">{{$vm->r2c}} %</td>
                                        <td>{{$vm->leads_received_this_month}}</td>
                                        @php

                                        @endphp
                                        <td data-value="{{$vm->unread_leads_overdue}}" class="unread_leads_overdue_td">{{$vm->unread_leads_overdue}}</td>
                                        <td data-value="{{$vm->task_overdue}}" class="task_overdue_td">{{$vm->task_overdue}}</td>
                                        <td data-value="{{$vm->unfollowed_leads}}" class="unfollowed_leads_td">{{$vm->unfollowed_leads}}</td>
                                        <td data-value="{{$total_unactioned}}" class="total_unactioned_td">{{$total_unactioned}}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="my-2">
                <div class="card text-xs mb-5">
                    <div class="card-header card-header-mod text-light" style="background: linear-gradient(48deg, #8e0000e6, #dfa930b5);">
                        <h6 class="mb-0 text-bold">Vendor Productivity - {{date('F')}}</h5>
                    </div>
                    <div class="card-body p-0" style="max-width: 100%; overflow-x: auto;">
                        <table class="table-bordered" style="width: 100%;">
                            <thead>
                                <tr class="text-center">
                                    <th class="text-left px-1">Vendor Name /<br/> Business Name</th>
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
                                    <tr class="text-center" style="font-weight: bold;">
                                        <td class="text-nowrap text-left p-1">{{$v->name}} /<br />{{$v->business_name}}</td>
                                        <td class="p-1">{{$v->total_leads_received}}</td>
                                        <td class="p-1">{{$v->time_period_lead}}</td>
                                        <td class="p-1">{{$v->leads_received_this_month}}</td>
                                        <td class="p-1">{{$v->leads_received_today}}</td>
                                        <td class="p-1">{{$v->unread_leads_this_month}}</td>
                                        <td class="p-1">{{$v->unread_leads_overdue}}</td>
                                        <td class="p-1">{{$v->task_schedule_this_month}}</td>
                                        <td class="p-1">{{$v->task_schedule_today}}</td>
                                        <td class="p-1">{{$v->task_overdue}}</td>
                                        <td class="p-1">{{$v->meeting_schedule_this_month}}</td>
                                        <td class="p-1">{{$v->meeting_schedule_today}}</td>
                                        <td class="p-1">{{$v->meeting_overdue}}</td>
                                        <td class="p-1">{{$v->created_lead}}</td>
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
                    <div class="card-header card-header-mod text-light" style="background: linear-gradient(48deg, #8e0000e6, #dfa930b5);">
                        <h6 class="mb-0 text-bold">{{$vm->venue_name}}</h5>
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
                                    <th>Recce (Visits Done) - L2R {{$vm->l2r}}%</th>
                                    <th>Bookings this Month - R2C {{$vm->r2c}} %</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr style="font-weight: bold;" class="text-center">
                                    <td>{{$vm->name}}</td>
                                    <td>{{$vm->leads_received_this_month}}</td>
                                    <td>{{$vm->leads_received_today}}</td>
                                    <td>{{$vm->unread_leads_this_month}}</td>
                                    <td>{{$vm->unread_leads_today}}</td>
                                    <td>{{$vm->unread_leads_overdue}}</td>
                                    <td>{{$vm->unfollowed_leads}}</td>
                                    <td>{{$vm->task_schedule_this_month}}</td>
                                    <td>{{$vm->task_schedule_today}}</td>
                                    <td>{{$vm->task_overdue}}</td>
                                    <td>{{$vm->recce_schedule_this_month}}</td>
                                    <td>{{$vm->recce_schedule_today}}</td>
                                    <td>{{$vm->recce_overdue}}</td>
                                    <td>{{$vm->recce_done_this_month}}</td>
                                    <td>{{$vm->bookings_this_month}}</td>
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
<script src="{{asset('plugins/charts/chart.bundle.min.js')}}"></script>
<script>
    (function unfollowed_columns_color_handling(){
        //unread leads
        const unread_leads_overdue_td = document.querySelectorAll('.unread_leads_overdue_td');
        data_arr = [];
        for(let item of unread_leads_overdue_td){
            data_arr.push(Number(item.innerText));
        }
        data_arr.sort(function(a, b){return a-b});
        data_arr.reverse();
        data_arr[0] ? document.querySelector(`.unread_leads_overdue_td[data-value="${data_arr[0]}"]`).style = `background-color: rgb(255 51 51 / 80%); color: white` : '';
        data_arr[1] ? document.querySelector(`.unread_leads_overdue_td[data-value="${data_arr[1]}"]`).style = `background-color: rgb(255 51 51 / 70%); color: white` : '';
        data_arr[2] ? document.querySelector(`.unread_leads_overdue_td[data-value="${data_arr[2]}"]`).style = `background-color: rgb(255 51 51 / 60%); color: white` : '';
        data_arr[3] ? document.querySelector(`.unread_leads_overdue_td[data-value="${data_arr[3]}"]`).style = `background-color: rgb(255 51 51 / 50%); color: white` : '';
        data_arr[4] ? document.querySelector(`.unread_leads_overdue_td[data-value="${data_arr[4]}"]`).style = `background-color: rgb(255 51 51 / 40%); color: white` : '';

        //task_overdue
        const task_overdue_td = document.querySelectorAll('.task_overdue_td');
        data_arr = [];
        for(let item of task_overdue_td){
            data_arr.push(Number(item.innerText));
        }
        data_arr.sort(function(a, b){return a-b});
        data_arr.reverse();
        data_arr[0] ? document.querySelector(`.task_overdue_td[data-value="${data_arr[0]}"]`).style = `background-color: rgb(255 51 51 / 80%); color: white` : '';
        data_arr[1] ? document.querySelector(`.task_overdue_td[data-value="${data_arr[1]}"]`).style = `background-color: rgb(255 51 51 / 70%); color: white` : '';
        data_arr[2] ? document.querySelector(`.task_overdue_td[data-value="${data_arr[2]}"]`).style = `background-color: rgb(255 51 51 / 60%); color: white` : '';
        data_arr[3] ? document.querySelector(`.task_overdue_td[data-value="${data_arr[3]}"]`).style = `background-color: rgb(255 51 51 / 50%); color: white` : '';
        data_arr[4] ? document.querySelector(`.task_overdue_td[data-value="${data_arr[4]}"]`).style = `background-color: rgb(255 51 51 / 40%); color: white` : '';

        //unfollowed_leads
        const unfollowed_leads_td = document.querySelectorAll('.unfollowed_leads_td');
        data_arr = [];
        for(let item of unfollowed_leads_td){
            data_arr.push(Number(item.innerText));
        }
        data_arr.sort(function(a, b){return a-b});
        data_arr.reverse();
        data_arr[0] ? document.querySelector(`.unfollowed_leads_td[data-value="${data_arr[0]}"]`).style = `background-color: rgb(255 51 51 / 80%); color: white` : '';
        data_arr[1] ? document.querySelector(`.unfollowed_leads_td[data-value="${data_arr[1]}"]`).style = `background-color: rgb(255 51 51 / 70%); color: white` : '';
        data_arr[2] ? document.querySelector(`.unfollowed_leads_td[data-value="${data_arr[2]}"]`).style = `background-color: rgb(255 51 51 / 60%); color: white` : '';
        data_arr[3] ? document.querySelector(`.unfollowed_leads_td[data-value="${data_arr[3]}"]`).style = `background-color: rgb(255 51 51 / 50%); color: white` : '';
        data_arr[4] ? document.querySelector(`.unfollowed_leads_td[data-value="${data_arr[4]}"]`).style = `background-color: rgb(255 51 51 / 40%); color: white` : '';

        //total_unactioned_td
        const total_unactioned_td = document.querySelectorAll('.total_unactioned_td');
        data_arr = [];
        for(let item of total_unactioned_td){
            data_arr.push(Number(item.innerText));
        }
        data_arr.sort(function(a, b){return a-b});
        data_arr.reverse();
        data_arr[0] ? document.querySelector(`.total_unactioned_td[data-value="${data_arr[0]}"]`).style = `background-color: rgb(255 51 51 / 80%); color: white` : '';
        data_arr[1] ? document.querySelector(`.total_unactioned_td[data-value="${data_arr[1]}"]`).style = `background-color: rgb(255 51 51 / 70%); color: white` : '';
        data_arr[2] ? document.querySelector(`.total_unactioned_td[data-value="${data_arr[2]}"]`).style = `background-color: rgb(255 51 51 / 60%); color: white` : '';
        data_arr[3] ? document.querySelector(`.total_unactioned_td[data-value="${data_arr[3]}"]`).style = `background-color: rgb(255 51 51 / 50%); color: white` : '';
        data_arr[4] ? document.querySelector(`.total_unactioned_td[data-value="${data_arr[4]}"]`).style = `background-color: rgb(255 51 51 / 40%); color: white` : '';
    }())
    function wb_recce_target(elem){
        const current_elem_parent = elem.parentElement.parentElement;
        const recce_done_this_month_value = Number(current_elem_parent.querySelector('.recce_done_this_month_td').innerText);
        const data_vm_id = elem.getAttribute('data-vm_id');
        const wb_recce_target_value = Number(elem.value);

        let formBody = JSON.stringify({
            vm_id: data_vm_id,
            wb_recce_target: wb_recce_target_value
        });
        common_ajax(`{{route('vm_productivity.manage_process')}}`, 'post', formBody).then(response => response.json()).then(data => {
            const wb_recce_percentage_td = current_elem_parent.querySelector('.wb_recce_percentage_td');
            if(recce_done_this_month_value > 0 && wb_recce_target_value > 0){
                val = (recce_done_this_month_value/wb_recce_target_value)*100;
                val = val.toString().split(".")[0]
                if(val < 50){
                    bg_color = "red";
                }else if(val == 50){
                    bg_color = "darkgoldenrod";
                }else if(val > 50 && val < 100){
                    bg_color = "green";
                }else if(val >= 100 ){
                    bg_color = "darkgreen";
                }else{
                    bg_color = null;
                }
                wb_recce_percentage_td.innerText = `${val} %`;
                wb_recce_percentage_td.style.backgroundColor = bg_color;

            }else{
                wb_recce_percentage_td.innerText = `0 %`;
            }
            toastr[`${data.alert_type}`](`${data.message}`);
        })


    }

    const get_last_day_of_the_month = Number("{{date('t')}}");

    const current_month_days_arr = [];
    for (let i = 1; i <= get_last_day_of_the_month; i++) {
        current_month_days_arr.push(`${i}-{{date('M')}}`)
    }

    //VanuesChart
    new Chart("venue_chart_months", {
        type: "line",
        data: {
            labels: current_month_days_arr,
            datasets: [{
                fill: false,
                lineTension: 0,
                backgroundColor: "#891010",
                borderColor: "rgba(0,0,255,0.1)",
                data: ("{{$venue_leads_for_this_month}}").split(",")
            }]
        },
        options: {
            legend: {
                display: false
            },
            scales: {
                yAxes: [{
                    ticks: {
                        min: 1
                    }
                }],
            },
            //  title: {
            //     display: true,
            //     text: "Last 30 Days Leads (Vanues)"
            //  }
        }
    });

    new Chart("venue_chart_years", {
        type: "bar",
        data: {
            labels: ("{{$yearly_calendar}}").split(','),
            datasets: [{
                fill: false,
                lineTension: 0,
                backgroundColor: "#891010",
                borderColor: "rgba(0,0,255,0.1)",
                data: ("{{$venue_leads_for_this_year}}").split(',')
            }]
        },
        options: {
            legend: {
                display: false
            },
            scales: {
                yAxes: [{
                    ticks: {
                        min: 1
                    }
                }],
            },
        }
    });

    // NonVenue Charts
    new Chart("nv_chart_months", {
        type: "line",
        data: {
            labels: current_month_days_arr,
            datasets: [{
                fill: false,
                lineTension: 0,
                backgroundColor: "#891010",
                borderColor: "rgba(0,0,255,0.1)",
                data: ("{{$nv_leads_for_this_month}}").split(",")
            }]
        },
        options: {
            legend: {
                display: false
            },
            scales: {
                yAxes: [{
                    ticks: {
                        min: 1
                    }
                }],
            },
        }
    });

    new Chart("nv_chart_years", {
        type: "bar",
        data: {
            labels: ("{{$yearly_calendar}}").split(','),
            datasets: [{
                fill: false,
                lineTension: 0,
                backgroundColor: "#891010",
                borderColor: "rgba(0,0,255,0.1)",
                data: ("{{$nv_leads_for_this_year}}").split(',')
            }]
        },
        options: {
            legend: {
                display: false
            },
            scales: {
                yAxes: [{
                    ticks: {
                        min: 1
                    }
                }],
            },
        }
    });
</script>
@endsection

@endsection
