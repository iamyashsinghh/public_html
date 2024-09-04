@extends('team.layouts.app')
@section('title', 'Dashboard | Team')
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
                    <a target="_blank" href="{{route('team.lead.list', 'leads_received_this_month')}}" class="text-light">
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
                    <a target="_blank" href="{{route('team.lead.list', 'leads_received_today')}}" class="text-light">
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
                    <a target="_blank" href="{{route('team.lead.list', 'unread_leads_this_month')}}" class="text-light">
                        <div class="small-box text-sm {{$unread_leads_this_month > 4 ? 'bg-danger' : ''}}" style="background-color: #995d62">
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
                    <a target="_blank" href="{{route('team.lead.list', 'unread_leads_today')}}" class="text-light">
                        <div class="small-box text-sm {{$unread_leads_today > 4 ? 'bg-danger' : ''}}" style="background-color: #995d62">
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
                    <a target="_blank" href="{{route('team.lead.list', 'total_unread_leads_overdue')}}" class="text-light">
                        <div class="small-box text-sm {{$total_unread_leads_overdue > 4 ? 'bg-danger' : ''}}" style="background-color: #995d62">
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
                @if (Auth::guard('team')->user()->role_id == 4)
                <div class="col-lg-3 col-6">
                    <a target="_blank" href="{{route('team.lead.list', 'rm_unfollowed_leads')}}" class="text-light">
                        <div class="small-box text-sm bg-success">
                            <div class="inner">
                                <h3>{{$rm_unfollowed_leads}}</h3>
                                <p>Unfollowed Leads</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>
                            <div class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 col-6">
                    <a target="_blank" href="{{route('team.lead.list', 'not_contacted_lead')}}" class="text-light">
                        <div class="small-box text-sm bg-warning">
                            <div class="inner">
                                <h3>{{$not_contacted_lead}}</h3>
                                <p>Not Contacted Leads</p>
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
                    <a target="_blank" href="{{route('team.task.list', 'task_schedule_this_month')}}" class="text-light">
                        <div class="small-box text-sm bg-success">
                            <div class="inner">
                                <h3>{{$rm_month_task_leads}}</h3>
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
                    <a target="_blank" href="{{route('team.task.list', 'task_schedule_today')}}" class="text-light">
                        <div class="small-box text-sm" style="background-color: cadetblue;">
                            <div class="inner">
                                <h3>{{$rm_today_task_leads}}</h3>
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
                    <a target="_blank" href="{{route('team.task.list', 'total_task_overdue')}}" class="text-light">
                        <div class="small-box text-sm bg-secondary">
                            <div class="inner">
                                <h3>{{$rm_task_overdue_leads}}</h3>
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
                    <h3 class="mt-3">Leads Forward</h3>
                </div>
                <div class="col-lg-3 col-6">
                    <a target="_blank" href="{{route('team.lead.list', 'forward_leads_this_month')}}" class="text-light">
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
                    <a target="_blank" href="{{route('team.lead.list', 'forward_leads_today')}}" class="text-light">
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
                @endif
                @if (Auth::guard('team')->user()->role_id == 5)
                    <div class="col-lg-3 col-6">
                        <a target="_blank" href="{{route('team.lead.list', 'unfollowed_leads')}}" class="text-light">
                            <div class="small-box text-sm bg-success">
                                <div class="inner">
                                    <h3>{{$unfollowed_leads}}</h3>
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
                        <a target="_blank" href="{{route('team.task.list', 'task_schedule_this_month')}}" class="text-light">
                            <div class="small-box text-sm bg-success">
                                <div class="inner">
                                    <h3>{{$task_schedule_this_month}}</h3>
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
                        <a target="_blank" href="{{route('team.task.list', 'task_schedule_today')}}" class="text-light">
                            <div class="small-box text-sm" style="background-color: cadetblue;">
                                <div class="inner">
                                    <h3>{{$task_schedule_today}}</h3>
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
                        <a target="_blank" href="{{route('team.task.list', 'total_task_overdue')}}" class="text-light">
                            <div class="small-box text-sm bg-secondary">
                                <div class="inner">
                                    <h3>{{$total_task_overdue}}</h3>
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
                        <h3>Visits</h3>
                    </div>
                    <div class="col-lg-3 col-6">
                        <a target="_blank" href="{{route('team.visit.list', 'recce_schedule_this_month')}}" class="text-light">
                            <div class="small-box text-sm" style="background-color:cadetblue;">
                                <div class="inner">
                                    <h3>{{$recce_schedule_this_month}}</h3>
                                    <p>Recce Schedule this Month</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-bag"></i>
                                </div>
                                <div class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-6">
                        <a target="_blank" href="{{route('team.visit.list', 'recce_schedule_today')}}" class="text-light">
                            <div class="small-box text-sm bg-success">
                                <div class="inner">
                                    <h3>{{$recce_schedule_today}}</h3>
                                    <p>Recce Schedule Today</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-bag"></i>
                                </div>
                                <div class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-6">
                        <a target="_blank" href="{{route('team.visit.list', 'total_recce_overdue')}}" class="text-light">
                            <div class="small-box text-sm bg-secondary">
                                <div class="inner">
                                    <h3>{{$total_recce_overdue}}</h3>
                                    <p>Total Recce Overdue</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-bag"></i>
                                </div>
                                <div class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-6">
                        <a target="_blank" href="{{route('team.visit.list', 'recce_done_this_month')}}" class="text-light">
                            <div class="small-box text-sm" style="background-color: cadetblue">
                                <div class="inner">
                                    <h3>{{$recce_done_this_month}}</h3>
                                    <p style="font-size: 14px;">Recce Done This Month / L2R - {{$l2r}} %</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-bag"></i>
                                </div>
                                <div class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-12 mt-3">
                        <h3>Bookings</h3>
                    </div>
                    <div class="col-lg-3 col-6">
                        <a target="_blank" href="{{route('team.bookings.list', 'bookings_this_month')}}" class="text-light">
                            <div class="small-box text-sm" style="background-color: cadetblue">
                                <div class="inner">
                                    <h3>{{$bookings_this_month}}</h3>
                                    <p style="font-size: 14px;">Bookings This Month / R2C - {{$r2c}} %</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-bag"></i>
                                </div>
                                <div class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></div>
                            </div>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </section>
</div>
@endsection
@section('footer-script')
<script>
    toastr.options = {
        "closeButton": true,
        "timeOut": 0,
        "extendedTimeOut": 0,
        "tapToDismiss": false
    }

    const total_task_overdue = Number("{{$total_task_overdue}}");
    const total_recce_overdue = Number("{{$total_recce_overdue}}");

    setTimeout(() => {
        total_task_overdue > 0 ? toastr.info(`Task Overdue: ${total_task_overdue}`) : '';
        total_recce_overdue > 0 ? toastr.info(`Recce Overdue: ${total_recce_overdue}`) : '';
    }, 2000);

</script>
@endsection
