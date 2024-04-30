@extends('vendormanager.layouts.app')
@section('title', 'Dashboard | Manager')
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
            <div class="row">
                <div class="col-lg-3 col-6">
                    <a target="_blank" href="{{route('vendormanager.team.list')}}" class="text-light">
                        <div class="small-box text-sm" style="background: var(--wb-renosand);">
                            <div class="inner">
                                <h3>{{$v_members->count()}}</h3>
                                <p>Total Vendors</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-stats-bars"></i>
                            </div>
                            <div class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 col-6">
                    <a target="_blank" href="{{route('vendormanager.lead.list')}}" class="text-light">
                        <div class="small-box text-sm" style="background: var(--wb-dark-red);">
                            <div class="inner">
                                <h3>{{$total_leads_received}}</h3>
                                <p>Total Leads Received</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>
                            <div class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></div>
                        </div>
                    </a>
                </div>
            </div>

            <div class="vm-statics my-2">
                <h2>Vendor Statics</h2>
                @foreach ($v_members as $v)
                <div class="card text-xs mb-5">
                    <div class="card-header card-header-mod text-light" style="background: linear-gradient(48deg, #8e0000e6, #dfa930b5);">
                        <h6 class="mb-0 text-bold">{{$v->name}} -- {{$v->business_name}}</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table-bordered">
                                <thead>
                                    <tr class="text-center">
                                        <th class="p-1">Vendor Name</th>
                                        <th class="p-1">Total Leads Recieved</th>
                                        <th class="p-1">Leads Recieved this Month</th>
                                        <th class="p-1">Leads Recieved Today</th>
                                        <th class="p-1">Unread Leads this Month</th>
                                        <th class="p-1">Unread Leads Today</th>
                                        <th class="p-1">Unread Leads Overdue</th>
                                        <th class="p-1">Schedule Task this Month</th>
                                        <th class="p-1">Schedule Task Today</th>
                                        <th class="p-1">Task Overdue</th>
                                        <th class="p-1">Schedule Meeting this Month</th>
                                        <th class="p-1">Schedule Meeting Today</th>
                                        <th class="p-1">Meeting Overdue</th>
                                        <th class="p-1">Self-Lead Created</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="font-weight: bold;" class="text-center">
                                        <td class="p-1">{{$v->name}}</td>
                                        <td class="p-1">{{$v->total_leads_received}}</td>
                                        <td class="p-1">{{$v->leads_received_this_month}}</td>
                                        <td class="p-1">{{$v->leads_received_today}}</td>
                                        <td class="p-1">{{$v->unread_leads_this_month}}</td>
                                        <td class="p-1">{{$v->unread_leads_today}}</td>
                                        <td class="p-1">{{$v->unread_leads_overdue}}</td>
                                        <td class="p-1">{{$v->task_schedule_this_month}}</td>
                                        <td class="p-1">{{$v->task_schedule_today}}</td>
                                        <td class="p-1">{{$v->task_overdue}}</td>
                                        <td class="p-1">{{$v->meeting_schedule_this_month}}</td>
                                        <td class="p-1">{{$v->meeting_schedule_today}}</td>
                                        <td class="p-1">{{$v->meeting_overdue}}</td>
                                        <td class="p-1">{{$v->created_lead}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
</div>
@endsection
