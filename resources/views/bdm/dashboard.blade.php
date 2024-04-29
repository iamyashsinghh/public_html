@extends('bdm.layouts.app')
@section('title', 'Dashboard | BDM')
@section('navbar-left-links')
<li class="nav-item">
    <a href="javascript:void(0);" class="nav-link" onclick="handle_create_lead()">Create New Lead</a>
</li>
@endsection
@section('main')
    <div class="content-wrapper pb-5">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Dashboard | Work Report</h1>
                    </div>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <h3>Leads</h3>
                    </div>
                    <div class="col-lg-3 col-6">
                        <a target="_blank" href="{{ route('bdm.lead.list', 'total_leads_received_this_month') }}"
                            class="text-light">
                            <div class="small-box text-sm bg-secondary">
                                <div class="inner">
                                    <h3>{{ $total_leads_received_this_month }}</h3>
                                    <p>Leads Received this Month</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-bag"></i>
                                </div>
                                <div class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-6">
                        <a target="_blank" href="{{ route('bdm.lead.list', 'total_leads_received_today') }}"
                            class="text-light">
                            <div class="small-box text-sm" style="background: cadetblue;">
                                <div class="inner">
                                    <h3>{{ $total_leads_received_today }}</h3>
                                    <p>Leads Received Today</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-bag"></i>
                                </div>
                                <div class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-6">
                        <a target="_blank" href="{{ route('bdm.lead.list', 'unread_leads_this_month') }}"
                            class="text-light">
                            <div class="small-box text-sm {{ 8 > 4 ? 'bg-danger' : '' }}" style="background-color: #995d62">
                                <div class="inner">
                                    <h3>{{ $unread_leads_this_month }}</h3>
                                    <p>Unread Leads this Month</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-bag"></i>
                                </div>
                                <div class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-6">
                        <a target="_blank" href="{{ route('bdm.lead.list', 'unread_leads_today') }}" class="text-light">
                            <div class="small-box text-sm {{ 7 > 4 ? 'bg-danger' : '' }}" style="background-color: #995d62">
                                <div class="inner">
                                    <h3>{{ $unread_leads_today }}</h3>
                                    <p>Unread Leads Today</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-bag"></i>
                                </div>
                                <div class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-6">
                        <a target="_blank" href="{{ route('bdm.lead.list', 'total_unread_leads_overdue') }}"
                            class="text-light">
                            <div class="small-box text-sm {{ 6 > 4 ? 'bg-danger' : '' }}"
                                style="background-color: #995d62">
                                <div class="inner">
                                    <h3>{{ $total_unread_leads_overdue }}</h3>
                                    <p>Total Unread Leads Overdue</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-bag"></i>
                                </div>
                                <div class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-6">
                        <a target="_blank" href="{{ route('bdm.lead.list', 'bdm_unfollowed_leads') }}" class="text-light">
                            <div class="small-box text-sm bg-success">
                                <div class="inner">
                                    <h3>{{ $bdm_unfollowed_leads }}</h3>
                                    <p>Unfollowed Leads</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-bag"></i>
                                </div>
                                <div class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-12 mt-3">
                        <h3>Tasks</h3>
                    </div>
                    <div class="col-lg-3 col-6">
                        <a target="_blank" href="{{ route('bdm.task.list', 'task_schedule_this_month') }}" class="text-light">
                            <div class="small-box text-sm bg-success">
                                <div class="inner">
                                    <h3>{{ $bdm_month_task_leads }}</h3>
                                    <p>Task Schedule this Month</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-bag"></i>
                                </div>
                                <div class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-6">
                        <a target="_blank" href="{{ route('bdm.task.list', 'task_schedule_today') }}" class="text-light">
                            <div class="small-box text-sm" style="background-color: cadetblue;">
                                <div class="inner">
                                    <h3>{{ $bdm_today_task_leads }}</h3>
                                    <p>Task Schedule Today</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-bag"></i>
                                </div>
                                <div class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-6">
                        <a target="_blank" href="{{ route('bdm.task.list', 'total_task_overdue') }}"
                            class="text-light">
                            <div class="small-box text-sm bg-secondary">
                                <div class="inner">
                                    <h3>{{ $bdm_task_overdue_leads }}</h3>
                                    <p>Total Task Overdue</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-bag"></i>
                                </div>
                                <div class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-12 mt-3">
                        <h3>Meetings</h3>
                    </div>
                    <div class="col-lg-3 col-6">
                        <a target="_blank" href="{{ route('bdm.meeting.list', 'meeting_schedule_this_month') }}"
                            class="text-light">
                            <div class="small-box text-sm bg-success">
                                <div class="inner">
                                    <h3>{{$bdm_month_meeting_leads}}</h3>
                                    <p>Meeting Schedule this Month</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-bag"></i>
                                </div>
                                <div class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-6">
                        <a target="_blank" href="{{ route('bdm.meeting.list', 'meeting_schedule_today') }}"
                            class="text-light">
                            <div class="small-box text-sm" style="background-color: cadetblue;">
                                <div class="inner">
                                    <h3>{{$bdm_today_meeting_leads}}</h3>
                                    <p>Meeting Schedule Today</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-bag"></i>
                                </div>
                                <div class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-6">
                        <a target="_blank" href="{{ route('bdm.meeting.list', 'total_meeting_overdue') }}" class="text-light">
                            <div class="small-box text-sm bg-secondary">
                                <div class="inner">
                                    <h3>{{$bdm_meeting_overdue_leads}}</h3>
                                    <p>Total Meeting Overdue</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-bag"></i>
                                </div>
                                <div class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-6">
                        <a target="_blank" href="{{ route('bdm.meeting.list', 'meeting_done_this_month') }}"
                            class="text-light">
                            <div class="small-box text-sm bg-success">
                                <div class="inner">
                                    <h3>{{$meeting_done_this_month}}</h3>
                                    <p style="font-size: 14px;">Meeting Done this Month / L2M -- {{$l2m}}%</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-bag"></i>
                                </div>
                                <div class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-12 mt-3">
                        <h3>Order Signed</h3>
                    </div>
                    <div class="col-lg-3 col-6">
                        <a target="_blank" href="{{ route('bdm.meeting.list', 'order_signed_this_month') }}"
                            class="text-light">
                            <div class="small-box text-sm bg-info">
                                <div class="inner">
                                    <h3>{{$order_signed_this_month}}</h3>
                                    <p style="font-size: 14px;">Order Signed this Month / M2O -- {{$m2o}}%</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-bag"></i>
                                </div>
                                <div class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
@section('footer-script')
@include('bdm.lead.manage_lead_modal');
@endsection
