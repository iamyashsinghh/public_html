@extends('admin.layouts.app')
@section('header-css')
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
@endsection
@section('title', "Team Login Info | Venue CRM")
@section('main')
<div class="content-wrapper pb-5">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Team Login Info</h1>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="table-responsive" style="overflow-x: visible">
                <table id="clientTable" class="table text-sm">
                    <thead class="sticky_head bg-light" style="position: sticky; top: 0;">
                        <tr>
                            <th class="text-nowrap">Team ID</th>
                            <th class="text-nowrap">User Name</th>
                            <th class="text-nowrap">Request OTP Count/Daily</th>
                            <th class="text-nowrap">Last OTP Request At</th>
                            <th class="text-nowrap">Login Count/Daily</th>
                            <th class="text-nowrap">Last Login At</th>
                            <th class="text-nowrap">IP Address</th>
                            <th class="text-nowrap">Browser</th>
                            <th class="text-center">Platform</th>
                            <th class="text-nowrap">Last Logout At</th>
                            <th class="text-nowrap">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($login_infos as $list)
                        @php
                            if($list->role_id == 2){
                                $bg_color = 'bg_light_blue';
                            }elseif($list->role_id == 3 || $list->role_id == 4){
                                $bg_color = 'bg_light_dark';
                            }else{
                                $bg_color = '';
                            }
                        @endphp
                        <tr class="{{$bg_color}}">
                            <td>{{$list->user_id}}</td>
                            <td>{{$list->team_name}} ({{$list->role_name}})</td>
                            <td class="text-center">{{$list->request_otp_count}}</td>
                            <td>{{date('d-M-Y h:i a', strtotime($list->request_otp_at))}}</td>
                            <td class="text-center">{{$list->login_count}}</td>
                            <td>{{date('d-M-Y h:i a', strtotime($list->login_at))}}</td>
                            <td>{{$list->ip_address}}</td>
                            <td>{{$list->browser}}</td>
                            <td>{{$list->platform}}</td>
                            <td>{{$list->logout_at ? date('d-M-Y h:i a', strtotime($list->logout_at)) : 'N/A'}}</td>
                            <td>
                                @if ($list->status == 0)
                                    <span class="badge badge-secondary">Offline</span>
                                @else
                                    <span class="badge badge-success">Online</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
@endsection
@section('footer-script')
<script src="//cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script>
     $("#clientTable").DataTable({
        pageLength: 100,
        ordering: false,
        language: {
            "search": "_INPUT_", // Removes the 'Search' field label
            "searchPlaceholder": "Type here to search..", // Placeholder for the search box
            processing: `<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>`, // loader
        },
    });
</script>
@endsection