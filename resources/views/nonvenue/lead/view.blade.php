@extends('nonvenue.layouts.app')
@php
    $page_title = $lead->name ?: 'N/A';
    $page_title .= " | $lead->mobile | View Lead | Non Venue CRM";
    $current_date = date('Y-m-d');
    $active_task_count = 0;
    $elem_class_help = "";
    $elem_text_help = "";
    $elem_class = "";
    $elem_text = "";
@endphp
@section('title', $page_title)
@section('main')
    <style>
    </style>
    <div class="content-wrapper pb-5">
        <section class="content-header">
            <div class="container-fluid">
                <h1>View Lead</h1>
            </div>
        </section>
        <section class="content">
            <div id="view_lead_card_container" class="card text-sm">
                <div class="card-header">
                    <button class="btn btn-xs text-light px-2 m-1" style="background-color: var(--wb-renosand)"
                        data-bs-toggle="modal" data-bs-target="#manageRmMessageModal"><i class="fa fa-plus"></i> Add RM
                        Message</button>
                    <button class="btn btn-xs text-light px-2 m-1" style="background-color: var(--wb-dark-red)"
                        onclick="handle_event_information(`{{ route('nonvenue.event.add.process') }}`)"><i
                            class="fa fa-plus"></i> Add Event</button>
                    <div class="dropdown d-inline-block">
                        <a href="javascript:void(0);"
                            class="btn dropdown-toggle text-light btn-xs px-2 mx-1 {{ $lead->lead_status == 'Done' ? 'bg-secondary' : '' }}"
                            data-bs-toggle="dropdown" style="background-color: var(--wb-renosand);"><i
                                class="fa fa-chart-line"></i> Lead: {{ $lead->lead_status }}</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" onclick="return confirm('Are you sure want to active this lead?')"
                                    href="{{ route('nonvenue.lead.status.update', $lead->id) }}/Active">Active</a></li>
                            <li><a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal"
                                    data-bs-target="#manageLeadStatusModal">Done</a></li>
                        </ul>
                    </div>
                    <div class="dropdown d-inline-block">
                        @if ($lead->service_status == 1)
                            <button class="btn btn-success dropdown-toggle btn-xs px-2 m-1" data-bs-toggle="dropdown"><i class="fa fa-phone"></i> Service Status: Contacted</button>
                        @else
                            <button class="btn btn-danger dropdown-toggle btn-xs px-2 m-1" data-bs-toggle="dropdown"><i class="fa fa-phone-slash"></i> Service Status: Not Contacted</button>
                        @endif
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{route('nonvenue.lead.serviceStatus.update', $lead->id)}}/1">Contacted</a></li>
                            <li><a class="dropdown-item" href="{{route('nonvenue.lead.serviceStatus.update', $lead->id)}}/0">Not Contacted</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="container-fluid">
                        <div class="card mb-5">
                            <div class="card-header text-light" style="background-color: var(--wb-renosand);">
                                <h3 class="card-title">RM Message's</h3>
                                <button class="btn p-0 text-light float-right" title="Add RM message" data-bs-toggle="modal"
                                    data-bs-target="#manageRmMessageModal"><i class="fa fa-plus"
                                        style="font-size: 15px;"></i></button>
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
                                                <th class="">Tentative Budget</th>
                                                <th class="">Service Category</th>
                                                <th class="text-center text-nowrap">Action</th>
                                            </tr>
                                        </thead>

                                        <body>
                                            @if (sizeof($lead->get_nvrm_messages) > 0)
                                                @foreach ($lead->get_nvrm_messages as $key => $list)
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
                                                        <td>₹ {{ $list->budget ? number_format($list->budget) : 'N/A' }}
                                                        </td>
                                                        <td>{{ $list->get_service_category->name }}</td>
                                                        <td class="text-center text-nowrap">
                                                            <button
                                                                onclick="handle_lead_forward({{ $list->get_service_category->id }}, `{{ $list->get_service_category->name }}`)"
                                                                class="btn p-0 mx-2" title="Forward"
                                                                style="color: var(--wb-dark-red);"><i
                                                                    class="fa fa-paper-plane"></i></button>
                                                            {{-- <button onclick="handle_get_nvlead_forwarded_info(${data.id})" class="btn mx-2 p-0 px-2 btn-info" title="Forwarded info"><i class="fa fa-share-alt" style="font-size: 15px;"></i> 0</button> --}}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td class="text-center text-muted" colspan="8">No data available in
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
                                <button class="btn p-0 text-light float-right" title="Edit" data-bs-toggle="modal"
                                    data-bs-target="#editLeadModal"><i class="fa fa-edit"
                                        style="font-size: 15px;"></i></button>
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
                                        <div class="phone_action_btns d-flex" style="position: absolute; top: -8px; left: 11rem;">
                                            <a href="#" class="d-flex"><div> </div>&nbsp;&nbsp;&nbsp;<i class="fab fa-whatsapp" onclick="handle_whatsapp_msg({{$lead->mobile}})" style="font-size: 25px; color: green;"></i></a>
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
                                        <span class="text-bold mx-1" style="color: var(--wb-wood)">Done Title: </span>
                                        <span class="mx-1">{{ $lead->done_title ?: 'N/A' }}</span>
                                    </div>
                                    <div class="col-sm-6">
                                        <span class="text-bold mx-1" style="color: var(--wb-wood)">Address: </span>
                                        <span class="mx-1">{{ $lead->address ?: 'N/A' }}</span>
                                    </div>
                                    <div class="col-sm-6">
                                        <span class="text-bold mx-1" style="color: var(--wb-wood)">Done Message: </span>
                                        <span class="mx-1">{{ $lead->done_message ?: 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-5">
                            <div class="card-header text-light" style="background-color: var(--wb-renosand);">
                                <h3 class="card-title">Event Information</h3>
                                <button class="btn p-0 text-light float-right" title="Add Event."
                                    onclick="handle_event_information(`{{ route('nonvenue.event.add.process') }}`)"><i
                                        class="fa fa-plus" style="font-size: 15px;"></i></button>
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
                                                <th class="">Action</th>
                                            </tr>
                                        </thead>

                                        <body>
                                            @if (sizeof($lead->get_events) > 0)
                                                @foreach ($lead->get_events as $key => $list)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td class="text-nowrap">
                                                            {{ date('d-M-Y', strtotime($list->event_datetime)) }}</td>
                                                        <td>{{ $list->event_name ?: 'N/A' }}</td>
                                                        <td>{{ $list->event_slot ?: 'N/A' }}</td>
                                                        <td>{{ $list->venue_name ?: 'N/A' }}</td>
                                                        <td>{{ $list->pax ?: 'N/A' }}</td>
                                                        <td>
                                                            <button
                                                                onclick="handle_event_information(`{{ route('nonvenue.event.edit.process', $list->id) }}`, `{{ route('nonvenue.event.edit', $list->id) }}`)"
                                                                class="btn p-0 text-success" title="Edit Event."><i
                                                                    class="fa fa-edit"></i></button>
                                                        </td>
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
                        <div id="task_card_container" class="card mb-5">
                            <div class="card-header text-light" style="background-color: var(--wb-renosand);">
                                <h3 class="card-title">Task Details</h3>
                                <button data-bs-toggle="modal" data-bs-target="#manageTaskModal" class="btn p-0 text-light float-right" title="Add Task."><i class="fa fa-plus"></i></button>
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
                                                <th class="text-nowrap">Action</th>
                                            </tr>
                                        </thead>

                                        <body>
                                            @php
                                                    $tasks = $lead->get_nvrm_tasks();
                                            @endphp
                                            @if (sizeof($tasks) > 0)
                                            @foreach ($tasks as $key => $list)
                                            <tr>
                                                <td>{{$key+1}}</td>
                                                <td class="">{{date('d-M-Y h:i a', strtotime($list->task_schedule_datetime))}}</td>
                                                <td>{{$list->follow_up}}</td>
                                                <td>
                                                    <button class="btn" onclick="handle_view_message(`{{$list->message ?: 'N/A'}}`)"><i class="fa fa-comment-dots" style="color: var(--wb-renosand);"></i></button>
                                                </td>
                                                <td>
                                                    @php
                                                    $schedule_date = date('Y-m-d', strtotime($list->task_schedule_datetime));
                                                    if ($list->done_datetime !== null) {
                                                        $elem_class = "success";
                                                        $elem_text = "Updated";
                                                    } elseif ($schedule_date > $current_date) {
                                                        $elem_class = "info";
                                                        $elem_text = "Upcoming";
                                                    } elseif ($schedule_date == $current_date) {
                                                        $elem_class = "warning";
                                                        $elem_text = "Today";
                                                    } elseif ($schedule_date < $current_date) {
                                                        $elem_class = "danger";
                                                        $elem_text = "Overdue";
                                                    }
                                                    @endphp
                                                    @if ($list->done_datetime !== null)
                                                        <span class="badge badge-{{$elem_class}}">{{$elem_text}}</span>
                                                    @else
                                                        <button class="btn btn-{{$elem_class}} dropdown-toggle btn-xs" data-bs-toggle="dropdown" style="font-size: 75% !important;">{{$elem_text}}</button>
                                                        <ul class="dropdown-menu">
                                                            <li>
                                                                <a class="dropdown-item" href="javascript:void(0);" onclick="handle_task_status_update({{$list->id}})">Task Update</a>
                                                            </li>
                                                        </ul>
                                                        @php
                                                            $active_task_count++;
                                                        @endphp
                                                    @endif
                                                </td>
                                                <td>{{$list->done_with ?: 'N/A'}}</td>
                                                <td>
                                                    <button class="btn" onclick="handle_view_message(`{{$list->done_message ?: 'N/A'}}`)"><i class="fa fa-comment-dots" style="color: var(--wb-renosand);"></i></button>
                                                </td>
                                                <td class="">{{$list->done_datetime ? date('d-M-Y h:i a', strtotime($list->done_datetime)) : 'N/A'}}</td>
                                                <td class="text-nowrap">
                                                    @if ($list->done_datetime == null)
                                                        <a href="{{route('nonvenue.task.delete', $list->id)}}" onclick="return confirm('Are you sure want to delete the task?')" class="text-danger mx-2"><i class="fa fa-trash-alt"></i></a>
                                                    @else
                                                        <button class="btn p-0 text-secondary mx-2" disabled><i class="fa fa-trash-alt" title="Done task cannot be deleted."></i></button>
                                                    @endif
                                                </td>
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
        <div class="modal fade" id="manageTaskModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Add Task</h4>
                        <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                    </div>
                    <form action="{{route('nonvenue.task.add.process')}}" id="manage_task_form" method="post">
                        <div class="modal-body text-sm">
                            @csrf
                            <div class="row">
                                <div class="col-sm-6 mb-3">
                                    <div class="form-group">
                                        <input type="hidden" name="lead_id" value="{{$lead->lead_id}}">
                                        <label for="task_schedule_datetime_inp">Task Schedule Date Time <span class="text-danger">*</span></label>
                                        <input type="datetime-local" id="task_schedule_datetime_inp" min="{{date('Y-m-d H:i')}}" class="form-control" name="task_schedule_datetime" required>
                                    </div>
                                </div>
                                <div class="col-sm-6 mb-3">
                                    <div class="form-group">
                                        <label for="task_follow_up_select">Task Follow Up</label>
                                        <select class="form-control" id="task_follow_up_select" name="task_follow_up">
                                            <option value="Call">Call</option>
                                            <option value="SMS">SMS</option>
                                            <option value="Mail">Mail</option>
                                            <option value="WhatsApp">WhatsApp</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-12 mb-3">
                                    <div class="form-group">
                                        <label for="task_message_textarea">Message</label>
                                        <textarea type="text" class="form-control" id="task_message_textarea" placeholder="Enter task message." name="task_message"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer text-sm">
                            <div class="col">
                                <p>
                                    <span class="text-danger">*</span>
                                    Fields are required.
                                </p>
                            </div>
                            <button type="button" class="btn btn-sm bg-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-sm text-light" style="background-color: var(--wb-dark-red);">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" id="manageTaskStatusModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Update Task Status</h4>
                        <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                    </div>
                    <form id="task_status_update_form" method="post">
                        <div class="modal-body text-sm">
                            <div class="form-group mb-3">
                                @csrf
                                <label for="task_done_with_select">Task Done With <span class="text-danger">*</span></label>
                                <select class="form-control" id="task_done_with_select" name="task_done_with" required>
                                    <option value="Call">Call</option>
                                    <option value="SMS">SMS</option>
                                    <option value="Mail">Mail</option>
                                    <option value="WhatsApp">WhatsApp</option>
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label for="task_done_message_textarea">Done Message <span class="text-danger">*</span></label>
                                <textarea type="text" class="form-control" id="task_done_message_textarea" placeholder="Enter done message." name="task_done_message"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer text-sm">
                            <div class="col">
                                <p>
                                    <span class="text-danger">*</span>
                                    Fields are required.
                                </p>
                            </div>
                            <button type="button" class="btn btn-sm bg-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-sm text-light" style="background-color: var(--wb-dark-red);">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" id="manageVendorHelpModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="manageVendorHelpModalTitle">Resonse to Vendor</h4>
                        <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                    </div>
                    <form id="vendor_help_update_form" method="post">
                        <div class="modal-body text-sm">
                                @csrf
                            <div class="form-group mb-3">
                                <label for="vendor_help_reponse_message_textarea">Response Message <span class="text-danger">*</span></label>
                                <textarea type="text" class="form-control" id="vendor_help_reponse_message_textarea" placeholder="Enter response message." name="vendor_help_reponse_message"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer text-sm">
                            <div class="col">
                                <p>
                                    <span class="text-danger">*</span>
                                    Fields are required.
                                </p>
                            </div>
                            <button type="button" class="btn btn-sm bg-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-sm text-light" style="background-color: var(--wb-dark-red);">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" id="editLeadModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Edit NV Lead</h4>
                        <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i
                                class="fa fa-times"></i></button>
                    </div>
                    <form action="{{ route('nonvenue.lead.edit.process', $lead->id) }}" method="post">
                        <div class="modal-body text-sm">
                            @csrf
                            <div class="row">
                                <div class="col-sm-4 mb-3">
                                    <div class="form-group">
                                        <label for="nv_lead_name_inp">Name</label>
                                        <input type="text" class="form-control" id="nv_lead_name_inp"
                                            placeholder="Enter name" name="name" value="{{ $lead->name }}">
                                    </div>
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <div class="form-group">
                                        <label for="nv_lead_email_inp">Email</label>
                                        <input type="email" class="form-control" id="nv_lead_email_inp"
                                            placeholder="Enter email" name="email" value="{{ $lead->email }}">
                                    </div>
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <div class="form-group">
                                        <label for="nv_lead_mobile_inp">Mobile No. <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="nv_lead_mobile_inp"
                                            placeholder="Enter mobile no." name="mobile_number"
                                            value="{{ $lead->mobile }}" disabled
                                            title="Primary phone number cannot be edit.">
                                    </div>
                                </div>
                                <div class="col-sm-4 mb-">
                                    <div class="form-group">
                                        <label for="nv_lead_alt_mobile_inp">Alternate Mobile No.</label>
                                        <input type="text" class="form-control" id="nv_lead_alt_mobile_inp"
                                            placeholder="Enter alternate mobile no." name="alternate_mobile_number"
                                            value="{{ $lead->alternate_mobile }}">
                                    </div>
                                </div>
                                <div class="col-sm-12 mb-">
                                    <div class="form-group">
                                        <label for="nv_lead_alt_address_inp">Address</label>
                                        <textarea type="text" class="form-control" id="nv_lead_alt_address_inp"
                                            placeholder="Enter address." name="address"
                                            >{{ $lead->address }}</textarea>
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
        <div class="modal fade" id="manageRmMessageModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Add RM Message</h4>
                        <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i
                                class="fa fa-times"></i></button>
                    </div>
                    <form action="{{ route('nonvenue.rm_message.manage.process') }}" method="post">
                        <div class="modal-body text-sm">
                            @csrf
                            <input type="hidden" name="lead_id" value="{{ $lead->lead_id }}">
                            <div class="form-group">
                                <label for="msg_service_category_select">Service category <span
                                        class="text-danger">*</span></label>
                                <select name="service_category" id="msg_service_category_select" class="form-control"
                                    required>
                                    <option value="" disabled selected>Select service category</option>
                                    @foreach ($service_categories as $list)
                                        <option value="{{ $list->id }}">{{ $list->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="msg_budget_inp">Tentative Budget (in INR)</label>
                                <input type="text" class="form-control" id="msg_budget_inp"
                                    placeholder="Enter budget value" name="budget" required
                                    onblur="integer_validate(this)">
                                <span class="text-danger ml-1 position-absolute d-none">Invalid integer value</span>
                            </div>
                            <div class="form-group">
                                <label for="msg_title_inp">Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="msg_title_inp" placeholder="Enter title"
                                    name="title" required>
                            </div>
                            <div class="form-group">
                                <label for="msg_desc_inp">Message</label>
                                <textarea type="text" class="form-control" id="msg_desc_inp" placeholder="Type message" name="message"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer text-sm">
                            <div class="col">
                                <p class="">
                                    <span class="text-danger">*</span>
                                    Fields are required.
                                </p>
                            </div>
                            <button type="button" class="btn btn-sm btn-secondary"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-sm text-light"
                                style="background-color: var(--wb-dark-red);">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" id="manageEventModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Create Event</h4>
                        <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                    </div>
                    <form id="manage_event_form" method="post">
                        <div class="modal-body text-sm">
                            @csrf
                            <div class="row">
                                <div class="col-sm-4 mb-3">
                                    <div class="form-group">
                                        <label for="nv_lead_event_name_inp">Event Name</label>
                                        <input type="hidden" name="forward_id" value="{{ $lead->id }}">
                                        <input type="text" class="form-control" id="nv_lead_event_name_inp"
                                            placeholder="Enter event name" name="event_name">
                                    </div>
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <div class="form-group">
                                        <label for="nv_lead_event_date_inp">Event Date</label>
                                        <input type="date" min="{{ date('Y-m-d') }}" class="form-control"
                                            id="nv_lead_event_date_inp" name="event_date">
                                    </div>
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <div class="form-group">
                                        <label for="nv_lead_event_slot_select">Event Slot</label>
                                        <select class="form-control" id="nv_lead_event_slot_select" name="event_slot">
                                            <option value="" selected disabled>Select event slot</option>
                                            <option value="Morning">Morning</option>
                                            <option value="Evening">Evening</option>
                                            <option value="Full Day">Full Day</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <div class="form-group">
                                        <label for="nv_lead_venue_name">Venue Name</label>
                                        <input type="text" class="form-control" id="nv_lead_venue_name"
                                            placeholder="Enter venue name" name="venue_name">
                                    </div>
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <div class="form-group">
                                        <label for="nv_lead_pax_inp">Number of Guest</label>
                                        <input type="text" class="form-control" id="nv_lead_pax_inp"
                                            placeholder="Enter number of guest" name="number_of_guest">
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
        <div class="modal fade" id="forwardLeadModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header align-items-center">
                        <h4 class="modal-title"></h4>
                        {{-- <div class="form-check d-flex align-items-center" style="column-gap: 10px;">
                        <input class="form-check-input position-static" id="select_all_rms" type="checkbox" onclick="handle_select_all(this, '.checkbox_for_vendors')" style="height: 1.5rem; width: 1.5rem;">
                        <label class="form-check-label" for="select_all_rms">Select All</label>
                    </div> --}}
                        <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i
                                class="fa fa-times"></i></button>
                    </div>
                    <form action="{{ route('nonvenue.lead.forward') }}" method="post">
                        @csrf
                        <input type="hidden" name="forward_id" value="{{ $lead->id }}">
                        <input type="hidden" name="nvrm_msg_id" id="nvrm_msg_id" value="">
                        <div class="modal-body text-sm">
                            <div>
                                <div class="">
                                    <h5 class="d-inline-block mr-3">Group A</h5>
                                    <input type="checkbox" name="" id=""
                                        style="width: 1.2rem; height: 1.2rem;">
                                    <div class="d-flex ml-3 my-1 mb-3" style="column-gap: 3rem;">
                                        <div class="form-check d-flex align-items-center">
                                            <input class="form-check-input checkbox_for_vendors" type="checkbox"
                                                name="forward_vendors_id[]">
                                            <label class="form-check-label">Vendor1</label>
                                        </div>
                                        <div class="form-check d-flex align-items-center">
                                            <input class="form-check-input checkbox_for_vendors" type="checkbox"
                                                name="forward_vendors_id[]">
                                            <label class="form-check-label"
                                                for="forward_vendors_id_checkbox${default_vendors[i].id}">Vendor2</label>
                                        </div>
                                        <div class="form-check d-flex align-items-center">
                                            <input class="form-check-input checkbox_for_vendors" type="checkbox"
                                                name="forward_vendors_id[]">
                                            <label class="form-check-label"
                                                for="forward_vendors_id_checkbox${default_vendors[i].id}">Vendor2</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="">
                                    <h5 class="d-inline-block mr-3">Group B</h5>
                                    <input type="checkbox" name="" id=""
                                        style="width: 1.2rem; height: 1.2rem;">
                                    <div class="d-flex ml-3 my-1 mb-3" style="column-gap: 3rem;">
                                        <div class="form-check d-flex align-items-center">
                                            <input class="form-check-input checkbox_for_vendors" type="checkbox"
                                                name="forward_vendors_id[]">
                                            <label class="form-check-label">Vendor1</label>
                                        </div>
                                        <div class="form-check d-flex align-items-center">
                                            <input class="form-check-input checkbox_for_vendors" type="checkbox"
                                                name="forward_vendors_id[]">
                                            <label class="form-check-label"
                                                for="forward_vendors_id_checkbox${default_vendors[i].id}">Vendor2</label>
                                        </div>
                                        <div class="form-check d-flex align-items-center">
                                            <input class="form-check-input checkbox_for_vendors" type="checkbox"
                                                name="forward_vendors_id[]">
                                            <label class="form-check-label"
                                                for="forward_vendors_id_checkbox${default_vendors[i].id}">Vendor2</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-sm bg-secondary"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" onclick="btn_preloader(this)" class="btn btn-sm text-light"
                                style="background-color: var(--wb-dark-red);">Forward</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" id="manageLeadStatusModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Lead Done</h4>
                        <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i
                                class="fa fa-times"></i></button>
                    </div>
                    <form action="{{ route('nonvenue.lead.status.update', $lead->id) }}" method="post">
                        <div class="modal-body text-sm">
                            @csrf
                            <div class="form-group">
                                <input type="hidden" name="forward_id" value="{{ $lead->id }}">
                                <label for="done_title_select">Done Title <span class="text-danger">*</span></label>
                                <select class="form-control" id="done_title_select" name="done_title" required>
                                    <option value="Budget low.">Budget low. </option>
                                    <option value=" very small function ."> very small function .</option>
                                    <option value="customer didn't like the Sample/Demo">customer didn't like the
                                        Sample/Demo.</option>
                                    <option
                                        value="Different locality: Customer is not looking in this locality or part of the town. Some other area of the city.">
                                        Different locality: Customer is not looking in this locality or part of the town.
                                        Some other area of the city.</option>
                                    <option value="Looking for more premium services">Looking for more premium services.
                                    </option>
                                    <option
                                        value="Not picking calls: Cannot say a reason as the customer is not picking calls">
                                        Not picking calls: Cannot say a reason as the customer is not picking calls</option>
                                    <option value="Already Booked: Already booked">Already Booked: Already booked</option>
                                    <option value="No requirment : customer not looking ">No requirment : customer not
                                        looking for </option>
                                    <option value="Not Meet the customer expectations">Not Meet the customer expectations
                                    </option>
                                    <option value="Others: Others">Others</option>
                                    <option value="Lead successfully done.">Lead successfully done.</option>
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
    </div>
@endsection
@section('footer-script')
@include('whatsapp.chat');
    <script>

function handle_whatsapp_msg(id) {
            const elementToUpdate = document.querySelector(`#what_id-${id}`);

            if (elementToUpdate) {
                elementToUpdate.outerHTML =
                    `<i class="fab fa-whatsapp" onclick="handle_whatsapp_msg(${id})" style="font-size: 25px; color: green;"></i>`;
            }
            const form_title = document.querySelector(`#form_title_modal`);
            form_title.innerHTML = `Whatsapp Messages of ${id}`;
            const manageWhatsappChatModal = new bootstrap.Modal(document.getElementById('wa_msg'));
            wamsg(id);
            manageWhatsappChatModal.show();
            const wa_status_url = `{{ route('whatsapp_chat.status_nv_team') }}`;
            const wa_status_data = {
                mobile: id
            };
            fetch(wa_status_url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(wa_status_data),
                })
                .then(response => response.json())
                .then(data => {})
                .catch((error) => {});
        }

        
        function handle_task_status_update(task_id) {
        const url = `{{route('nonvenue.task.status.update')}}/${task_id}`;
        const modal = new bootstrap.Modal('#manageTaskStatusModal');
        task_status_update_form.action = url;
        modal.show();
    }

    function handle_vendor_help_update(msd_id, vendor_name, vendor_cat) {
        const url = `{{route('nonvenue.vendor.help.update')}}/${msd_id}`;
        const modal = new bootstrap.Modal('#manageVendorHelpModal');
        vendor_help_update_form.action = url;
        manageVendorHelpModalTitle = document.getElementById('manageVendorHelpModalTitle');
        manageVendorHelpModalTitle.innerText = `Response to ${vendor_name}--${vendor_cat}`;
        modal.show();
    }

        $(document).ready(function() {
            if ("{{ $lead->lead_status }}" == "Done") {
                const container = document.getElementById('view_lead_card_container');
                const btns = container.querySelectorAll('button');
                for (let item of btns) {
                    item.disabled = true;
                    item.removeAttribute('data-bs-toggle');
                }
            }
        })

        function handle_event_information(url_for_submit, url_for_fetch = null) {
            const manageEventModal = document.getElementById('manageEventModal');
            const modalHeading = manageEventModal.querySelector('.modal-title')

            manage_event_form.action = url_for_submit;
            const modal = new bootstrap.Modal(manageEventModal);
            if (url_for_fetch === null) {
                modalHeading.innerText = "Create Event";
                const inps = manageEventModal.querySelectorAll("input:not([type='hidden'])");
                for (let inp of inps) {
                    inp.value = null;
                }
                modal.show();
            } else {
                fetch(url_for_fetch).then(response => response.json()).then(data => {
                    if (data.success == true) {
                        modalHeading.innerText = "Edit Event";
                        manageEventModal.querySelector('#nv_lead_event_name_inp').value = data.event.event_name;
                        manageEventModal.querySelector('#nv_lead_event_date_inp').value = data.event.event_date;
                        manageEventModal.querySelector('#nv_lead_venue_name').value = data.event.venue_name;
                        manageEventModal.querySelector('#nv_lead_pax_inp').value = data.event.pax;

                        const optionToSelect = manageEventModal.querySelector(
                            `option[value="${data.event.event_slot}"]`);
                        if (optionToSelect) {
                            optionToSelect.selected = true;
                        } else {
                            console.warn('Option with value', data.event.event_slot, 'not found.');
                        }
                        modal.show();
                    } else {
                        toastr[data.alert_type](data.message)
                    }
                })
            }
        }

        // function handle_lead_forward($vendor_category_id, category_name){
        //     const url = `{{ route('nonvenue.getVendorsByCategory') }}/${$vendor_category_id}`;
        //     const forwardLeadModal = document.getElementById('forwardLeadModal');
        //     const modalHeading = forwardLeadModal.querySelector('.modal-title');
        //     const modalBody = forwardLeadModal.querySelector('.modal-body')
        //     modalHeading.innerHTML = `Forward Lead to <span style="color: var(--wb-renosand);">${category_name} Vendors</span>`;
        //     modalBody.innerHTML = "";
        //     fetch(url).then(response => response.json()).then(data => {
        //         if(data.success === true){
        //             // const default_vendors = data.default_vendors;

        //             // const div = document.createElement('div');
        //             // div.innerHTML += `<h5 class="d-inline-block mr-3">Default Selected Group:</h5>`;
        //             // const flex_div = document.createElement('div');
        //             // flex_div.classList = "d-flex ml-3 my-1 mb-3";
        //             // flex_div.style = "column-gap: 3rem;";
        //             // for(let i = 0; i < default_vendors.length; i++){
        //             //     let elem = `<div class="form-check">
    //             //             <input class="form-check-input checkbox_for_vendors" id="forward_vendors_id_checkbox${default_vendors[i].id}" type="checkbox" name="forward_vendors_id[]" value="${default_vendors[i].id}" checked>
    //             //             <label class="form-check-label" for="forward_vendors_id_checkbox${default_vendors[i].id}">${default_vendors[i].name} (${default_vendors[i].business_name})</label>
    //             //         </div>`;
        //             //     flex_div.innerHTML += elem;
        //             //     div.append(flex_div);
        //             // }
        //             // modalBody.appendChild(div);

        //             //Vendors devide into groups
        //             let group = ["A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z"];
        //             const vendors = data.vendors;
        //             let checkbox_container = null;
        //             let div2 = null;
        // 			let group_count = 0;
        //             for (let i = 0; i < vendors.length; i++) {
        //                 if(i%4===0 || i===0){
        //                     const demo_div = document.createElement('div');
        //                     demo_div.innerHTML = `<h5 class="d-inline-block mr-3">Group: <span style="color: var(--wb-dark-red);">${group[group_count]}</span></h5>
    //                     <input type="checkbox" onclick="handle_group_selection(this)" style="width: 1.2rem; height: 1.2rem;">`;
        //                     div2 = demo_div;
        //                     group_count++;

        //                     const flex_div = document.createElement('div');
        //                     flex_div.classList = "d-flex ml-3 my-1 mb-3";
        //                     flex_div.style = "column-gap: 3rem;";
        //                     checkbox_container = flex_div;
        //                 }

        //                 let elem = `<div class="form-check">
    //                     <input class="form-check-input checkbox_for_vendors" id="forward_vendors_id_checkbox${vendors[i].id}" type="checkbox" name="forward_vendors_id[]" value="${vendors[i].id}">
    //                     <label class="form-check-label" for="forward_vendors_id_checkbox${vendors[i].id}">${vendors[i].name} (${vendors[i].business_name})</label>
    //                 </div>`;
        //                 checkbox_container.innerHTML += elem;
        //                 div2.append(checkbox_container);
        //                 modalBody.appendChild(div2);
        //             }

        //             // const custom_arr = [];
        //             // for(let i = 0; i < vendors.length; i++){
        //             //     custom_arr.push(vendors[i].id);
        //             //     let div = document.createElement('div');
        //             //     div.classList = "col-sm-4 mb-3";
        //             //     if(group_count%2 === 0 || group_count === 0){
        //             //         row.innerHTML += `<div class="col-sm-12 d-flex align-items-center mb-2" style="column-gap: 5px;">
    //             //             <h5 class="mb-0 border-bottom border-secondary">Group <span style="color: var(--wb-dark-red);">${group[group_count]}</span></h5>
    //             //             <div class="form-check d-flex align-items-center" style="column-gap: 10px;">
    //             //                 <input data-id="${custom_arr.toString()}" class="form-check-input position-static" id="select_all_rms" type="checkbox" onclick="handle_select_all(this, '.checkbox_for_vendors')" style="height: 1.5rem; width: 1.5rem;">
    //             //             </div>
    //             //         </div>`;
        //             //     }
        //             //     let elem = `<div class="col-sm-4 mb-3">
    //             //         <div class="form-check d-flex align-items-center">
    //             //             <input class="form-check-input checkbox_for_vendors" id="forward_vendors_id_checkbox${vendors[i].id}" type="checkbox" name="forward_vendors_id[]" value="${vendors[i].id}" ${vendors[i].id == 58 || vendors[i].id == 80 ? 'checked': ''}>
    //             //             <label class="form-check-label" for="forward_vendors_id_checkbox${vendors[i].id}">${vendors[i].name} (${vendors[i].business_name})</label>
    //             //         </div>
    //             //     </div>`;
        //             //     row.innerHTML += elem;
        //             //     group_count++;
        //             // }
        //             const modal = new bootstrap.Modal(forwardLeadModal);
        //             modal.show();
        //         }else{
        //             toastr[data.alert_type](data.message);
        //         }
        //     })
        // }
        function handle_lead_forward(vendor_category_id, category_name) {
            const url = `{{ route('nonvenue.getVendorsByCategory') }}/${vendor_category_id}`;
            const forwardLeadModal = document.getElementById('forwardLeadModal');
            const modalHeading = forwardLeadModal.querySelector('.modal-title');
            const modalBody = forwardLeadModal.querySelector('.modal-body');
            const nvrm_msg_id = document.getElementById('nvrm_msg_id');
            modalHeading.innerHTML =
                `Forward Lead to <span style="color: var(--wb-renosand);">${category_name} Vendors</span>`;
            modalBody.innerHTML = "";
            nvrm_msg_id.value = vendor_category_id;
            fetch(url).then(response => response.json()).then(data => {
                if (data.success === true) {
                    const vendors = data.vendors;
                    const groupedVendors = vendors.reduce((groups, vendor) => {
                        const groupName = vendor.group_name || 'No Group'; // Use 'No Group' as a fallback
                        if (!groups[groupName]) {
                            groups[groupName] = [];
                        }
                        groups[groupName].push(vendor);
                        return groups;
                    }, {});

                    Object.keys(groupedVendors).forEach((groupName, index) => {
                        const groupDiv = document.createElement('div');
                        groupDiv.innerHTML =
                            `<h5 class="d-inline-block mr-3">Group: <span style="color: var(--wb-dark-red);">${groupName}</span></h5>
                    <input type="checkbox" onclick="handle_group_selection(this, '${groupName}')" style="width: 1.2rem; height: 1.2rem;">`;

                        const vendorContainer = document.createElement('div');
                        vendorContainer.classList.add('row', 'ml-3', 'my-1', 'mb-3');
                        vendorContainer.style.columnGap = '';

                        groupedVendors[groupName].forEach(vendor => {
                            let vendorElem = document.createElement('div');
                            vendorElem.classList.add('form-check', 'col-3', 'mb-4');
                            vendorElem.innerHTML =
                                `<input class="form-check-input checkbox_for_vendors" id="forward_vendors_id_checkbox${vendor.id}" type="checkbox" name="forward_vendors_id[]" value="${vendor.id}">
                        <label class="form-check-label" for="forward_vendors_id_checkbox${vendor.id}">${vendor.name} (${vendor.business_name})</label>`;
                            vendorContainer.appendChild(vendorElem);
                        });

                        groupDiv.appendChild(vendorContainer);
                        modalBody.appendChild(groupDiv);
                    });

                    const modal = new bootstrap.Modal(forwardLeadModal);
                    modal.show();
                } else {
                    toastr[data.alert_type](data.message);
                }
            });
        }

        function handle_group_selection(checkbox, groupName) {
            const groupDiv = checkbox.closest('div');
            const checkboxes = groupDiv.querySelectorAll('.checkbox_for_vendors');
            checkboxes.forEach(cb => cb.checked = checkbox.checked);
        }


        function handle_group_selection(elem) {
            const parent_container = elem.parentElement;
            const checkbox_for_vendors = parent_container.querySelectorAll('.checkbox_for_vendors');
            for (let item of checkbox_for_vendors) {
                if (elem.checked) {
                    item.checked = true;
                } else {
                    item.checked = false;
                }
            }
        }
    </script>
@endsection
