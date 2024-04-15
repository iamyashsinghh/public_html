@extends('nonvenue.layouts.app')
@section('title', 'Dashboard | NVRM')
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
                <div class="col-lg-12">
                    <h3>Leads</h3>
                </div>
                <div class="col-lg-3 col-6">
                    <a target="_blank" href="{{route('nonvenue.lead.list', 'leads_received_this_month')}}" class="text-light">
                        <div class="small-box text-sm bg-secondary">
                            <div class="inner">
                                <h3>{{$total_leads_received_this_month}}</h3>
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
                    <a target="_blank" href="{{route('nonvenue.lead.list', 'leads_received_today')}}" class="text-light">
                        <div class="small-box text-sm" style="background: cadetblue;">
                            <div class="inner">
                                <h3>{{$total_leads_received_today}}</h3>
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
                    <a target="_blank" href="{{route('nonvenue.lead.list', 'unread_leads_this_month')}}" class="text-light">
                        <div class="small-box text-sm" style="background: #995d62;">
                            <div class="inner">
                                <h3>{{$unread_leads_this_month}}</h3>
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
                    <a target="_blank" href="{{route('nonvenue.lead.list', 'unread_leads_today')}}" class="text-light">
                        <div class="small-box text-sm" style="background: #995d62;">
                            <div class="inner">
                                <h3>{{$unread_leads_today}}</h3>
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
                    <a target="_blank" href="{{route('nonvenue.lead.list', 'total_unread_leads_overdue')}}" class="text-light">

                        <div class="small-box text-sm" style="background: #995d62;">
                            <div class="inner">
                                <h3>{{$total_unread_leads_overdue}}</h3>
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
                    <a target="_blank" href="{{route('nonvenue.lead.list', 'nvrm_unfollowed_leads')}}" class="text-light">
                        <div class="small-box text-sm bg-success">
                            <div class="inner">
                                <h3>{{$nvrm_unfollowed_leads}}</h3>
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
                    <a target="_blank" href="{{route('nonvenue.task.list', 'task_schedule_this_month')}}" class="text-light">
                        <div class="small-box text-sm bg-success">
                            <div class="inner">
                                <h3>{{$nvrm_month_task_leads}}</h3>
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
                    <a target="_blank" href="{{route('nonvenue.task.list', 'task_schedule_today')}}" class="text-light">
                        <div class="small-box text-sm" style="background-color: cadetblue;">
                            <div class="inner">
                                <h3>{{$nvrm_today_task_leads}}</h3>
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
                    <a target="_blank" href="{{route('nonvenue.task.list', 'total_task_overdue')}}" class="text-light">
                        <div class="small-box text-sm bg-secondary">
                            <div class="inner">
                                <h3>{{$nvrm_task_overdue_leads}}</h3>
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
                    <h3>Vendors Help Support</h3>
                </div>
                <div class="col-lg-3 col-6">
                    <a target="_blank" href="{{route('nonvenue.vendor.help.list', 'vendor_total_helped')}}" class="text-light">
                        <div class="small-box text-sm bg-success">
                            <div class="inner">
                                <h3>{{$vendor_total_helped}}</h3>
                                <p>Vendors Issue Resolved</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>
                            <div class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 col-6">
                    <a target="_blank" href="{{route('nonvenue.vendor.help.list', 'vendor_non_responsed_help')}}" class="text-light">
                        <div class="small-box text-sm" style="background-color: cadetblue;">
                            <div class="inner">
                                <h3>{{$vendor_non_responsed_help}}</h3>
                                <p>Need to be Responsed</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>
                            <div class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 col-6">
                    <a target="_blank" href="{{route('nonvenue.vendor.help.list')}}" class="text-light">
                        <div class="small-box text-sm bg-secondary">
                            <div class="inner">
                                <h3>{{$vendor_all_help}}</h3>
                                <p>Total Vendors Help Support</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>
                            <div class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-12 my-3">
                    <h3>Total Vendors Help</h3>
                </div>
                <div class="col-lg-3 col-6">
                    <a target="_blank" href="{{route('nonvenue.lead.list', 'forward_leads_this_month')}}" class="text-light">
                        <div class="small-box text-sm" style="background: cadetblue;">
                            <div class="inner">
                                <h3>{{$forward_leads_this_month}}</h3>
                                <p>Forward Leads this Month</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>
                            <div class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 col-6">
                    <a target="_blank" href="{{route('nonvenue.lead.list', 'forward_leads_today')}}" class="text-light">
                        <div class="small-box text-sm bg-success">
                            <div class="inner">
                                <h3>{{$forward_leads_today}}</h3>
                                <p>Forward Leads Today</p>
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
