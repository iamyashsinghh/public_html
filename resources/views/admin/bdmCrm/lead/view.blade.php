@extends('admin.layouts.app')
@php
    $page_title = $lead->name ?: 'N/A';
    $page_title .= " | $lead->mobile | View Lead | Bdm CRM";
    $current_date = date('Y-m-d');
    $auth_user = Auth::guard('admin')->user();
@endphp
@section('title', $page_title)
@section('header-css')
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
@endsection
@section('main')
    @php
        $auth_user = Auth::guard('admin')->user();
        $active_task_count = 0;
        $active_meeting_count = 0;
        $active_meeting_count = 0;
    @endphp
    <div class="content-wrapper pb-5">
        <section class="content-header">
            <div class="container-fluid">
                <h1>View Lead</h1>
            </div>
        </section>
        <section class="content">
            <div id="view_lead_card_container" class="card text-sm">
                <div class="card-header">
                    <div class="dropdown d-inline-block">
                        <a href="javascript:void(0);"
                            class="btn dropdown-toggle text-light btn-xs px-2 mx-1 {{ $lead->lead_status == 'Done' ? 'bg-secondary' : '' }}"
                            data-bs-toggle="dropdown" style="background-color: var(--wb-renosand);"><i
                                class="fa fa-chart-line"></i> Lead:
                            {{ $lead->lead_status != 'Done' ? 'Active' : 'Done' }}</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item {{ $lead->lead_status != 'Done' ? 'disabled' : '' }}"
                                    onclick="return confirm('Are you sure want to active this lead?')"
                                    href="{{ route('admin.bdm.lead.status.update', $lead->lead_id) }}/Active">Active</a></li>
                            <li><a class="dropdown-item" href="javascript:void(0);"
                                    onclick="handle_lead_status(this)">Done</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="container-fluid">
                        <div class="card mb-5">
                            <div class="card-header text-light" style="background-color: var(--wb-renosand);">
                                <h3 class="card-title">Lead Information</h3>
                                <button href="javascript:void(0);" class="btn p-0 text-light float-right"
                                    title="Edit lead info." data-bs-toggle="modal" data-bs-target="#editLeadModal"><i
                                        class="fa fa-edit"></i></button>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <span class="text-bold mx-1" style="color: var(--wb-wood)">Lead Date: </span>
                                        <span
                                            class="mx-1">{{ date('d-M-Y h:i a', strtotime($lead->lead_datetime)) }}</span>
                                    </div>
                                    <div class="col-sm-6">
                                        <span class="text-bold mx-1" style="color: var(--wb-wood)">Lead ID: </span>
                                        <span class="mx-1">{{ $lead->lead_id }}</span>
                                    </div>
                                    <div class="col-sm-6">
                                        <span class="text-bold mx-1" style="color: var(--wb-wood)">Name: </span>
                                        <span class="mx-1">{{ $lead->name ?: 'N/A' }}</span>
                                    </div>
                                    <div class="col-sm-6">
                                        <span class="text-bold mx-1" style="color: var(--wb-wood)">Mobile No.: </span>
                                        <span class="mx-1">{{ $lead->mobile }}</span>
                                        <div class="phone_action_btns" style="position: absolute; top: -8px; left: 11rem;">
                                            <a target="_blank" href="https://wa.me/{{ $lead->mobile }}"
                                                class="text-success text-bold mx-1" style="font-size: 20px;"><i
                                                    class="fab fa-whatsapp"></i></a>
                                            <a href="tel:{{ $lead->mobile }}" class="text-primary text-bold mx-1"
                                                style="font-size: 20px;"><i class="fa fa-phone-alt"></i></a>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <span class="text-bold mx-1" style="color: var(--wb-wood)">Email: </span>
                                        <span class="mx-1">{{ $lead->email ?: 'N/A' }}</span>
                                    </div>
                                    <div class="col-sm-6">
                                        <span class="text-bold mx-1" style="color: var(--wb-wood)">Alternate Mobile No.:
                                        </span>
                                        <span class="mx-1">{{ $lead->alternate_mobile ?: 'N/A' }}</span>
                                    </div>
                                    <div class="col-sm-6">
                                        <span class="text-bold mx-1" style="color: var(--wb-wood)">Business Name: </span>
                                        <span class="mx-1">{{ $lead->business_name ?: 'N/A' }}</span>
                                    </div>
                                    <div class="col-sm-6">
                                        <span class="text-bold mx-1" style="color: var(--wb-wood)">Business Category:
                                        </span>
                                        <span class="mx-1">{{ $lead->get_lead_cat->name ?: 'N/A' }}</span>
                                    </div>
                                    <div class="col-sm-6">
                                        <span class="text-bold mx-1" style="color: var(--wb-wood)">Lead Status: </span>
                                        <span
                                            class="mx-1 badge badge-{{ $lead->lead_status == 'Done' ? 'secondary' : 'success' }}">{{ $lead->lead_status }}</span>
                                    </div>
                                    <div class="col-sm-6">
                                        <span class="text-bold mx-1" style="color: var(--wb-wood)">Done Title: </span>
                                        <span class="mx-1">{{ $lead->done_title ?: 'N/A' }}</span>
                                    </div>
                                    <div class="col-sm-6">
                                        <span class="text-bold mx-1" style="color: var(--wb-wood)">Service Status: </span>
                                        @if ($lead->service_status == 1)
                                            <span class="mx-1 badge badge-success">Contacted</span>
                                        @else
                                            <span class="mx-1 badge badge-danger">Not Contacted</span>
                                        @endif
                                    </div>
                                    <div class="col-sm-6">
                                        <span class="text-bold mx-1" style="color: var(--wb-wood)">Done Message: </span>
                                        <span class="mx-1">{{ $lead->done_message ?: 'N/A' }}</span>
                                    </div>
                                    <div class="col-sm-6">
                                        <span class="text-bold mx-1" style="color: var(--wb-wood)">Lead Source: </span>
                                        <span class="mx-1">{{ $lead->source ?: 'N/A' }}</span>
                                    </div>
                                    <div class="col-sm-6">
                                        <span class="text-bold mx-1" style="color: var(--wb-wood)">Done Datetime: </span>
                                        <span
                                            class="mx-1">{{ $lead->done_title ? date('d-M-Y H:i a', strtotime($lead->updated_at)) : 'N/A' }}</span>
                                    </div>
                                    <div class="col-sm-6">
                                        <span class="text-bold mx-1" style="color: var(--wb-wood)">Lead Created or Done
                                            By: </span>
                                        <span class="mx-1">
                                            @php
                                                if ($lead->get_created_by) {
                                                    echo $lead->get_created_by->name .
                                                        ' (' .
                                                        $lead->get_created_by->get_role->name .
                                                        ')';
                                                } else {
                                                    echo 'API Reference';
                                                }
                                            @endphp
                                        </span>
                                    </div>
                                    <div class="col-sm-6">
                                        <span class="text-bold mx-1" style="color: var(--wb-wood)">City :</span>
                                        <span>
                                            {{ $lead->city }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="task_card_container" class="card mb-5">
                            <div class="card-header text-light" style="background-color: var(--wb-renosand);">
                                <h3 class="card-title">Task Details</h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="serverTable" class="table mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-nowrap">S.No.</th>
                                                <th class="text-nowrap">Task Schedule Date</th>
                                                <th class="text-nowrap">Follow Up</th>
                                                <th class="">Message</th>
                                                <th class="text-nowrap">Status</th>
                                                <th class="text-nowrap">Done With</th>
                                                <th class="text-nowrap">Done Message</th>
                                                <th class="text-nowrap">Done Date</th>
                                                <th class="text-nowrap">Created By</th>
                                            </tr>
                                        </thead>

                                        <body>
                                            @php
                                                $tasks = $lead->get_tasks;
                                            @endphp
                                            @if (sizeof($tasks) > 0)
                                                @foreach ($tasks as $key => $list)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td class="">
                                                            {{ date('d-M-Y h:i a', strtotime($list->task_schedule_datetime)) }}
                                                        </td>
                                                        <td>{{ $list->follow_up }}</td>
                                                        <td>
                                                            <button class="btn"
                                                                onclick="handle_view_message(`{{ $list->message ?: 'N/A' }}`)"><i
                                                                    class="fa fa-comment-dots"
                                                                    style="color: var(--wb-renosand);"></i></button>
                                                        </td>
                                                        <td>
                                                            @php
                                                                $schedule_date = date(
                                                                    'Y-m-d',
                                                                    strtotime($list->task_schedule_datetime),
                                                                );
                                                                if ($list->done_datetime !== null) {
                                                                    $elem_class = 'success';
                                                                    $elem_text = 'Updated';
                                                                } elseif ($schedule_date > $current_date) {
                                                                    $elem_class = 'info';
                                                                    $elem_text = 'Upcoming';
                                                                } elseif ($schedule_date == $current_date) {
                                                                    $elem_class = 'warning';
                                                                    $elem_text = 'Today';
                                                                } elseif ($schedule_date < $current_date) {
                                                                    $elem_class = 'danger';
                                                                    $elem_text = 'Overdue';
                                                                }
                                                            @endphp
                                                            @if ($list->done_datetime !== null)
                                                                <span
                                                                    class="badge badge-{{ $elem_class }}">{{ $elem_text }}</span>
                                                            @else
                                                                <button
                                                                    class="btn btn-{{ $elem_class }} btn-xs"
                                                                    data-bs-toggle="dropdown"
                                                                    style="font-size: 75% !important;">{{ $elem_text }}</button>

                                                                @php
                                                                    $active_task_count++;
                                                                @endphp
                                                            @endif
                                                        </td>
                                                        <td>{{ $list->done_with ?: 'N/A' }}</td>
                                                        <td>
                                                            <button class="btn"
                                                                onclick="handle_view_message(`{{ $list->done_message ?: 'N/A' }}`)"><i
                                                                    class="fa fa-comment-dots"
                                                                    style="color: var(--wb-renosand);"></i></button>
                                                        </td>
                                                        <td class="">
                                                            {{ $list->done_datetime ? date('d-M-Y h:i a', strtotime($list->done_datetime)) : 'N/A' }}
                                                        </td>
                                                        <td>{{ $list->get_created_by->name }}</td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td class="text-center text-muted" colspan="9">No data available in
                                                        table</td>
                                                </tr>
                                            @endif
                                        </body>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div id="meeting_card_container" class="card mb-5">
                            <div class="card-header text-light" style="background-color: var(--wb-renosand);">
                                <h3 class="card-title">Meeting Details</h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="serverTable" class="table mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-nowrap">S.No.</th>
                                                <th class="text-nowrap">Meeting Schedule Date</th>
                                                <th class="text-nowrap">Follow Up</th>
                                                <th class="">Message</th>
                                                <th class="text-nowrap">Status</th>
                                                <th class="text-nowrap">Done Date</th>
                                                <th class="text-nowrap">Done Status</th>
                                                <th class="text-nowrap">Done With</th>
                                                <th class="text-nowrap">Done Message</th>
                                                <th class="text-nowrap">Created By</th>
                                            </tr>
                                        </thead>

                                        <body>
                                            @php
                                                $meeting = $lead->get_meetings;
                                            @endphp
                                            @if (sizeof($meeting) > 0)
                                                @foreach ($meeting as $key => $list)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td class="">
                                                            {{ date('d-M-Y h:i a', strtotime($list->meeting_schedule_datetime)) }}
                                                        </td>
                                                        <td>{{ $list->follow_up }}</td>
                                                        <td>
                                                            <button class="btn"
                                                                onclick="handle_view_message(`{{ $list->message ?: 'N/A' }}`)"><i
                                                                    class="fa fa-comment-dots"
                                                                    style="color: var(--wb-renosand);"></i></button>
                                                        </td>
                                                        <td>
                                                            @php
                                                                $schedule_date = date(
                                                                    'Y-m-d',
                                                                    strtotime($list->meeting_schedule_datetime),
                                                                );
                                                                if ($list->done_datetime !== null) {
                                                                    $elem_class = 'success';
                                                                    $elem_text = 'Updated';
                                                                } elseif ($schedule_date > $current_date) {
                                                                    $elem_class = 'info';
                                                                    $elem_text = 'Upcoming';
                                                                } elseif ($schedule_date == $current_date) {
                                                                    $elem_class = 'warning';
                                                                    $elem_text = 'Today';
                                                                } elseif ($schedule_date < $current_date) {
                                                                    $elem_class = 'danger';
                                                                    $elem_text = 'Overdue';
                                                                }
                                                            @endphp
                                                            @if ($list->done_datetime !== null)
                                                                <span
                                                                    class="badge badge-{{ $elem_class }}">{{ $elem_text }}</span>
                                                            @else
                                                                <button
                                                                    class="btn btn-{{ $elem_class }} btn-xs"
                                                                    data-bs-toggle="dropdown"
                                                                    style="font-size: 75% !important;">{{ $elem_text }}</button>
                                                                @php
                                                                    $active_meeting_count++;
                                                                @endphp
                                                            @endif
                                                        </td>
                                                        <td class="">
                                                            {{ $list->done_datetime ? date('d-M-Y h:i a', strtotime($list->done_datetime)) : 'N/A' }}
                                                        </td>
                                                        <td class="">{{ $list->meeting_done_status ?: 'N/A' }}</td>
                                                        <td>{{ $list->done_with ?: 'N/A' }}</td>
                                                        <td>
                                                            <button class="btn"
                                                                onclick="handle_view_message(`{{ $list->done_message ?: 'N/A' }}`)"><i
                                                                    class="fa fa-comment-dots"
                                                                    style="color: var(--wb-renosand);"></i></button>
                                                        </td>
                                                        <td>{{ $list->get_created_by->name }}</td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td class="text-center text-muted" colspan="9">No data available in
                                                        table</td>
                                                </tr>
                                            @endif
                                        </body>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div id="booking_card_container" class="card mb-5">
                            <div class="card-header text-light" style="background-color: var(--wb-renosand);">
                                <h3 class="card-title">Order Sign Details</h3>
                                <button data-bs-toggle="modal" data-bs-target="#manageBookingModal"
                                    class="btn p-0 text-light float-right" title="Add Task."><i
                                        class="fa fa-plus"></i></button>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="serverTable" class="table mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-nowrap">S.No.</th>
                                                <th class="text-nowrap">Order Sign Date</th>
                                                <th class="text-nowrap">Package Name</th>
                                                <th class="">Amount</th>
                                                <th class="text-nowrap">Payment Method</th>
                                                <th class="">Payment Image</th>
                                                <th class="text-nowrap">Order & Agreement Farm Image</th>
                                                <th class="text-nowrap">Action</th>
                                                <th>Created by</th>
                                            </tr>
                                        </thead>

                                        <body>
                                            @php
                                                $booking = $lead->get_lead_booking;
                                            @endphp
                                            @if (sizeof($booking) > 0)
                                                @foreach ($booking as $key => $list)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td class="">
                                                            {{ date('d-M-Y h:i a', strtotime($list->booking_date)) }}</td>
                                                        <td>{{ $list->package_name }}</td>
                                                        <td>{{ $list->price }}</td>
                                                        <td>{{ $list->payment_method }}</td>
                                                        <td style="cursor: pointer"
                                                            onclick="handle_set_payment_image(`{{ $list->payment_proof }}`, {{ $list->id }})">
                                                            <img src='{{ $list->payment_proof }}' class="elevation-2"
                                                                style="width: 50px; height: 50px;">
                                                        </td>
                                                        <td>
                                                            <div class="row">
                                                                @if ($list->order_agreement_farm_image != null)
                                                                    @foreach (explode(',', $list->order_agreement_farm_image) as $key => $item)
                                                                        <a target="_blank"
                                                                            href="{{ asset("storage/uploads/bdmAgreementImg/$item") }}"
                                                                            class="col-sm-4 my-1">
                                                                            <img src="{{ asset("storage/uploads/bdmAgreementImg/$item") }}"
                                                                                style="width: 50px; height: 50px;">
                                                                        </a>
                                                                    @endforeach
                                                                @endif
                                                            </div>
                                                        </td>
                                                        <td class="text-nowrap">
                                                            <div class="d-flex">
                                                                <i class="fa fa-edit text-success"
                                                                    onclick="edit_booking({{ $list->id }})"
                                                                    style="cursor: pointer"></i>
                                                                <a class="mx-3"
                                                                    href="{{ route('admin.bdm.booking.delete') }}/{{ $list->id }}">
                                                                    <i class="fa fa-trash text-danger"
                                                                        style="cursor: pointer"></i>
                                                                </a>
                                                                <div class="dropdown d-inline-block mx-2">
                                                                    <a href="javascript:void(0);"
                                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                                        <i class="fa fa-caret-down text-dark"></i>
                                                                    </a>
                                                                    <ul class="dropdown-menu">
                                                                        <li><a class="dropdown-item" target="_blank"
                                                                                href="{{ route('admin.bdm.aggrement.manage_images') }}/{{ $list->id }}">Agreement
                                                                                Image</a></li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>{{ $list->get_created_by->name }}</td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td class="text-center text-muted" colspan="9">No data available in
                                                        table</td>
                                                </tr>
                                            @endif
                                        </body>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-5">
                            <div class="card-header text-light" style="background-color: var(--wb-renosand);">
                                <h3 class="card-title">Notes</h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="serverTable" class="table mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-nowrap">S.No.</th>
                                                <th class="">Message</th>
                                                <th class="">Created At</th>
                                                <th class="">Created By</th>
                                            </tr>
                                        </thead>

                                        <body>
                                            @php
                                                $notes = $lead->get_notes();

                                            @endphp
                                            @if (sizeof($notes) > 0)
                                                @foreach ($notes as $key => $list)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>
                                                            <button class="btn"
                                                                onclick="handle_view_message(`{{ $list->message ?: 'N/A' }}`)"><i
                                                                    class="fa fa-comment-dots"
                                                                    style="color: var(--wb-renosand);"></i></button>
                                                        </td>
                                                        <td class="text-nowrap">
                                                            {{ date('d-M-Y h:i a', strtotime($list->created_at)) }}</td>
                                                        <td>{{ $list->get_created_by }}</td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td class="text-center text-muted" colspan="5">No data available in
                                                        table</td>
                                                </tr>
                                            @endif
                                        </body>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="modal fade" id="manageLeadStatusModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Lead Done</h4>
                        <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i
                                class="fa fa-times"></i></button>
                    </div>
                    <form action="{{ route('admin.bdm.lead.status.update', $lead->lead_id) }}" method="post">
                        <div class="modal-body text-sm">
                            @csrf
                            <div class="form-group">
                                <input type="hidden" name="lead_di" value="{{ $lead->lead_id }}">
                                <label for="done_title_select">Done Title <span class="text-danger">*</span></label>
                                <select class="form-control" id="done_title_select" name="done_title" required>
                                    <option value="" selected disabled>Select title</option>
                                    <option value="Problem With WB | NV T&C">Problem With WB | NV T&C</option>
                                    <option value="Problem with Commercial / Take Rate">Problem with Commercial / Take Rate
                                    </option>
                                    <option value="Doing Good Business on Their Own">Doing Good Business on Their Own
                                    </option>
                                    <option value="Working with Competitor">Working with Competitor</option>
                                    <option value="Vendor Below WB | NV Standards">Vendor Below WB | NV Standards</option>
                                    <option value="Others: Others">Others: Others</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="done_message_textarea">Done Message</label>
                                <textarea type="text" class="form-control" id="done_message_textarea" placeholder="Type message"
                                    name="done_message"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-sm btn-secondary"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-sm text-light"
                                style="background-color: var(--wb-dark-red);">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="editLeadModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Edit Lead</h4>
                        <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i
                                class="fa fa-times"></i></button>
                    </div>
                    <form id="manage_lead_form" action="{{ route('admin.bdm.lead.edit.process', $lead->lead_id) }}"
                        method="post">
                        <div class="modal-body text-sm">
                            @csrf
                            <div class="row">
                                <div class="col-sm-4 mb-3">
                                    <div class="form-group">
                                        <label for="name_inp">Name</label>
                                        <input type="text" class="form-control" id="name_inp"
                                            placeholder="Enter name" name="name" value="{{ $lead->name }}">
                                    </div>
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <div class="form-group">
                                        <label for="email_inp">Email</label>
                                        <input type="email" class="form-control" id="email_inp"
                                            placeholder="Enter email" name="email" value="{{ $lead->email }}">
                                    </div>
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <div class="form-group">
                                        <label for="mobile_inp">Mobile No. <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="mobile_inp"
                                            placeholder="Enter mobile no." name="mobile_number"
                                            value="{{ $lead->mobile }}" disabled
                                            title="Primary phone number cannot be edit.">
                                    </div>
                                </div>
                                <div class="col-sm-4 mb-">
                                    <div class="form-group">
                                        <label for="alt_mobile_inp">Alternate Mobile No.</label>
                                        <input type="text" class="form-control" id="alt_mobile_inp"
                                            placeholder="Enter alternate mobile no." name="alternate_mobile_number"
                                            value="{{ $lead->alternate_mobile }}">
                                    </div>
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <div class="form-group">
                                        <label for="city_inp">City</label>
                                        <input type="text" class="form-control" id="city_inp"
                                            placeholder="Enter City." name="city" value="{{ $lead->city }}">
                                    </div>
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <div class="form-group">
                                        <label for="lead_status_select">Lead Status</label>
                                        <select class="form-control" id="lead_status_select" name="lead_status" required>
                                            <option value="Active" {{ $lead->lead_status == 'Active' ? 'selected' : '' }}>
                                                Active</option>
                                            <option value="Hot" {{ $lead->lead_status == 'Hot' ? 'selected' : '' }}>Hot
                                            </option>
                                            <option value="Super Hot"
                                                {{ $lead->lead_status == 'Super Hot' ? 'selected' : '' }}>Super Hot
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <div class="form-group">
                                        <label for="business_cat_inp">Business Category</label>
                                        <select class="form-control" id="business_cat_inp" name="business_cat">
                                            <option value="" disabled selected>Select Category</option>
                                            @foreach ($vendor_categories as $list)
                                                <option value="{{ $list->id }}"
                                                    {{ $lead->get_lead_cat->id == $list->id ? 'selected' : '' }}>
                                                    {{ $list->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <div class="form-group">
                                        <label for="business_name_inp">Business Name</label>
                                        <input type="text" class="form-control" id="business_name_inp"
                                            placeholder="Enter Business Name." name="business_name"
                                            value="{{ $lead->business_name }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer text-sm">
                            <button type="button" class="btn btn-sm bg-secondary"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-sm text-light"
                                style="background-color: var(--wb-dark-red);">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="manageBookingModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="bookingmodaltitle">Sign New Order</h4>
                        <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i
                                class="fa fa-times"></i></button>
                    </div>
                    <form id="booking_status_update_form" action="{{route('admin.bdm.booking.add')}}" method="post">
                        <div class="modal-body text-sm">
                            <div class="form-group mb-3">
                                @csrf
                                <label for="booking_done_date">Order Sign Date<span class="text-danger">*</span></label>
                                <input type="datetime-local" id="booking_done_date" class="form-control"
                                    name="booking_date">
                                <input type="hidden" value="{{ $lead->lead_id }}" name="lead_id">
                            </div>
                            <div class="form-group mb-3">
                                <label for="booking_package_with_select">Select Package<span
                                        class="text-danger">*</span></label>
                                <select class="form-control" id="booking_package_with_select" name="package_name"
                                    required>
                                    <option value="" selected disabled>Select Package</option>
                                    <option value="Premium Listing">Premium Listing</option>
                                    <option value="Gold Package">Gold Package</option>
                                    <option value="Elite Package">Elite Package</option>
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label for="booking_done_price">Order Sign Price<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="price" id="booking_done_price">
                            </div>
                            <div class="form-group mb-3">
                                <label for="booking_package_payment_method_with_select">Payment Method<span
                                        class="text-danger">*</span></label>
                                <select class="form-control" id="booking_package_payment_method_with_select"
                                    name="payment_method" required>
                                    <option value="" selected disabled>Select Package</option>
                                    <option value="Card">Card</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Cheque">Cheque</option>
                                    <option value="QR-Code">QR-Code</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer text-sm">
                            <div class="col">
                                <p>
                                    <span class="text-danger">*</span>
                                    Fields are required.
                                </p>
                            </div>
                            <button type="button" class="btn btn-sm bg-secondary"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-sm text-light"
                                style="background-color: var(--wb-dark-red);">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('footer-script')
    <script src="{{ asset('plugins/select2/js/select2.min.js') }}"></script>
    <script>

        function handle_lead_status(elem) {
            const modal = new bootstrap.Modal("#manageLeadStatusModal");
            modal.show();
        }

        function handle_set_payment_image(image_url, id) {
            const existingModal = document.getElementById('viewImageModal');
            if (existingModal) {
                existingModal.remove();
            }
            var image_change_request_url = "{{ route('admin.bdm.update.bookingPayment.img') }}";

            const div = document.createElement('div');
            div.classList = "modal fade";
            div.id = "viewImageModal";
            div.setAttribute("tabindex", "-1");
            const modal_elem = `
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Image</h4>
                    <button type="button" class="btn text-secondary" onclick="handle_remove_modal('viewImageModal')" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                </div>
                <div class="modal-body text-center">
                    <img src="${image_url}" class="rounded img-fluid" style="min-width: 20rem; height: 20rem;" />
                </div>
                <div class="modal-footer justify-content-between align-items-end">
                    <form action="${image_change_request_url}" method="post" class="w-50" enctype="multipart/form-data">
                        <div class="form-group">
                            <label>Update Image?</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="customFile" name="image" required>
                                <label class="custom-file-label" for="customFile">Choose file</label>
                            </div>
                            <input type="text" id="customTemp_name" class="d-none" name="id" value="${id}" required>
                        </div>
                        <button type="submit" class="btn btn-sm m-1 text-light" style="background-color: var(--wb-dark-red);">Submit</button>
                    </form>
                    <button type="button" class="btn btn-sm btn-secondary" onclick="handle_remove_modal('viewImageModal')" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    `;
            div.innerHTML = modal_elem;
            document.body.appendChild(div);
            const modal = new bootstrap.Modal(div);
            modal.show();
            const fileInput = document.querySelector('#customFile');
            const label = document.querySelector('label[for="customFile"]');
            fileInput.addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    label.textContent = file.name;
                    const img = document.querySelector('.modal-body img');
                    img.src = URL.createObjectURL(file);
                }
            });
        }

        function edit_booking(booking_id) {
            const apiUrl = `{{ route('admin.bdm.booking.get') }}/${booking_id}`;
            var bookingData;
            fetch(apiUrl, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    const manageBookingModal = new bootstrap.Modal(document.getElementById('manageBookingModal'));
                    bookingmodaltitle.innerHTML = 'Update Order Sign';
                    booking_status_update_form.action = `{{ route('admin.bdm.booking.edit') }}/${data.id}`;
                    booking_done_date.value = data.booking_date;
                    booking_package_with_select.querySelector(`option[value="${data.package_name}"]`).selected = true;
                    booking_package_payment_method_with_select.querySelector(`option[value="${data.payment_method}"]`)
                        .selected = true;
                    booking_done_price.value = data.price;
                    manageBookingModal.show();
                })
                .catch(error => {
                    console.error('There was a problem with the fetch operation:', error);
                });


        }
    </script>
@endsection
