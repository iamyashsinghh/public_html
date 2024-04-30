@extends('vendor.layouts.app')
@section('main')
@section("title", "Dashboard | Vendor")
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
                <div class="col-lg-12 mt-3">
                    <h3>Leads</h3>
                </div>
                <div class="col-lg-3 col-6">
                    <a target="_blank" href="{{route('vendor.lead.list')}}" class="text-light">
                        <div class="small-box text-sm" style="background: var(--wb-renosand);">
                            <div class="inner">
                                <h3>{{$total_leads}}</h3>
                                <p>Total Leads</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>
                            <div class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 col-6">
                    <a target="_blank" href="{{route('vendor.lead.list', 'leads_of_the_month')}}" class="text-light">
                        <div class="small-box text-sm" style="background: var(--wb-dark-red);">
                            <div class="inner">
                                <h3>{{$leads_of_the_month}}</h3>
                                <p>Leads of the Month</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>
                            <div class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 col-6">
                    <a target="_blank" href="{{route('vendor.lead.list', 'leads_of_the_day')}}" class="text-light">
                        <div class="small-box text-sm" style="background: var(--wb-renosand);">
                            <div class="inner">
                                <h3>{{$leads_of_the_day}}</h3>
                                <p>Leads of the Day</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>
                            <div class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 col-6">
                    <a target="_blank" href="{{route('vendor.lead.list', 'unreaded_leads')}}" class="text-light">
                        <div class="small-box text-sm" style="background: var(--wb-dark-red);">
                            <div class="inner">
                                <h3>{{$unreaded_leads}}</h3>
                                <p>Unreaded Leads</p>
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
                    <a target="_blank" href="{{route('vendor.task.list')}}" class="text-light">
                        <div class="small-box text-sm" style="background: var(--wb-dark-red);">
                            <div class="inner">
                                <h3>{{$schedule_tasks}}</h3>
                                <p>Schedule Tasks</p>
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
                    <a target="_blank" href="{{route('vendor.meeting.list')}}" class="text-light">
                        <div class="small-box text-sm" style="background: var(--wb-renosand);">
                            <div class="inner">
                                <h3>{{$schedule_meetings}}</h3>
                                <p>Schedule Meetings</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>
                            <div class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-12 mt-3">
                    <h3>Booked</h3>
                </div>
                <div class="col-lg-3 col-6">
                    <a target="_blank" href="{{route('vendor.lead.list')}}" class="text-light">
                        <div class="small-box text-sm" style="background: var(--wb-dark-red);">
                            <div class="inner">
                                <h3>{{$leads_booked}}</h3>
                                <p>Booked Leads</p>
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
@endsection
