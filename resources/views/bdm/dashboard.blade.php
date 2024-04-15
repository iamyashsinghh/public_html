@extends('bdm.layouts.app')
@section('title', 'Dashboard | BDM')
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
                                <h3>1</h3>
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
                                <h3>2</h3>
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
                        <div class="small-box text-sm {{8 > 4 ? 'bg-danger' : ''}}" style="background-color: #995d62">
                            <div class="inner">
                                <h3>3</h3>
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
                        <div class="small-box text-sm {{7 > 4 ? 'bg-danger' : ''}}" style="background-color: #995d62">
                            <div class="inner">
                                <h3>4</h3>
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
                    <a target="_blank" href="4" class="text-light">
                        <div class="small-box text-sm {{6 > 4 ? 'bg-danger' : ''}}" style="background-color: #995d62">
                            <div class="inner">
                                <h3>87</h3>
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
                    <a target="_blank" href="{{route('bdm.lead.list', 'bdm_unfollowed_leads')}}" class="text-light">
                        <div class="small-box text-sm bg-success">
                            <div class="inner">
                                <h3>{{$bdm_unfollowed_leads}}</h3>
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
                                <h3>87</h3>
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
                                <h3>09</h3>
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
                                <h3>867</h3>
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
                                <h3>45</h3>
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
                                <h3>43</h3>
                                <p>Forward Leads Today</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>
                            <div class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></div>
                        </div>
                    </a>
                </div>

                    <div class="col-lg-3 col-6">
                        <a target="_blank" href="{{route('team.lead.list', 'unfollowed_leads')}}" class="text-light">
                            <div class="small-box text-sm bg-success">
                                <div class="inner">
                                    <h3>34</h3>
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
                                    <h3>43</h3>
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
                                    <h3>434</h3>
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
                                    <h3>4</h3>
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
                                    <h3>8</h3>
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
                                    <h3>34</h3>
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
                                    <h3>4</h3>
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
                                    <h3>4</h3>
                                    <p style="font-size: 14px;">Recce Done This Month / L2R - 4 %</p>
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
                                    <h3>2</h3>
                                    <p style="font-size: 14px;">Bookings This Month / R2C -5 %</p>
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
<script>
    toastr.options = {
        "closeButton": true,
        "timeOut": 0,
        "extendedTimeOut": 0,
        "tapToDismiss": false
    }

    const total_task_overdue = Number("5");
    const total_recce_overdue = Number("50");

    setTimeout(() => {
        total_task_overdue > 0 ? toastr.info(`Task Overdue: ${total_task_overdue}`) : '';
        total_recce_overdue > 0 ? toastr.info(`Recce Overdue: ${total_recce_overdue}`) : '';
    }, 2000);

</script>

@endsection
