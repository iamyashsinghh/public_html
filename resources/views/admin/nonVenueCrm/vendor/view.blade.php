@extends('admin.layouts.app')
@section('title', 'Vendor Profile | Non Venue CRM')
@section('main')
<div class="content-wrapper pb-5 text-sm">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Vendor Profile</h1>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3">
                    <div class="card card-primary card-outline">
                        <div class="card-body box-profile">
                            <div class="text-center">
                                <img style="height: 100px; cursor: pointer;" class="profile-user-img img-fluid img-circle" src="{{asset($vendor->profile_image)}}" onerror="this.onerror=null; this.src='{{asset('images/default-user.png')}}'" alt="User profile picture" {{$vendor->profile_image ? "onclick=handle_view_image(`$vendor->profile_image`)" : ''}}>
                            </div>
                            <h3 class="profile-username text-center">{{$vendor->name}}</h3>
                            <p class="text-muted text-center">{{$vendor->get_category->name}}</p>
                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b>Mobile</b> <a href="tel:{{$vendor->mobile}}" class="float-right">{{$vendor->mobile}}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Email</b> <a class="float-right" href="mail:{{$vendor->email}}">{{$vendor->email}}</a>
                                </li>
                            </ul>
                            <a target="_blank" href="{{route('admin.vendor.bypass.login', $vendor->id)}}" onclick="return confirm('Login confirmation..')" class="btn btn-sm btn-block text-light" style="background-color: var(--wb-renosand);">
                                <b>Login</b>
                                <i class="fa fa-sign-in-alt mx-1"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-9">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="mb-0 float-left">Profile Info</h4>
                        </div>
                        <div class="card-body">
                            <div class="container-fluid">
                                <ul class="list-group">
                                    <li class="list-group-item border-0 ps-0 pt-0 text-sm">
                                        <span>Vendor Name: &nbsp;</span>
                                        <strong class="text-dark">{{$vendor->name}}</strong>
                                    </li>
                                    <li class="list-group-item border-0 ps-0 pt-0 text-sm">
                                        <span>Manager: &nbsp;</span>
                                        <strong class="text-dark">{{$vendor->get_manager ? $vendor->get_manager->name : 'N/A'}}</strong>
                                    </li>
                                    <li class="list-group-item border-0 ps-0 pt-0 text-sm" style="column-gap: 2rem">
                                        <span>Created At: &nbsp;</span>
                                        <strong class="text-dark">{{date('d-m-Y h:i a', strtotime($vendor->created_at))}}</strong>
                                    </li>
                                    <li class="list-group-item border-0 ps-0 pt-0 text-sm d-flex align-items-center" style="column-gap: 2rem">
                                        <span>Status: &nbsp;</span>
                                        <a href="{{route('admin.vendor.update.status', [$vendor->id, $vendor->status == 1 ? 0 : 1])}}" style="font-size: 22px;"><i class="fa {{$vendor->status == 1 ? 'fa-toggle-on text-success' : 'fa-toggle-off text-danger'}}"></i></a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h4 class="mb-0 float-left">Registered Devices</h4>
                            <a class="float-right text-dark" title="{{$vendor->can_add_device == 1 ? 'Remove permision to add device': 'Give permision to add device'}}" href="{{route('admin.registered.device.permission_manage', [ 'vendor', $vendor->id, $vendor->can_add_device == 1 ? 0 : 1])}}" style="font-size: 22px;"><i class="fa {{$vendor->can_add_device == 1 ? 'fa-toggle-on text-success' : 'fa-toggle-off text-danger'}}"></i></a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                     <thead>
                                         <tr>
                                             <th>S.No.</th>
                                             <th>Devices Name</th>
                                             <th class="text-nowrap">Created At</th>
                                             <th class="text-center">Action</th>
                                         </tr>
                                     </thead>
                                     <tbody>
                                        @if(sizeof($vendor->get_registered_devices) > 0)
                                            @foreach ($vendor->get_registered_devices as $key => $list)
                                            <tr>
                                                <td>{{$key+1}}</td>
                                                <td>{{$list->device_name}}</td>
                                                <td class="text-nowrap">{{date('d-m-Y h:i a', strtotime($list->created_at))}}</td>
                                                <td class="text-center">
                                                    <a href="{{route('admin.registered.device.delete', $list->id)}}" onclick="return confirm('Are you sure want to delete.')" class="text-danger mx-2"><i class="fa fa-trash-alt"></i></a>
                                                </td>
                                            </tr>
                                            @endforeach
                                         @else
                                            <tr>
                                                <td class="text-center" colspan="4">No data available in table</td>
                                            </tr>
                                     @endif
                                     </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
@section('footer-script')
@endsection
