@extends('admin.layouts.app')
@section('header-css')
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
@endsection
@section('title', $page_heading . ' | Venue CRM')
@section('main')
    <div class="content-wrapper pb-5">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">{{ $page_heading }}</h1>
                    </div>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="container-fluid">
                <div class="table-responsive">
                    <table id="clientTable" class="table text-sm">
                        <thead>
                            <tr>
                                <th class="">ID</th>
                                <th class="">Lead Id</th>
                                <th class="">Foward From</th>
                                <th class="">Foward To</th>
                                <th class="">Foward By</th>
                                <th class="">Reason</th>
                                <th class="text-nowrap">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($LeadForwardApproval as $list)
                                <tr>
                                    <td>{{ $list->id }}</td>
                                    <td>{{ $list->lead_id }}</td>
                                    <td>{{ \App\Models\LeadForwardApproval::getTeamMemberName($list->forward_from) }}</td>
                                    <td>{{ \App\Models\LeadForwardApproval::getTeamMemberName($list->forward_to) }}</td>
                                    <td>{{ \App\Models\LeadForwardApproval::getTeamMemberName($list->forward_by) }} <br /> {{ $list->created_at }}</td>
                                    <td>
                                        <button class="btn"
                                            onclick="handle_view_message(`{{ $list->reason ?: 'N/A' }}`)"><i
                                                class="fa fa-comment-dots" style="color: var(--wb-renosand);"></i></button>
                                    </td>
                                    <td>
                                        <div class="button-group d-flex my-4">
                                            <a href="{{route('admin.leadforwardapproval.status_update')}}/{{$list->id}}/0" class="btn text-light btn-sm buttons-print mx-1"
                                                style="background-color: var(--wb-dark-red)">Reject</a>
                                            <a href="{{route('admin.leadforwardapproval.status_update')}}/{{$list->id}}/1" class="btn text-light btn-sm buttons-print mx-1"
                                                style="background-color: var(--wb-renosand)">Approve</a>
                                        </div>
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
        initialize_datatable();
    </script>
@endsection
