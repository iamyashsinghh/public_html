@extends('manager.layouts.app')
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
                    <a target="_blank" href="{{route('manager.team.list')}}" class="text-light">
                        <div class="small-box text-sm" style="background: var(--wb-renosand);">
                            <div class="inner">
                                <h3>{{$vm_members->count()}}</h3>
                                <p>Total VM Members</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-stats-bars"></i>
                            </div>
                            <div class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 col-6">
                    <a target="_blank" href="{{route('manager.lead.list')}}" class="text-light">
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
                <h2>VM Statics</h2>
                @foreach ($vm_members as $vm)
                <div class="card text-xs mb-5">
                    <div class="card-header card-header-mod text-light" style="background: linear-gradient(48deg, #8e0000e6, #dfa930b5);">
                        <h6 class="mb-0 text-bold">{{$vm->venue_name}}</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table-bordered">
                                <thead>
                                    <tr class="text-center">
                                        <th>VM Name</th>
                                        <th>Leads Recieved this Month</th>
                                        <th>Leads Recieved Today</th>
                                        <th>Unread Leads this Month</th>
                                        <th>Unread Leads Today</th>
                                        <th>Unread Leads Overdue</th>
                                        <th>Schedule Task this Month</th>
                                        <th>Schedule Task Today</th>
                                        <th>Task Overdue</th>
                                        <th>Recce Schedule this Month</th>
                                        <th>Recce Schedule Today</th>
                                        <th>Recce Overdue</th>
                                        <th>Recce (Visits Done) - L2R {{$vm->l2r}}%</th>
                                        <th>Bookings this Month - R2C {{$vm->r2c}}%</th>
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
                </div>
                @endforeach
            </div>
        </div>
    </section>
</div>
@endsection