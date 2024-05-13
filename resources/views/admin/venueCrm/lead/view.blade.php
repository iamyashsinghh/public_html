@extends('admin.layouts.app')
@php
    $page_title = $lead->name ?: 'N/A';
    $page_title .= " | $lead->mobile | View Lead | Venue CRM";
@endphp
@section('title', $page_title)
@section('main')
    <div class="content-wrapper pb-5">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>View Lead</h1>
                    </div>
                </div>
                {{-- onclick="nvrm_forword_preloader(this)"  --}}
                <div class="button-group my-4">
                    <a href="javascript:void(0);" class="btn text-light btn-sm buttons-print mx-1" data-bs-toggle="modal"
                        data-bs-target="#forwardLeadModal" style="background-color: var(--wb-dark-red)"><i
                            class="fa fa-paper-plane"></i> Forward to RM's</a>
                    <button onclick="handle_get_forward_info({{ $lead->lead_id }})" class="btn btn-sm mx-1 btn-info"
                        title="Forward info">Forward Info: {{ $lead->get_lead_forwards->count() }}</button>
                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#forwardnvrmLeadModal" class="btn text-light btn-sm buttons-print mx-1"
                        style="background-color: var(--wb-dark-red)"><i class="fa fa-paper-plane"></i>Forward to NvRM</a>
                </div>
            </div>
        </section>
        @php
            $current_date = date('Y-m-d');
        @endphp
        <section class="content">
            <div class="card text-sm">
                <div class="card-body">
                    <div class="container-fluid">
                        <div class="card mb-5">
                            <div class="card-header text-light" style="background-color: var(--wb-renosand);">
                                <h3 class="card-title">RM Message's</h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="serverTable" class="table mb-0" style="background-color: #fdfd7b5c">
                                        <thead>
                                            <tr>
                                                <th class="text-nowrap">S.No.</th>
                                                <th class="text-nowrap">Created At</th>
                                                <th class="">RM Name</th>
                                                <th class="text-nowrap">Title</th>
                                                <th class="">Message</th>
                                            </tr>
                                        </thead>

                                        <body>
                                            @if (sizeof($lead->get_rm_messages) > 0)
                                                @foreach ($lead->get_rm_messages as $key => $list)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>{{ date('d-M-Y h:i a', strtotime($list->created_at)) }}</td>
                                                        <td>{{ $list->get_created_by->name ?? '' }}</td>
                                                        <td>{{ $list->title }}</td>
                                                        <td>
                                                            <button class="btn"
                                                                onclick="handle_view_message(`{{ $list->message ?: 'N/A' }}`)"><i
                                                                    class="fa fa-comment-dots"
                                                                    style="color: var(--wb-renosand);"></i></button>
                                                        </td>
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
                        <div class="card mb-5">
                            <div class="card-header text-light" style="background-color: var(--wb-renosand);">
                                <h3 class="card-title">Lead Information</h3>
                                <a href="javascript:void(0);" class="text-light float-right" title="Edit"
                                    data-bs-toggle="modal" data-bs-target="#editLeadModal"><i class="fa fa-edit"
                                        style="font-size: 15px;"></i></a>
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
                                        <span class="text-bold mx-1" style="color: var(--wb-wood)">Prefered Locality:
                                        </span>
                                        <span class="mx-1">{{ $lead->locality ?: 'N/A' }}</span>
                                    </div>
                                    <div class="col-sm-6">
                                        <span class="text-bold mx-1" style="color: var(--wb-wood)">Lead Status: </span>
                                        <span class="mx-1 badge badge-success">{{ $lead->lead_status }}</span>
                                    </div>
                                    <div class="col-sm-6">
                                        <span class="text-bold mx-1" style="color: var(--wb-wood)">Done Datetime: </span>
                                        {{ $lead->lead_status == 'Done' ? date('d-M-Y h:i a', strtotime($lead->updated_at)) : 'N/A' }}
                                    </div>

                                    <div class="col-sm-6">
                                        <span class="text-bold mx-1" style="color: var(--wb-wood)">Lead Source: </span>
                                        <span class="mx-1">{{ $lead->source ?: 'N/A' }}</span>
                                    </div>
                                    <div class="col-sm-6">
                                        <span class="text-bold mx-1" style="color: var(--wb-wood)">Done Title: </span>
                                        {{ $lead->lead_status == 'Done' ? $lead->done_title : 'N/A' }}
                                    </div>
                                    <div class="col-sm-6">
                                        <span class="text-bold mx-1" style="color: var(--wb-wood)">Lead Created or Done
                                            By: </span>
                                        <span
                                            class="mx-1">{{ $lead->get_created_by ? $lead->get_created_by->name : 'N/A' }}
                                            -
                                            {{ $lead->get_created_by ? $lead->get_created_by->get_role->name : '' }}</span>
                                    </div>
                                    <div class="col-sm-6">
                                        <span class="text-bold mx-1" style="color: var(--wb-wood)">Done Message: </span>
                                        {{ $lead->lead_status == 'Done' ? $lead->done_message : 'N/A' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-5">
                            <div class="card-header text-light" style="background-color: var(--wb-renosand);">
                                <h3 class="card-title">Event Information</h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="serverTable" class="table mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-nowrap">S.No.</th>
                                                <th class="text-nowrap">Event Date</th>
                                                <th class="">Event Name</th>
                                                <th class="text-nowrap">Pax</th>
                                                <th class="">Budget (in INR)</th>
                                                <th class="">Slot</th>
                                                <th class="">Food Preference</th>
                                                <th class="">Created By</th>
                                                <th class="">Created At</th>
                                            </tr>
                                        </thead>

                                        <body>
                                            @if (sizeof($lead->get_primary_events()) > 0)
                                                @foreach ($lead->get_primary_events() as $key => $list)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td class="text-nowrap">
                                                            {{ date('d-M-Y', strtotime($list->event_datetime)) }}</td>
                                                        <td>{{ $list->event_name }}</td>
                                                        <td>{{ $list->pax }}</td>
                                                        <td class="text-center">₹ {{ number_format($list->budget) }}</td>
                                                        <td>{{ $list->event_slot }}</td>
                                                        <td>{{ $list->food_preference }}</td>
                                                        <td>{{ $list->get_created_by->name ?? '' }} -
                                                            {{ $list->get_created_by->get_role->name ?? '' }}</td>
                                                        <td>{{ date('d-M-Y h:i a', strtotime($list->created_at)) }}</td>
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
                                                <th class="">Status</th>
                                                <th class="">Done With</th>
                                                <th class="">Done Message</th>
                                                <th class="text-nowrap">Done Date</th>
                                                <th class="text-nowrap">Created By</th>
                                                <th class="text-nowrap">Created At</th>
                                            </tr>
                                        </thead>

                                        <body>
                                            @if (sizeof($lead->get_tasks) > 0)
                                                @foreach ($lead->get_tasks as $key => $list)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td class="text-nowrap">
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
                                                            <span
                                                                class="badge badge-{{ $elem_class }}">{{ $elem_text }}</span>
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
                                                        <td class="">{{ $list->get_created_by->name ?? '' }} -
                                                            {{ $list->get_created_by->get_role->name ?? '' }}</td>
                                                        <td class="">
                                                            {{ date('d-m-Y h:i a', strtotime($list->created_at)) }}</td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td class="text-center text-muted" colspan="10">No data available in
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
                                <h3 class="card-title">Visit Details</h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="serverTable" class="table mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-nowrap">S.No.</th>
                                                <th class="text-nowrap">Visit Schedule Date</th>
                                                <th class="">Message</th>
                                                <th class="text-nowrap">Status</th>
                                                <th class="">Event Name</th>
                                                <th class="">Created By</th>
                                                <th class="">Created At</th>
                                            </tr>
                                        </thead>

                                        <body>
                                            @if (sizeof($lead->get_visits) > 0)
                                                @foreach ($lead->get_visits as $key => $list)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td class="text-nowrap">
                                                            {{ date('d-M-Y h:i a', strtotime($list->visit_schedule_datetime)) }}
                                                        </td>
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
                                                                    strtotime($list->visit_schedule_datetime),
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
                                                            <span
                                                                class="badge badge-{{ $elem_class }}">{{ $elem_text }}</span>
                                                        </td>
                                                        <td>{{ $list->event_name }}</td>
                                                        <td>{{ $list->get_created_by->name ?? '' }} -
                                                            {{ $list->get_created_by->get_role->name ?? '' }}</td>
                                                        <td>{{ date('d-m-Y h:i a', strtotime($list->created_at)) }}</td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td class="text-center text-muted" colspan="7">No data available in
                                                        table</td>
                                                </tr>
                                            @endif
                                        </body>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div id="bookings_card_container" class="card mb-5">
                            <div class="card-header text-light" style="background-color: var(--wb-renosand);">
                                <h3 class="card-title">Bookings</h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="serverTable" class="table mb-0">
                                        <thead>
                                            <tr>
                                                <th>Booking Date</th>
                                                <th>Booking Source</th>
                                                <th>Venue Name</th>
                                                <th>VM Name</th>
                                                <th>Event Name</th>
                                                <th>Event Date</th>
                                                <th>Slot</th>
                                                <th>Food Preference</th>
                                                <th>Menu Selected</th>
                                                <th>Party Area</th>
                                                <th>PAX</th>
                                                <th>Price per plate</th>
                                                <th>Total Amount (GMV)</th>
                                                <th>Advance Amount</th>
                                                <th>25% Advance Completed</th>
                                                <th class="">Action</th>
                                            </tr>
                                        </thead>

                                        <body>
                                            @if (sizeof($lead->get_bookings) > 0)
                                                @foreach ($lead->get_bookings as $key => $booking)
                                                    <tr>
                                                        <td class="text-nowrap">
                                                            {{ date('d-M-Y h:i a', strtotime($booking->created_at)) }}</td>
                                                        <td class="text-nowrap">{{ $booking->booking_source }}</td>
                                                        <td class="text-nowrap">{{ $booking->get_vm->venue_name }}</td>
                                                        <td class="text-nowrap">{{ $booking->get_vm->name }}</td>
                                                        <td class="text-nowrap">{{ $booking->get_event->event_name }}</td>
                                                        <td class="text-nowrap">
                                                            {{ date('d-M-Y h:i a', strtotime($booking->get_event->event_datetime)) }}
                                                        </td>
                                                        <td class="text-nowrap">{{ $booking->get_event->event_slot }}</td>
                                                        <td class="text-nowrap">{{ $booking->get_event->food_preference }}
                                                        </td>
                                                        <td class="text-nowrap">{{ $booking->menu_selected }}</td>
                                                        <td class="text-nowrap">{{ $booking->party_area }}</td>
                                                        <td class="text-nowrap">
                                                            {{ number_format($booking->get_event->pax) }}</td>
                                                        <td class="text-nowrap">
                                                            {{ number_format($booking->price_per_plate, 2) }}</td>
                                                        <td class="text-nowrap">
                                                            {{ number_format($booking->total_gmv, 2) }}</td>
                                                        <td class="text-nowrap">
                                                            {{ number_format($booking->advance_amount, 2) }}</td>
                                                        <td class="text-nowrap">
                                                            @if ($booking->quarter_advance_collected == 0)
                                                                <span class="badge badge-danger">Not Collected</span>
                                                            @else
                                                                <span class="badge badge-success">Received</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-nowrap">
                                                            <a href="javascript:void(0);"
                                                                onclick="fetch_booking(`{{ route('booking.manage_process', $booking->id) }}`, `{{ route('booking.fetch', $booking->id) }}`)"
                                                                class="text-success mx-2" title="Edit"><i
                                                                    class="fa fa-edit" style="font-size: 15px;"></i></a>
                                                            <a href="{{ route('booking.delete', $booking->id) }}"
                                                                onclick="return confirm('Are your sure want to delete this booking?')"
                                                                class="text-danger mx-2" title="Delete"><i
                                                                    class="fa fa-trash" style="font-size: 15px;"></i></a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td class="text-center text-muted" colspan="14">No data available in
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
                                <h3 class="card-title">Lead Done Details</h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-nowrap">S.No.</th>
                                                <th class="">Done By</th>
                                                <th class="text-nowrap">Done Datetime</th>
                                                <th class="text-nowrap">Done Title</th>
                                                <th class="text-nowrap">Done Message</th>
                                            </tr>
                                        </thead>

                                        <body>
                                            @if (sizeof($done_leads) > 0)
                                                @foreach ($done_leads as $key => $list)
                                                    @if ($list->get_forward_to->role_id == 5)
                                                        <tr>
                                                            <td>{{ $key + 1 }}</td>
                                                            <td>{{ $list->get_forward_to->name }} -
                                                                {{ $list->get_forward_to->venue_name }}</td>
                                                            <td>{{ date('d-M-Y h:i a', strtotime($list->updated_at)) }}
                                                            </td>
                                                            <td>
                                                                <button class="btn"
                                                                    onclick="handle_view_message(`{{ $list->done_title ?: 'N/A' }}`)"><i
                                                                        class="fa fa-comment-dots"
                                                                        style="color: var(--wb-renosand);"></i></button>
                                                            </td>
                                                            <td>
                                                                <button class="btn"
                                                                    onclick="handle_view_message(`{{ $list->done_message ?: 'N/A' }}`)"><i
                                                                        class="fa fa-comment-dots"
                                                                        style="color: var(--wb-renosand);"></i></button>
                                                            </td>
                                                        </tr>
                                                    @endif
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
                        <div class="card mb-5">
                            <div class="card-header text-light" style="background-color: var(--wb-renosand);">
                                <h3 class="card-title">Notes</h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-nowrap">S.No.</th>
                                                <th class="">Message</th>
                                                <th class="text-nowrap">Created By</th>
                                                <th class="text-nowrap">Created At</th>
                                            </tr>
                                        </thead>

                                        <body>
                                            @if (sizeof($lead->get_notes) > 0)
                                                @foreach ($lead->get_notes as $key => $list)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>
                                                            <button class="btn"
                                                                onclick="handle_view_message(`{{ $list->message ?: 'N/A' }}`)"><i
                                                                    class="fa fa-comment-dots"
                                                                    style="color: var(--wb-renosand);"></i></button>
                                                        </td>
                                                        <td class="text-nowrap">{{ $list->get_created_by->name ?? '' }} -
                                                            {{ $list->get_created_by->get_role->name ?? '' }}</td>
                                                        <td class="text-nowrap">
                                                            {{ date('d-M-Y h:i a', strtotime($list->created_at)) }}</td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td class="text-center text-muted" colspan="4">No data available in
                                                        table</td>
                                                </tr>
                                            @endif
                                        </body>
                                    </table>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="card mb-5">
                            <div class="card-header text-light" style="background-color: var(--wb-renosand);">
                                <h3 class="card-title">Call Recordings</h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="serverTable" class="table mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-nowrap">S.No.</th>
                                                <th class="">Recording</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                            $recording_urls = !empty($lead->recording_url) ? explode(',', $lead->recording_url) : [];
                                            @endphp
                                            @if(count($recording_urls) > 0)
                                            @foreach ($recording_urls as $key => $url)
                                            <tr>
                                                <td>{{$key+1}}</td>
                                                <td>
                                                    <audio controls>
                                                        <source src="{{$url}}" type="audio/mpeg">
                                                        Your browser does not support the audio element.
                                                    </audio>
                                                </td>
                                            </tr>
                                            @endforeach
                                            @else
                                            <tr>
                                                <td class="text-center text-muted" colspan="2">No data available in table</td>
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div> --}}
                        <div class="card mb-5">
                            <div class="card-header text-light" style="background-color: var(--wb-renosand);">
                                <h3 class="card-title">Call Recordings</h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="serverTable" class="table mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-nowrap">S.No.</th>
                                                <th class="">Recording</th>
                                                <th class="">Metadata</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                            $recording_urls_with_metadata = [];
                                            if (!empty($lead->recording_url)) {
                                                $recording_data = json_decode($lead->recording_url, true) ?? [];
                                                foreach ($recording_data as $index => $data) {
                                                    // Extract URL and metadata
                                                    $url = $data['url'];
                                                    $metadata = json_decode($data['metadata'], true);
                                                    // Append URL, datetime, and caller agent to the array
                                                    $recording_urls_with_metadata[] = [
                                                        'url' => $url,
                                                        'datetime' => $metadata['datetime'],
                                                        'caller_agent' => $metadata['caller_agent']
                                                    ];
                                                }
                                            }
                                            @endphp
                                            @if(count($recording_urls_with_metadata) > 0)
                                            @foreach ($recording_urls_with_metadata as $key => $data)
                                            <tr>
                                                <td>{{$key+1}}</td>
                                                <td>
                                                    <audio controls>
                                                        <source src="{{$data['url']}}" type="audio/mpeg">
                                                        Your browser does not support the audio element.
                                                    </audio>
                                                </td>
                                                <td>
                                                    Datetime: {{date('d-M-Y h:i a', strtotime($data['datetime']))}}<br>
                                                    Caller Agent: {{$data['caller_agent']}}
                                                </td>
                                            </tr>
                                            @endforeach
                                            @else
                                            <tr>
                                                <td class="text-center text-muted" colspan="3">No data available in table</td>
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
        <div class="modal fade" id="leadForwardedMemberInfo" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header text-sm">
                        <h4 class="modal-title">Forward Information</h4>
                        <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i
                                class="fa fa-times"></i></button>
                    </div>
                    <div class="modal-body">
                        <p id="last_forwarded_info_paragraph" class="text-sm mb-2"></p>
                        <div class="table-responsive">
                            <table id="clientTable" class="table text-sm">
                                <thead>
                                    <tr>
                                        <th class="text-nowrap">S.No.</th>
                                        <th class="text-nowrap">Name</th>
                                        <th class="text-nowrap">Venue Name</th>
                                        <th class="text-nowrap">Read Status</th>
                                    </tr>
                                </thead>
                                <tbody id="forward_info_table_body">
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                    </div>
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
                    <form action="{{ route('admin.lead.edit.process', $lead->lead_id) }}" method="post">
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
                                        <label for="locality_inp">Preferred Locality</label>
                                        <input type="text" class="form-control" id="locality_inp"
                                            placeholder="Enter preferred locality." name="locality"
                                            value="{{ $lead->locality }}">
                                    </div>
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <div class="form-group">
                                        <label for="lead_source_select">Lead Source</label>
                                        <select class="form-control" id="lead_source_select" name="lead_source" required>
                                            <option value="WB|Team" selected>WB|Team</option>
                                            <option value="VM|Reference"
                                                {{ $lead->source == 'VM|Reference' ? 'selected' : '' }}>VM|Reference
                                            </option>
                                            <option value="WB|Call" {{ $lead->source == 'WB|Call' ? 'selected' : '' }}>
                                                WB|Call</option>
                                            <option value="Walk-in" {{ $lead->source == 'Walk-in' ? 'selected' : '' }}>
                                                Walk-in</option>
                                            <option value="Other" {{ $lead->source == 'Other' ? 'selected' : '' }}>Other
                                            </option>
                                        </select>
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
        <div class="modal fade" id="forwardnvrmLeadModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header align-items-center">
                        <h4 class="modal-title">Forward Lead's to NVRM's</h4>
                        <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i
                                class="fa fa-times"></i></button>
                    </div>
                    <div class="row px-3">
                        @foreach ($nvrm_members as $rm)
                            <div class="custom-control custom-radio my-1 mx-2">
                                <input class="custom-control-input" type="radio" name="forward_rms_id"
                                    id="team_member_{{ $rm->id }}" value="{{ $rm->id }}">
                                <label for="team_member_{{ $rm->id }}"
                                    class="custom-control-label">{{ $rm->name }}</label>
                            </div>
                        @endforeach
                    </div>
                    <div class="modal-footer">
                        <a href="{{ route('admin.lead.list') }}" class="btn btn-sm bg-secondary m-1"
                            data-bs-dismiss="modal">Cancel</a>
                        <button type="submit" onclick="nvrm_forword_preloader(this)" class="btn btn-sm text-light m-1"
                            style="background-color: var(--wb-dark-red);">Forward</button>
                    </div>
                </div>
            </div>
        </div>
        @include('admin.venueCrm.lead.forward_leads_modal')
        @include('includes.manage_booking_modal')
    </div>
    <script>
        var postUrl = "{{ route('admin.lead.forwardnvrm') }}";

        var csrfToken = "{{ csrf_token() }}";

        function nvrm_forword_preloader(elem) {
            const loaderHtml = `<i class="fa fa-spinner fa-spin"></i>`;
            const originalText = elem.innerHTML;
            elem.innerHTML = loaderHtml;
            elem.disabled = true;

            sendPostRequest()
                .then(response => {
                    if (response.success) {
                        toastr.success(response.message);
                    } else {
                        toastr.error(response.message);
                    }
                    elem.innerHTML = originalText;
                    elem.disabled = false;
                })
                .catch(error => {
                    console.error('Request failed', error);
                    toastr.error('An error occurred. Please try again later.');
                    elem.innerHTML = originalText;
                    elem.disabled = false;
                });
        }

        function sendPostRequest() {
            const postData = {
                lead_id: `{{ $lead->lead_id }}`,
                forward_rms_id : document.querySelector('input[name="forward_rms_id"]:checked').value,
                _token: csrfToken
            };
            return fetch(postUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(postData)
                })
                .then(response => response.json());
        }
    </script>
@endsection
