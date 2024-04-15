@extends('admin.layouts.app')
@section('title', 'View NV Lead | Non Venue CRM')
@section('main')
<div class="content-wrapper pb-5">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>View NV Lead</h1>
                </div>
            </div>
            <div class="button-group my-4">
                <a href="javascript:void(0);" class="btn text-light btn-sm buttons-print mx-1" data-bs-toggle="modal" data-bs-target="#forwardLeadModal" style="background-color: var(--wb-dark-red)"><i class="fa fa-paper-plane"></i> Forward to NVRM's</a>
                <button onclick="handle_get_nvlead_forwarded_info({{$lead->id}})" class="btn btn-sm mx-1 btn-info" title="Forward info">Forward Info: {{$forwarded_count}}</button>
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
                                        @if (sizeof($lead->get_nvrm_messages) > 0)
                                            @foreach ($lead->get_nvrm_messages as $key => $list)
                                            <tr>
                                                <td>{{$key+1}}</td>
                                                <td>{{date('d-M-Y h:i a', strtotime($list->created_at))}}</td>
                                                <td>{{$list->get_created_by->name ?? ''}}</td>
                                                <td>{{$list->title}}</td>
                                                <td>
                                                    <button class="btn" onclick="handle_view_message(`{{$list->message ?: 'N/A'}}`)"><i class="fa fa-comment-dots" style="color: var(--wb-renosand);"></i></button>
                                                </td>
                                            </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td class="text-center text-muted" colspan="5">No data available in table</td>
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
                            <a href="javascript:void(0);" class="text-light float-right" title="Edit" data-bs-toggle="modal" data-bs-target="#editLeadModal"><i class="fa fa-edit" style="font-size: 15px;"></i></a>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <span class="text-bold mx-1" style="color: var(--wb-wood)">Lead Date: </span>
                                    <span class="mx-1">{{date('d-M-Y h:i a', strtotime($lead->lead_datetime))}}</span>
                                </div>
                                <div class="col-sm-6">
                                    <span class="text-bold mx-1" style="color: var(--wb-wood)">Lead ID: </span>
                                    <span class="mx-1">{{$lead->id}}</span>
                                </div>
                                <div class="col-sm-6">
                                    <span class="text-bold mx-1" style="color: var(--wb-wood)">Name: </span>
                                    <span class="mx-1">{{$lead->name ?: 'N/A'}}</span>
                                </div>
                                <div class="col-sm-6">
                                    <span class="text-bold mx-1" style="color: var(--wb-wood)">Mobile No.: </span>
                                    <span class="mx-1">{{$lead->mobile}}</span>
                                    <div class="phone_action_btns" style="position: absolute; top: -8px; left: 11rem;">
                                        <a target="_blank" href="https://wa.me/{{$lead->mobile}}" class="text-success text-bold mx-1" style="font-size: 20px;"><i class="fab fa-whatsapp"></i></a>
                                        <a href="tel:{{$lead->mobile}}" class="text-primary text-bold mx-1" style="font-size: 20px;"><i class="fa fa-phone-alt"></i></a>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <span class="text-bold mx-1" style="color: var(--wb-wood)">Email: </span>
                                    <span class="mx-1">{{$lead->email ?: 'N/A'}}</span>
                                </div>
                                <div class="col-sm-6">
                                    <span class="text-bold mx-1" style="color: var(--wb-wood)">Alternate Mobile No.: </span>
                                    <span class="mx-1">{{$lead->alternate_mobile ?: "N/A"}}</span>
                                </div>
                                <div class="col-sm-6">
                                    <span class="text-bold mx-1" style="color: var(--wb-wood)">Address: </span>
                                    <span class="mx-1">{{$lead->address ?: 'N/A'}}</span>
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
                                            <th class="">Slot</th>
                                            <th class="text-nowrap">Venue Name</th>
                                            <th class="text-nowrap">Pax</th>
                                            <th class="">Created By</th>
                                            <th class="">Created At</th>
                                        </tr>
                                    </thead>
                                    <body>
                                        @if (sizeof($lead->get_events) > 0)
                                            @foreach ($lead->get_events as $key => $list)
                                            <tr>
                                                <td>{{$key+1}}</td>
                                                <td class="text-nowrap">{{date('d-M-Y', strtotime($list->event_datetime))}}</td>
                                                <td>{{$list->event_name ?: 'N/A'}}</td>
                                                <td>{{$list->event_slot ?: 'N/A'}}</td>
                                                <td>{{$list->venue_name ?: 'N/A'}}</td>
                                                <td>{{$list->pax ?: 'N/A'}}</td>
                                                <td>{{$list->get_created_by->name ?? ''}} - {{$list->get_created_by->get_role->name ?? ''}}</td>
                                                <td>{{date('d-M-Y h:i a', strtotime($list->created_at))}}</td>
                                            </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td class="text-center text-muted" colspan="9">No data available in table</td>
                                            </tr>
                                        @endif
                                    </body>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-5">
                        <div class="card-header text-light" style="background-color: var(--wb-renosand);">
                            <h3 class="card-title">NVRM Task Details</h3>
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
                                                    <td class="">{{ $list->creator->name?? '' }} -
                                                        {{ $list->creator->get_role->name ?? '' }}</td>
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
                            <h3 class="card-title">Vendors Tasks Details</h3>
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
                                        @if (sizeof($lead->get_tasks_vendor) > 0)
                                            @foreach ($lead->get_tasks_vendor as $key => $list)
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
                                                    <td class="">{{ $list->creator->name ?? '' }} -
                                                        {{ $list->creator->get_category->name ?? '' }}</td>
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
                    <div class="card mb-5" id="get_nvrm_help_messages_card">
                        <div class="card-header text-light" style="background-color: var(--wb-renosand);">
                            <h3 class="card-title">Vendor Help Section</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="serverTable" class="table mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-nowrap">S.No.</th>
                                            <th class="">Created At</th>
                                            <th class="">Message</th>
                                            <th class="">Created By</th>
                                            <th class="">Status</th>
                                            <th class="">Responsed At</th>
                                            <th class="">Response Msg</th>
                                            <th class="">Responsed By</th>
                                        </tr>
                                    </thead>
                                    <body>
                                        @php
                                        $helpmsg = $lead->get_nvrm_help_messages();
                                        @endphp
                                        @if (sizeof($helpmsg) > 0)
                                        @foreach ($helpmsg as $key => $list)
                                        <tr style="{{ $list->is_solved === 0 ? 'background-color: #00992385;' : '' }}">
                                            <td>{{$key+1}}</td>
                                            <td class="text-nowrap">{{date('d-M-Y h:i a', strtotime($list->created_at))}}</td>
                                            <td>
                                                <button class="btn" onclick="handle_view_message(`{{$list->message ?: 'N/A'}}`)"><i class="fa fa-comment-dots" style="color: var(--wb-renosand);"></i></button>
                                            </td>
                                            <td class="text-nowrap">{{$list->created_by_name}} -- {{$list->category_name}}</td>
                                            <td>
                                                @php
                                                if ($list->status == 1 && $list->done_datetime !== null) {
                                                    $elem_class_help = "success";
                                                    $elem_text_help = "Responsed";
                                                } elseif ($list->done_datetime === null && $list->status != 1) {
                                                    $elem_class_help = "danger";
                                                    $elem_text_help = "Recived";
                                                }
                                                @endphp
                                                @if ($list->done_datetime !== null)
                                                    <span class="badge badge-{{$elem_class_help}}">{{$elem_text_help}}</span>
                                                @else
                                                    <button class="btn btn-{{$elem_class_help}} dropdown-toggle btn-xs" data-bs-toggle="dropdown" style="font-size: 75% !important;">{{$elem_text_help}}</button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a class="dropdown-item" href="javascript:void(0);" onclick="handle_vendor_help_update( {{$list->id}}, `{{$list->created_by_name}}`, `{{$list->category_name}}` )">Response</a>
                                                        </li>
                                                    </ul>
                                                @endif
                                            </td>
                                            <td class="text-nowrap">{{$list->done_datetime ?? 'N/A'}}</td>
                                            <td>
                                                <button class="btn" onclick="handle_view_message(`{{$list->nvrm_msg ?: 'N/A'}}`)"><i class="fa fa-comment-dots" style="color: var(--wb-renosand);"></i></button>
                                            </td>
                                            <td class="text-nowrap">{{ $list->done_by_name ?? 'N/A' }}</td>
                                        </tr>
                                        @endforeach
                                        @else
                                        <tr>
                                            <td class="text-center text-muted" colspan="5">No data available in table</td>
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
    <div class="modal fade" id="editLeadModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Edit NV Lead</h4>
                    <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                </div>
                <form action="{{route('admin.nvlead.edit.process', $lead->id)}}" method="post">
                    <div class="modal-body text-sm">
                        @csrf
                        <div class="row">
                            <div class="col-sm-4 mb-3">
                                <div class="form-group">
                                    <label for="nv_lead_name_inp">Name</label>
                                    <input type="text" class="form-control" id="nv_lead_name_inp" placeholder="Enter name" name="name" value="{{$lead->name}}">
                                </div>
                            </div>
                            <div class="col-sm-4 mb-3">
                                <div class="form-group">
                                    <label for="nv_lead_email_inp">Email</label>
                                    <input type="email" class="form-control" id="nv_lead_email_inp" placeholder="Enter email" name="email" value="{{$lead->email}}">
                                </div>
                            </div>
                            <div class="col-sm-4 mb-3">
                                <div class="form-group">
                                    <label for="nv_lead_mobile_inp">Mobile No. <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nv_lead_mobile_inp" placeholder="Enter mobile no." name="mobile_number" value="{{$lead->mobile}}" disabled title="Primary phone number cannot be edit.">
                                </div>
                            </div>
                            <div class="col-sm-4 mb-">
                                <div class="form-group">
                                    <label for="nv_lead_alt_mobile_inp">Alternate Mobile No.</label>
                                    <input type="text" class="form-control" id="nv_lead_alt_mobile_inp" placeholder="Enter alternate mobile no." name="alternate_mobile_number" value="{{$lead->alternate_mobile}}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer text-sm">
                        <button type="button" class="btn btn-sm bg-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm text-light" style="background-color: var(--wb-dark-red);">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @include('admin.nonVenueCrm.nvlead.forward_leads_modal')
    @include('admin.nonVenueCrm.nvlead.nvlead_forwarded_info_modal')
</div>
@endsection
