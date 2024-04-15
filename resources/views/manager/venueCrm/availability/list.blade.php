@extends('manager.layouts.app')
@section('title', "Availability | Manager")
@section('main')
<div class="content-wrapper pb-5">
    <section class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">Availability</h1>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box text-sm text-light" style="background: var(--wb-renosand);">
                        <div class="inner">
                            <h3>{{$grand_total_event}}</h3>
                            <p>Grand Total of Events</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box text-sm text-light" style="background: var(--wb-dark-red);">
                        <div class="inner">
                            <h3>{{$grand_total_pax}}</h3>
                            <p>Grand Total of PAX</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="my-2">
                @foreach ($vm_members as $vm)
                <div class="card text-xs mb-5">
                    <div class="card-header card-header-mod text-light" style="background: linear-gradient(48deg, #8e0000e6, #dfa930b5);">
                        <h6 class="mb-0 text-bold">{{$vm->venue_name}}</h5>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-bordered">
                            <thead class="text-sm">
                                <tr class="text-center">
                                    <th>{{$vm->name}}</th>
                                    @foreach ($vm->availability as $key => $list)
                                        <th>{{$key}}</th>
                                    @endforeach
                                    <th>Total Event/Pax</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm">
                                <tr style="font-weight: bold;" class="text-center">
                                    <td>Events</td>
                                    @foreach ($vm->availability as $key => $list)
                                        <td>{{$list['events']}}</td>
                                    @endforeach
                                    <td style="color: var(--wb-renosand);" class="text-lg">{{$vm->total_event}}</td>

                                </tr>
                                <tr style="font-weight: bold;" class="text-center">
                                    <td>PAX</td>
                                    @foreach ($vm->availability as $key => $list)
                                        <td>{{$list['pax']}}</td>
                                    @endforeach
                                    <td style="color: var(--wb-dark-red);" class="text-lg">{{$vm->total_pax}}</td>
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
@endsection