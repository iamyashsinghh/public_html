@extends('nonvenue.layouts.app')
@section('title', 'Dashboard | NVRM')
@section('main')
<style>
    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: scale(1.05);
        box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
    }

    .card-footer {
        font-size: 0.9rem;
        font-weight: bold;
    }

    .card-body h4 {
        font-size: 1.5rem;
    }
</style>
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
                    <a target="_blank" href="{{ route('nonvenue.lead.list', 'leads_received_this_month') }}"
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
                    <a target="_blank" href="{{ route('nonvenue.lead.list', 'leads_received_today') }}"
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
                    <a target="_blank" href="{{ route('nonvenue.lead.list', 'unread_leads_this_month') }}"
                        class="text-light">
                        <div class="small-box text-sm" style="background: #995d62;">
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
                    <a target="_blank" href="{{ route('nonvenue.lead.list', 'unread_leads_today') }}"
                        class="text-light">
                        <div class="small-box text-sm" style="background: #995d62;">
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
                    <a target="_blank" href="{{ route('nonvenue.lead.list', 'total_unread_leads_overdue') }}"
                        class="text-light">

                        <div class="small-box text-sm" style="background: #995d62;">
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
                    <a target="_blank" href="{{ route('nonvenue.lead.list', 'nvrm_unfollowed_leads') }}"
                        class="text-light">
                        <div class="small-box text-sm bg-success">
                            <div class="inner">
                                <h3>{{ $nvrm_unfollowed_leads }}</h3>
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
                    <a target="_blank" href="{{ route('nonvenue.task.list', 'task_schedule_this_month') }}"
                        class="text-light">
                        <div class="small-box text-sm bg-success">
                            <div class="inner">
                                <h3>{{ $nvrm_month_task_leads }}</h3>
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
                    <a target="_blank" href="{{ route('nonvenue.task.list', 'task_schedule_today') }}"
                        class="text-light">
                        <div class="small-box text-sm" style="background-color: cadetblue;">
                            <div class="inner">
                                <h3>{{ $nvrm_today_task_leads }}</h3>
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
                    <a target="_blank" href="{{ route('nonvenue.task.list', 'total_task_overdue') }}"
                        class="text-light">
                        <div class="small-box text-sm bg-secondary">
                            <div class="inner">
                                <h3>{{ $nvrm_task_overdue_leads }}</h3>
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
                    <a target="_blank" href="{{ route('nonvenue.vendor.help.list', 'vendor_overdue_issue') }}"
                        class="text-light">
                        <div class="small-box text-sm bg-warning">
                            <div class="inner">
                                <h3>{{ $vendor_overdue_issue }}</h3>
                                <p>Vendors Overdue Issue</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>
                            <div class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 col-6">
                    <a target="_blank" href="{{ route('nonvenue.vendor.help.list', 'vendor_today_issue') }}"
                        class="text-light">
                        <div class="small-box text-sm" style="background-color: cadetblue;">
                            <div class="inner">
                                <h3>{{ $vendor_today_issue }}</h3>
                                <p>Vendors Issue Today</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>
                            <div class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-12 my-3">
                    <h3>Lead Forward</h3>
                </div>
                <div class="col-lg-3 col-6">
                    <a target="_blank" href="{{ route('nonvenue.lead.list', 'forward_leads_this_month') }}"
                        class="text-light">
                        <div class="small-box text-sm" style="background: cadetblue;">
                            <div class="inner">
                                <h3>{{ $forward_leads_this_month }}</h3>
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
                    <a target="_blank" href="{{ route('nonvenue.lead.list', 'forward_leads_today') }}"
                        class="text-light">
                        <div class="small-box text-sm bg-success">
                            <div class="inner">
                                <h3>{{ $forward_leads_today }}</h3>
                                <p>Forward Leads Today</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>
                            <div class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-12 my-4">
                    <h3 class="mb-4">Lead Forward By Category</h3>
                    <div class="row gy-4">
                        @foreach ($forward_leads_by_category as $category => $lead_counts)
                        {{-- @if ($lead_counts['month'] > 0 || $lead_counts['today'] > 0) --}}
                        <div class="col-lg-3 col-md-6 col-sm-12">
                            <div class="card shadow-sm border-0" style="background: cadetblue !important;"> 
                                <div class="card-body bg-light rounded" >
                                    <h5 class="card-title text-center text-dark fw-bold mb-3">{{ $category }}</h5>
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <a target="_blank" href="{{ route('nonvenue.lead.list', ['category' => $category, 'filter' => 'month']) }}" class="text-decoration-none">
                                                <h4 class="text-primary fw-bold">{{ $lead_counts['month'] }}</h4>
                                                <p class="text-muted mb-0">Month</p>
                                            </a>
                                        </div>
                                        <div class="col-6">
                                            <a target="_blank" href="{{ route('nonvenue.lead.list', ['category' => $category, 'filter' => 'today']) }}" class="text-decoration-none">
                                                <h4 class="text-secondary fw-bold">{{ $lead_counts['today'] }}</h4>
                                                <p class="text-muted mb-0">Today</p>
                                            </a>
                                        </div>
                                    </div>
                                    <hr class="my-3">
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <a target="_blank" href="{{ route('nonvenue.lead.list', ['category' => $category, 'filter' => 'fresh_requirement']) }}" class="text-decoration-none">
                                                <h4 class="text-success fw-bold">{{ $lead_counts['fresh_requirement'] }}</h4>
                                                <p class="text-muted mb-0">Fresh Requirement</p>
                                            </a>
                                        </div>
                                        <div class="col-6">
                                            <a target="_blank" href="{{ route('nonvenue.lead.list', ['category' => $category, 'filter' => 'not_fresh_requirement']) }}" class="text-decoration-none">
                                                <h4 class="text-danger fw-bold">{{ $lead_counts['not_fresh_requirement'] }}</h4>
                                                <p class="text-muted mb-0">Other Requirement</p>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- @endif --}}
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
