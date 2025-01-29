@extends('manager.layouts.app')
@php
$page_title = $lead->name ?: 'N/A';
$page_title .= " | $lead->mobile | View Lead | Venue CRM";
@endphp
@section('title', $page_title)
@section('main')
@php
$current_date = date('Y-m-d');
@endphp
<div class="content-wrapper pb-5">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Manager View Lead</h1>
                </div>
            </div>
            <div class="button-group my-4">
                <a href="javascript:void(0);" class="btn text-light btn-sm buttons-print mx-1" data-bs-toggle="modal"
                    data-bs-target="#forwardLeadModal" style="background-color: var(--wb-dark-red)"><i
                        class="fa fa-paper-plane"></i> Forward to VM's</a>
                <button onclick="handle_get_forward_info({{$lead->lead_id}})" class="btn btn-sm mx-1 btn-info"
                    title="Forward info">Forward Info: {{$leads_forward->count()}}</button>
            </div>
        </div>
    </section>
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
                                            <td>{{$key+1}}</td>
                                            <td>{{date('d-M-Y h:i a', strtotime($list->created_at))}}</td>
                                            <td>{{$list->get_created_by->name ?? ''}}</td>
                                            <td>{{$list->title}}</td>
                                            <td>
                                                <button class="btn"
                                                    onclick="handle_view_message(`{{$list->message ?: 'N/A'}}`)"><i
                                                        class="fa fa-comment-dots"
                                                        style="color: var(--wb-renosand);"></i></button>
                                            </td>
                                        </tr>
                                        @endforeach
                                        @else
                                        <tr>
                                            <td class="text-center text-muted" colspan="5">No data available in table
                                            </td>
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
                            {{-- <a href="javascript:void(0);" class="text-light float-right" title="Edit"><i
                                    class="fa fa-edit" style="font-size: 15px;"></i></a> --}}
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <span class="text-bold mx-1" style="color: var(--wb-wood)">Lead Date: </span>
                                    <span class="mx-1">{{date('d-M-Y h:i a', strtotime($lead->lead_datetime))}}</span>
                                </div>
                                <div class="col-sm-6">
                                    <span class="text-bold mx-1" style="color: var(--wb-wood)">Lead ID: </span>
                                    <span class="mx-1">{{$lead->lead_id}}</span>
                                </div>

                                <div class="col-sm-6">
                                    <span class="text-bold mx-1" style="color: var(--wb-wood)">Name: </span>
                                    <span class="mx-1">{{$lead->name ?: 'N/A'}}</span>
                                </div>
                                <div class="col-sm-6">
                                    <span class="text-bold mx-1" style="color: var(--wb-wood)">Mobile No.: </span>
                                    <span class="mx-1">{{$lead->mobile}}</span>
                                    <div class="phone_action_btns" style="position: absolute; top: -8px; left: 11rem;">
                                        <a target="_blank" href="https://wa.me/{{$lead->mobile}}"
                                            class="text-success text-bold mx-1" style="font-size: 20px;"><i
                                                class="fab fa-whatsapp"></i></a>
                                        <a href="tel:{{$lead->mobile}}" class="text-primary text-bold mx-1"
                                            style="font-size: 20px;"><i class="fa fa-phone-alt"></i></a>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <span class="text-bold mx-1" style="color: var(--wb-wood)">Email: </span>
                                    <span class="mx-1">{{$lead->email ?: 'N/A'}}</span>
                                </div>
                                <div class="col-sm-6">
                                    <span class="text-bold mx-1" style="color: var(--wb-wood)">Alternate Mobile No.:
                                    </span>
                                    <span class="mx-1">{{$lead->alternate_mobile ?: 'N/A'}}</span>
                                </div>
                                <div class="col-sm-6">
                                    <span class="text-bold mx-1" style="color: var(--wb-wood)">Prefered Locality:
                                    </span>
                                    <span class="mx-1">{{$lead->locality ?: 'N/A'}}</span>
                                </div>
                                <div class="col-sm-6">
                                    <span class="text-bold mx-1" style="color: var(--wb-wood)">Lead Status: </span>
                                    <span class="mx-1">{{$lead->lead_status ?: 'N/A'}}</span>
                                </div>
                                <div class="col-sm-6">
                                    <span class="text-bold mx-1" style="color: var(--wb-wood)">Lead Source: </span>
                                    <span class="mx-1">{{$lead->source ?: 'N/A'}}</span>
                                </div>
                                <div class="col-sm-6">
                                    <span class="text-bold mx-1" style="color: var(--wb-wood)">Lead Created By: </span>
                                    <span class="mx-1">{{$lead->get_created_by ? $lead->get_created_by->name : 'N/A'}} -
                                        {{$lead->get_created_by ? $lead->get_created_by->get_role->name : ''}}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-5">
                        <div class="card-header text-light" style="background-color: var(--wb-renosand);">
                            <h3 class="card-title">Event Information</h3>
                            {{-- <button class="btn btn-sm bg-gradient-dark text-xs float-right"
                                title="Add RM Message."><i class="fa fa-plus"></i></button> --}}
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
                                            <td>{{$key+1}}</td>
                                            <td class="text-nowrap">{{date('d-M-Y', strtotime($list->event_datetime))}}
                                            </td>
                                            <td>{{$list->event_name}}</td>
                                            <td>{{$list->pax}}</td>
                                            <td class="text-center">₹ {{number_format($list->budget)}}</td>
                                            <td>{{$list->event_slot}}</td>
                                            <td>{{$list->food_preference}}</td>
                                            <td>{{$list->get_created_by->name ?? ''}} -
                                                {{$list->get_created_by->get_role->name ?? ''}}</td>
                                            <td>{{date('d-M-Y h:i a', strtotime($list->created_at))}}</td>
                                        </tr>
                                        @endforeach
                                        @else
                                        <tr>
                                            <td class="text-center text-muted" colspan="5">No data available in table
                                            </td>
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
                                            <th class="text-nowrap">Status</th>
                                            <th class="text-nowrap">Done With</th>
                                            <th class="text-nowrap">Done Message</th>
                                            <th class="text-nowrap">Done Date</th>
                                            <th class="text-nowrap">Created By</th>
                                            <th class="text-nowrap">Created At</th>
                                        </tr>
                                    </thead>

                                    <body>
                                        @if (sizeof($tasks) > 0)
                                        @foreach ($tasks as $key => $list)
                                        <tr>
                                            <td>{{$key+1}}</td>
                                            <td class="text-nowrap">{{date('d-M-Y h:i a',
                                                strtotime($list->task_schedule_datetime))}}</td>
                                            <td>{{$list->follow_up}}</td>
                                            <td>
                                                <button class="btn"
                                                    onclick="handle_view_message(`{{$list->message ?: 'N/A'}}`)"><i
                                                        class="fa fa-comment-dots"
                                                        style="color: var(--wb-renosand);"></i></button>
                                            </td>
                                            <td>
                                                @php
                                                $schedule_date = date('Y-m-d',
                                                strtotime($list->task_schedule_datetime));
                                                if ($list->done_datetime !== null) {
                                                $elem_class = "success";
                                                $elem_text = "Updated";
                                                } elseif ($schedule_date > $current_date) {
                                                $elem_class = "info";
                                                $elem_text = "Upcoming";
                                                } elseif ($schedule_date == $current_date) {
                                                $elem_class = "warning";
                                                $elem_text = "Today";
                                                } elseif ($schedule_date < $current_date) { $elem_class="danger" ;
                                                    $elem_text="Overdue" ; } @endphp <span
                                                    class="badge badge-{{$elem_class}}">{{$elem_text}}</span>
                                            </td>
                                            <td>{{$list->done_with ?: 'N/A'}}</td>
                                            <td>
                                                <button class="btn"
                                                    onclick="handle_view_message(`{{$list->done_message ?: 'N/A'}}`)"><i
                                                        class="fa fa-comment-dots"
                                                        style="color: var(--wb-renosand);"></i></button>
                                            </td>
                                            <td class="text-nowrap">{{$list->done_datetime ? date('d-M-Y h:i a',
                                                strtotime($list->done_datetime)) : 'N/A'}}</td>
                                            <td class="text-nowrap">
                                                {{-- <button class="btn"
                                                    onclick="handle_view_message(`{{$list->get_created_by->name}} - {{$list->get_created_by->venue_name}}`)"><i
                                                        class="fa fa-comment-dots"
                                                        style="color: var(--wb-renosand);"></i></button> --}}
                                                {{$list->get_created_by->name ?? ''}}
                                            </td>
                                            <td class="text-nowrap">{{date('d-m-Y h:i a',
                                                strtotime($list->created_at))}}</td>
                                        </tr>
                                        @endforeach
                                        @else
                                        <tr>
                                            <td class="text-center text-muted" colspan="10">No data available in table
                                            </td>
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
                                            <th class="text-nowrap">Event Date</th>
                                            <th class="text-nowrap">Menu Selected</th>
                                            <th class="text-nowrap">Party Area</th>
                                            <th class="text-nowrap">Price Quoted</th>
                                            <th class="">Done Message</th>
                                            <th class="text-nowrap">Created By</th>
                                            <th class="text-nowrap">Created At</th>
                                            <th class="text-nowrap">CLH Status</th>
                                            <th class="text-nowrap">Follow Up Date</th>
                                            <th class="text-nowrap">Dropped Reason</th>
                                            <th class="text-nowrap">Action Step Taken by CLH</th>
                                        </tr>
                                    </thead>

                                    <body>
                                        @if (sizeof($visits) > 0)
                                        @foreach ($visits as $key => $list)
                                        <tr>
                                            <td>{{$key+1}}</td>
                                            <td class="text-nowrap">{{date('d-M-Y h:i a',
                                                strtotime($list->visit_schedule_datetime))}}</td>
                                            <td>
                                                <button class="btn"
                                                    onclick="handle_view_message(`{{$list->message ?: 'N/A'}}`)"><i
                                                        class="fa fa-comment-dots"
                                                        style="color: var(--wb-renosand);"></i></button>
                                            </td>
                                            <td>
                                                @php
                                                $schedule_date = date('Y-m-d',
                                                strtotime($list->visit_schedule_datetime));
                                                if ($list->done_datetime !== null) {
                                                $elem_class = "success";
                                                $elem_text = "Updated";
                                                } elseif ($schedule_date > $current_date) {
                                                $elem_class = "info";
                                                $elem_text = "Upcoming";
                                                } elseif ($schedule_date == $current_date) {
                                                $elem_class = "warning";
                                                $elem_text = "Today";
                                                } elseif ($schedule_date < $current_date) { $elem_class="danger" ;
                                                    $elem_text="Overdue" ; } @endphp <span
                                                    class="badge badge-{{$elem_class}}">{{$elem_text}}</span>
                                            </td>
                                            <td>{{$list->event_name}}</td>
                                            <td>{{$list->event_datetime ? date('d-M-Y',
                                                strtotime($list->event_datetime)) : 'N/A'}}</td>
                                            <td>{{$list->menu_selected ?: 'N/A'}}</td>
                                            <td>{{$list->party_area ?: 'N/A'}}</td>
                                            <td class="text-center">{{$list->price_qouted ? "₹ ".
                                                number_format($list->price_qouted) : 'N/A'}}</td>
                                            <td>
                                                <button class="btn"
                                                    onclick="handle_view_message(`{{$list->done_message ?: 'N/A'}}`)"><i
                                                        class="fa fa-comment-dots"
                                                        style="color: var(--wb-renosand);"></i></button>
                                            </td>
                                            <td class="text-nowrap">
                                                {{-- <button class="btn"
                                                    onclick="handle_view_message(`{{$list->get_created_by->name}} - {{$list->get_created_by->venue_name}}`)"
                                                    title="{{$list->referred_by ? 'Referred by '.$list->get_referred_by->name.' ( '.$list->get_referred_by->get_role->name.' )' : ''}}"><i
                                                        class="fa fa-comment-dots {{$list->referred_by ? 'text-primary' : ''}}"
                                                        style="color: var(--wb-renosand);"></i></button> --}}
                                                {{$list->get_created_by->name ?? ''}}
                                            </td>
                                            <td class="text-nowrap">{{date('d-M-Y h:i a',
                                                strtotime($list->created_at))}}</td>
                                            <td class="text-nowrap">{{$list->clh_status ?: 'N/A'}}</td>
                                            <td class="text-nowrap">{{date('d-M-Y',
                                                strtotime($list->clh_follow_up_date))}}</td>
                                            <td class="text-nowrap">{{$list->clh_dropped_reason ?: 'N/A'}}</td>
                                            <td class="text-nowrap"><button class="btn"
                                                    onclick="handle_view_message(`{{$list->clh_action_step_taken ?: 'N/A'}}`)"><i
                                                        class="fa fa-comment-dots"
                                                        style="color: var(--wb-renosand);"></i></button>
                                                <a class="text-success mx-2"
                                                    onclick="clh_edit_visits('{{$list->id}}', '{{$list->clh_status}}','{{$list->clh_follow_up_date}}','{{$list->clh_dropped_reason}}','{{$list->clh_action_step_taken}}')">
                                                    <i class="fa fa-edit" style="font-size: 15px;"></i></a>
                                            </td>
                                        </tr>
                                        @endforeach
                                        @else
                                        <tr>
                                            <td class="text-center text-muted" colspan="12">No data available in table
                                            </td>
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
                                            <th>Action</th>
                                        </tr>
                                    </thead>

                                    <body>
                                        @if (sizeof($bookings) > 0)
                                        @foreach ($bookings as $key => $booking)
                                        <tr>
                                            <td class="text-nowrap">{{date('d-M-Y h:i a',
                                                strtotime($booking->created_at))}}</td>
                                            <td class="text-nowrap">{{$booking->booking_source}}</td>
                                            <td class="text-nowrap">{{$booking->get_vm->venue_name}}</td>
                                            <td class="text-nowrap">{{$booking->get_vm->name}}</td>
                                            <td class="text-nowrap">{{$booking->get_event->event_name}}</td>
                                            <td class="text-nowrap">{{date('d-M-Y h:i a',
                                                strtotime($booking->get_event->event_datetime))}}</td>
                                            <td class="text-nowrap">{{$booking->get_event->event_slot}}</td>
                                            <td class="text-nowrap">{{$booking->get_event->food_preference}}</td>
                                            <td class="text-nowrap">{{$booking->menu_selected}}</td>
                                            <td class="text-nowrap">{{$booking->party_area}}</td>
                                            <td class="text-nowrap">{{number_format($booking->get_event->pax)}}</td>
                                            <td class="text-nowrap">{{number_format($booking->price_per_plate, 2)}}</td>
                                            <td class="text-nowrap">{{number_format($booking->total_gmv, 2)}}</td>
                                            <td class="text-nowrap">{{number_format($booking->advance_amount, 2)}}</td>
                                            <td class="text-nowrap">
                                                @if ($booking->quarter_advance_collected == 0)
                                                <span class="badge badge-danger">Not Collected</span>
                                                @else
                                                <span class="badge badge-success">Received</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="javascript:void(0);"
                                                    onclick="fetch_booking(`{{route('booking.manage_process', $booking->id)}}`, `{{route('booking.fetch', $booking->id)}}`)"
                                                    class="text-success mx-2" title="Edit"><i class="fa fa-edit"
                                                        style="font-size: 15px;"></i></a>
                                            </td>
                                        </tr>
                                        @endforeach
                                        @else
                                        <tr>
                                            <td class="text-center text-muted" colspan="14">No data available in table
                                            </td>
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
                                        @if (sizeof($notes) > 0)
                                        @foreach ($notes as $key => $list)
                                        <tr>
                                            <td>{{$key+1}}</td>
                                            <td>
                                                <button class="btn"
                                                    onclick="handle_view_message(`{{$list->message ?: 'N/A'}}`)"><i
                                                        class="fa fa-comment-dots"
                                                        style="color: var(--wb-renosand);"></i></button>
                                            </td>
                                            <td class="text-nowrap">{{$list->get_created_by->name ?? ''}}</td>
                                            <td class="text-nowrap">{{date('d-M-Y h:i a',
                                                strtotime($list->created_at))}}</td>
                                        </tr>
                                        @endforeach
                                        @else
                                        <tr>
                                            <td class="text-center text-muted" colspan="5">No data available in table
                                            </td>
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
                                        @php
                                        $done_leads = $leads_forward->where('lead_status', 'Done')->get();
                                        @endphp
                                        @if (sizeof($done_leads) > 0)
                                        @foreach ($done_leads as $key => $list)
                                        <tr>
                                            <td>{{$key+1}}</td>
                                            <td>{{$list->get_forward_to->name}} - {{$list->get_forward_to->venue_name}}
                                            </td>
                                            <td>{{date('d-M-Y h:i a', strtotime($list->updated_at))}}</td>
                                            <td>
                                                <button class="btn"
                                                    onclick="handle_view_message(`{{$list->done_title ?: 'N/A'}}`)"><i
                                                        class="fa fa-comment-dots"
                                                        style="color: var(--wb-renosand);"></i></button>
                                            </td>
                                            <td>
                                                <button class="btn"
                                                    onclick="handle_view_message(`{{$list->done_message ?: 'N/A'}}`)"><i
                                                        class="fa fa-comment-dots"
                                                        style="color: var(--wb-renosand);"></i></button>
                                            </td>
                                        </tr>
                                        @endforeach
                                        @else
                                        <tr>
                                            <td class="text-center text-muted" colspan="5">No data available in table
                                            </td>
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
                                    <th class="text-nowrap">Forwarded At</th>
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
    <div class="modal fade" id="editVisitModal" tabindex="-1" aria-labelledby="editVisitModalLabel" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editVisitModalLabel">Edit Visit</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="editVisitForm" action="{{route('manager.visit.update')}}" method="POST">
                        @csrf
                        <input type="hidden" id="visit_id" name="visit_id" />
                        <div class="mb-3">
                            <label for="clh_status" class="form-label">Status</label>
                            <select class="form-control" id="clh_status" name="clh_status">
                                <option value="" disabled selected>Select</option>
                                <option value="Active">Active</option>
                                <option value="Hot">Hot</option>
                                <option value="Super Hot">Super Hot</option>
                                <option value="Done">Done</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="clh_follow_up_date" class="form-label">Follow-up Date</label>
                            <input type="date" class="form-control" id="clh_follow_up_date" name="clh_follow_up_date">
                        </div>
                        <div class="mb-3">
                            <label for="clh_dropped_reason" class="form-label">Dropped Reason</label>
                            <select class="form-control" id="clh_dropped_reason" name="clh_dropped_reason">
                                <option value="" disabled selected>Select</option>
                                <option value="Budget low">Budget low</option>
                                <option value="Different locality">Different locality</option>
                                <option value="Venue small">Venue small</option>
                                <option value="Date not available">Date not available</option>
                                <option value="Looking for more premium">Looking for more premium</option>
                                <option value="Venue too big">Venue too big</option>
                                <option value="Did not like venue">Did not like venue</option>
                                <option value="Did not like food">Did not like food</option>
                                <option value="Does not like area around">Does not like area around</option>
                                <option value="Event cancelled postponed">Event cancelled postponed</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="clh_action_step_taken" class="form-label">Action Steps Taken by CLH</label>
                            <textarea class="form-control" id="clh_action_step_taken" name="clh_action_step_taken"
                                rows="3"></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
    @include('manager.venueCrm.lead.forward_leads_modal')
    @include('manager.venueCrm.lead.lead_forwarded_info_modal')
    @include('includes.manage_booking_modal')
</div>
@endsection
@section('footer-script')
<script>
    function clh_edit_visits(visit_id, clh_status, clh_follow_up_date, clh_dropped_reason, clh_action_step_taken){
        document.getElementById('visit_id').value = visit_id;
        document.getElementById('clh_status').value = clh_status;
    document.getElementById('clh_follow_up_date').value = clh_follow_up_date;
    document.getElementById('clh_dropped_reason').value = clh_dropped_reason;
    document.getElementById('clh_action_step_taken').value = clh_action_step_taken;

    // Store visit_id in a data attribute
    document.getElementById('editVisitForm').dataset.visitId = visit_id;

    // Show the modal
    const editModal = new bootstrap.Modal(document.getElementById('editVisitModal'));
    editModal.show();
    }
</script>
@endsection
