@extends('bdm.layouts.app')
@php
    $page_title = $lead->name ?: 'N/A';
    $page_title .= " | $lead->mobile | View Lead | Bdm CRM";
    $current_date = date('Y-m-d');
    $auth_user = Auth::guard('bdm')->user();
@endphp
@section('title', $page_title)
@section('header-css')
<link rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
@endsection
@section('main')
@php
    $auth_user = Auth::guard('bdm')->user();
    $active_task_count = 0;
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
                <button class="btn btn-xs text-light px-2 m-1" style="background-color: var(--wb-dark-red)" data-bs-toggle="modal" data-bs-target="#manageTaskModal"><i class="fa fa-plus"></i> Add Task</button>
                <button class="btn btn-xs text-light px-2 m-1" style="background-color: var(--wb-dark-red)" data-bs-toggle="modal" data-bs-target="#manageMeetingModal"><i class="fa fa-plus"></i> Add Meeting</button>
                <button class="btn btn-xs text-light px-2 m-1" style="background-color: var(--wb-renosand)" onclick="handle_note_information(`{{route('team.note.manage.process')}}`)"><i class="fa fa-plus"></i> Add Note</button>
                <div class="dropdown d-inline-block">
                    @if ($lead->service_status == 1)
                        <button class="btn btn-success dropdown-toggle btn-xs px-2 m-1" data-bs-toggle="dropdown"><i class="fa fa-phone"></i> Service Status: Contacted</button>
                    @else
                        <button class="btn btn-danger dropdown-toggle btn-xs px-2 m-1" data-bs-toggle="dropdown"><i class="fa fa-phone-slash"></i> Service Status: Not Contacted</button>
                    @endif
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{route('bdm.lead.serviceStatus.update', $lead->lead_id)}}/1">Contacted</a></li>
                        <li><a class="dropdown-item" href="{{route('bdm.lead.serviceStatus.update', $lead->lead_id)}}/0">Not Contacted</a></li>
                    </ul>
                </div>
                <div class="dropdown d-inline-block">
                    <a href="javascript:void(0);" class="btn dropdown-toggle text-light btn-xs px-2 mx-1 {{$lead->lead_status == 'Done' ? 'bg-secondary' : ''}}" data-bs-toggle="dropdown" style="background-color: var(--wb-renosand);"><i class="fa fa-chart-line"></i> Lead: {{$lead->lead_status != "Done" ? "Active" : "Done"}}</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item {{$lead->lead_status != "Done" ? "disabled" : ""}}" onclick="return confirm('Are you sure want to active this lead?')" href="{{route('bdm.lead.status.update', $lead->lead_id)}}/Active">Active</a></li>
                        <li><a class="dropdown-item" href="javascript:void(0);" onclick="handle_lead_status(this)">Done</a></li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <div class="container-fluid">
                    <div class="card mb-5">
                        <div class="card-header text-light" style="background-color: var(--wb-renosand);">
                            <h3 class="card-title">Lead Information</h3>
                            <button href="javascript:void(0);" class="btn p-0 text-light float-right" title="Edit lead info." data-bs-toggle="modal" data-bs-target="#editLeadModal"><i class="fa fa-edit"></i></button>
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
                                    <span class="text-bold mx-1" style="color: var(--wb-wood)">Business Name: </span>
                                    <span class="mx-1">{{$lead->business_name ?: 'N/A'}}</span>
                                </div>
                                <div class="col-sm-6">
                                    <span class="text-bold mx-1" style="color: var(--wb-wood)">Business Category: </span>
                                    <span class="mx-1">{{$lead->get_lead_cat->name ?: 'N/A'}}</span>
                                </div>
                                <div class="col-sm-6">
                                    <span class="text-bold mx-1" style="color: var(--wb-wood)">Lead Status: </span>
                                    <span class="mx-1 badge badge-{{$lead->lead_status == 'Done' ? 'secondary' : 'success'}}">{{$lead->lead_status}}</span>
                                </div>
                                <div class="col-sm-6">
                                    <span class="text-bold mx-1" style="color: var(--wb-wood)">Done Title: </span>
                                    <span class="mx-1">{{$lead->done_title ?: 'N/A'}}</span>
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
                                    <span class="mx-1">{{$lead->done_message ?: 'N/A'}}</span>
                                </div>
                                <div class="col-sm-6">
                                    <span class="text-bold mx-1" style="color: var(--wb-wood)">Lead Source: </span>
                                    <span class="mx-1">{{$lead->source ?: 'N/A'}}</span>
                                </div>
                                <div class="col-sm-6">
                                    <span class="text-bold mx-1" style="color: var(--wb-wood)">Done Datetime: </span>
                                    <span class="mx-1">{{$lead->done_title ? date('d-M-Y H:i a', strtotime($lead->updated_at)) : 'N/A'}}</span>
                                </div>
                                <div class="col-sm-6">
                                    <span class="text-bold mx-1" style="color: var(--wb-wood)">Lead Created or Done By: </span>
                                    <span class="mx-1">
                                        @php
                                            if($lead->get_created_by){
                                                echo $lead->get_created_by->name." (".$lead->get_created_by->get_role->name.")";
                                            }else{
                                               echo "API Reference";
                                            }
                                        @endphp
                                    </span>
                                </div>
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
                                                $tasks = $lead->get_bdm_tasks;
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
                                                    <a href="{{route('bdm.task.delete', $list->id)}}" onclick="return confirm('Are you sure want to delete the task?')" class="text-danger mx-2"><i class="fa fa-trash-alt"></i></a>
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
                    <div id="meeting_card_container" class="card mb-5">
                        <div class="card-header text-light" style="background-color: var(--wb-renosand);">
                            <h3 class="card-title">Meeting Details</h3>
                            <button data-bs-toggle="modal" data-bs-target="#manageMeetingModal" class="btn p-0 text-light float-right" title="Add Task."><i class="fa fa-plus"></i></button>
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
                                            <th class="text-nowrap">Done With</th>
                                            <th class="text-nowrap">Done Message</th>
                                            <th class="text-nowrap">Done Date</th>
                                            <th class="text-nowrap">Action</th>
                                        </tr>
                                    </thead>
                                    <body>
                                        @php
                                                $meeting = $lead->get_bdm_meetings;
                                        @endphp
                                        @if (sizeof($meeting) > 0)
                                        @foreach ($meeting as $key => $list)
                                        <tr>
                                            <td>{{$key+1}}</td>
                                            <td class="">{{date('d-M-Y h:i a', strtotime($list->meeting_schedule_datetime))}}</td>
                                            <td>{{$list->follow_up}}</td>
                                            <td>
                                                <button class="btn" onclick="handle_view_message(`{{$list->message ?: 'N/A'}}`)"><i class="fa fa-comment-dots" style="color: var(--wb-renosand);"></i></button>
                                            </td>
                                            <td>
                                                @php
                                                $schedule_date = date('Y-m-d', strtotime($list->meeting_schedule_datetime));
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
                                                            <a class="dropdown-item" href="javascript:void(0);" onclick="handle_meeting_status_update({{$list->id}})">Meeting Update</a>
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
                                                    <a href="{{route('bdm.meeting.delete', $list->id)}}" onclick="return confirm('Are you sure want to delete the Meeting?')" class="text-danger mx-2"><i class="fa fa-trash-alt"></i></a>
                                                @else
                                                    <button class="btn p-0 text-secondary mx-2" disabled><i class="fa fa-trash-alt" title="Done Meeting cannot be deleted."></i></button>
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
                </div>
            </div>
        </div>
    </section>


    <div class="modal fade" id="manageLeadStatusModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Lead Done</h4>
                    <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                </div>
                <form action="{{route('bdm.lead.status.update', $lead->lead_id)}}" method="post">
                    <div class="modal-body text-sm">
                        @csrf
                        <div class="form-group">
                            <input type="hidden" name="lead_di" value="{{$lead->lead_id}}">
                            <label for="done_title_select">Done Title <span class="text-danger">*</span></label>
                            <select class="form-control" id="done_title_select" name="done_title" required>
                                <option value="" selected disabled>Select title</option>
                                <option value="Date not available: The date that customer is looking for is not available at this venue [across relevant party areas]">Date not available: The date that customer is looking for is not available at this venue [across relevant party areas]</option>
                                <option value="Budget low. Customer budget is too low. We cannot serve at this venue.">Budget low. Customer budget is too low. We cannot serve at this venue. </option>
                                <option value=" Venue small: This venue is small for customer pax."> Venue small: This venue is small for customer pax.</option>
                                <option value="Venue too big: This venue [acros	s relevant party areas) -- too big. Customer PAX low.">Venue too big: This venue [acros	s relevant party areas) -- too big. Customer PAX low.</option>
                                <option value="Food not great: Customer does not like the food. Customer tasted the food and hates it.">Food not great: Customer does not like the food. Customer tasted the food and hates it.</option>
                                <option value="Does not like area around: Customer does not like things around the venue.">Does not like area around: Customer does not like things around the venue.</option>
                                <option value="Did not like venue: Customer did not like the venue. For one or many of the following: Interior/ Cleanliness/AC Hall etc">Did not like venue: Customer did not like the venue. For one or many of the following: Interior/ Cleanliness/AC Hall etc</option>
                                <option value="Different locality: Customer is not looking in this locality or part of the town. Some other area of the city.">Different locality: Customer is not looking in this locality or part of the town. Some other area of the city.</option>
                                <option value="Looking for more premium: Customer is looking for a more upmarket venue">Looking for more premium: Customer is looking for a more upmarket venue</option>
                                <option value="Not picking calls: Cannot say a reason as the customer is not picking calls">Not picking calls: Cannot say a reason as the customer is not picking calls</option>
                                <option value="Already Booked: Already booked">Already Booked: Already booked</option>
                                <option value="No requirment : customer not looking for venue">No requirment : customer not looking for venue</option>
                                <option value="Others: Others">Others: Others</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="done_message_textarea">Done Message</label>
                            <textarea type="text" class="form-control" id="done_message_textarea" placeholder="Type message" name="done_message"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
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
                    <h4 class="modal-title">Edit Lead</h4>
                    <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                </div>
                <form id="manage_lead_form" action="{{route('bdm.lead.edit.process', $lead->lead_id)}}" method="post">
                    <div class="modal-body text-sm">
                        @csrf
                        <div class="row">
                            <div class="col-sm-4 mb-3">
                                <div class="form-group">
                                    <label for="name_inp">Name</label>
                                    <input type="text" class="form-control" id="name_inp" placeholder="Enter name" name="name" value="{{$lead->name}}">
                                </div>
                            </div>
                            <div class="col-sm-4 mb-3">
                                <div class="form-group">
                                    <label for="email_inp">Email</label>
                                    <input type="email" class="form-control" id="email_inp" placeholder="Enter email" name="email" value="{{$lead->email}}">
                                </div>
                            </div>
                            <div class="col-sm-4 mb-3">
                                <div class="form-group">
                                    <label for="mobile_inp">Mobile No. <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="mobile_inp" placeholder="Enter mobile no." name="mobile_number" value="{{$lead->mobile}}" disabled title="Primary phone number cannot be edit.">
                                </div>
                            </div>
                            <div class="col-sm-4 mb-">
                                <div class="form-group">
                                    <label for="alt_mobile_inp">Alternate Mobile No.</label>
                                    <input type="text" class="form-control" id="alt_mobile_inp" placeholder="Enter alternate mobile no." name="alternate_mobile_number" value="{{$lead->alternate_mobile}}">
                                </div>
                            </div>
                            <div class="col-sm-4 mb-3">
                                <div class="form-group">
                                    <label for="city_inp">City</label>
                                    <input type="text" class="form-control" id="city_inp" placeholder="Enter City." name="city" value="{{$lead->city}}">
                                </div>
                            </div>
                            <div class="col-sm-4 mb-3">
                                <div class="form-group">
                                    <label for="lead_status_select">Lead Status</label>
                                    <select class="form-control" id="lead_status_select" name="lead_status" required>
                                        <option value="Active" {{$lead->lead_status == "Active" ? 'selected' : ''}}>Active</option>
                                        <option value="Hot" {{$lead->lead_status == "Hot" ? 'selected' : ''}}>Hot</option>
                                        <option value="Super Hot" {{$lead->lead_status == "Super Hot" ? 'selected' : ''}}>Super Hot</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4 mb-3">
                                <div class="form-group">
                                    <label for="business_cat_inp">Business Category</label>
                                    <select class="form-control" id="business_cat_inp" name="business_cat">
                                    <option value="" disabled selected>Select Category</option>
                                    @foreach ($vendor_categories as $list)
                                        <option value="{{ $list->id }}" {{ $lead->get_lead_cat->id == $list->id ? 'selected' : '' }}>{{ $list->name }}</option>
                                    @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4 mb-3">
                                <div class="form-group">
                                    <label for="business_name_inp">Business Name</label>
                                    <input type="text" class="form-control" id="business_name_inp" placeholder="Enter Business Name." name="business_name" value="{{$lead->business_name}}">
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
    <div class="modal fade" id="manageTaskModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Task</h4>
                    <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                </div>
                <form action="{{route('bdm.task.add.process')}}" id="manage_task_form" method="post">
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
    <div class="modal fade" id="manageMeetingModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Meeting</h4>
                    <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                </div>
                <form action="{{route('bdm.meeting.add.process')}}" id="manage_meeting_form" method="post">
                    <div class="modal-body text-sm">
                        @csrf
                        <div class="row">
                            <div class="col-sm-6 mb-3">
                                <div class="form-group">
                                    <input type="hidden" name="lead_id" value="{{$lead->lead_id}}">
                                    <label for="meeting_schedule_datetime_inp">Meeting Schedule Date Time <span class="text-danger">*</span></label>
                                    <input type="datetime-local" id="meeting_schedule_datetime_inp" min="{{date('Y-m-d H:i')}}" class="form-control" name="meeting_schedule_datetime" required>
                                </div>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <div class="form-group">
                                    <label for="meeting_follow_up_select">Meeting Follow Up</label>
                                    <select class="form-control" id="meeting_follow_up_select" name="meeting_follow_up">
                                        <option value="Call">Call</option>
                                        <option value="SMS">SMS</option>
                                        <option value="Mail">Mail</option>
                                        <option value="WhatsApp">WhatsApp</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12 mb-3">
                                <div class="form-group">
                                    <label for="meeting_message_textarea">Message</label>
                                    <textarea type="text" class="form-control" id="meeting_message_textarea" placeholder="Enter meeting message." name="meeting_message"></textarea>
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
    <div class="modal fade" id="manageMeetingStatusModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Update Meeting Status</h4>
                    <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                </div>
                <form id="meeting_status_update_form" method="post">
                    <div class="modal-body text-sm">
                        <div class="form-group mb-3">
                            @csrf
                            <label for="meeting_done_with_select">Meeting Done With <span class="text-danger">*</span></label>
                            <select class="form-control" id="meeting_done_with_select" name="meeting_done_with" required>
                                <option value="Call">Call</option>
                                <option value="SMS">SMS</option>
                                <option value="Mail">Mail</option>
                                <option value="WhatsApp">WhatsApp</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="task_done_message_textarea">Done Message <span class="text-danger">*</span></label>
                            <textarea type="text" class="form-control" id="meeting_done_message_textarea" placeholder="Enter done message." name="meeting_done_message"></textarea>
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
</div>
@endsection
@section('footer-script')
<script src="{{asset('plugins/select2/js/select2.min.js')}}"></script>
<script>
    function handle_note_information(url_for_submit, url_for_fetch = null) {
        const manageNoteModal = document.getElementById('manageNoteModal');
        const modalHeading = manageNoteModal.querySelector('.modal-title')

        manage_note_form.action = url_for_submit;
        const modal = new bootstrap.Modal(manageNoteModal);
        if (url_for_fetch === null) {
            modalHeading.innerText = "Add Note";
            const inps = manageNoteModal.querySelectorAll("input:not([type='hidden'])");
            for (let inp of inps) {
                inp.value = null;
            }
            modal.show();
        } else {
            fetch(url_for_fetch).then(response => response.json()).then(data => {
                if (data.success == true) {
                    modalHeading.innerText = "Edit Note";
                    manageNoteModal.querySelector('#note_message_textarea').value = data.note.message;
                    modal.show();
                } else {
                    toastr[data.alert_type](data.message);
                }
            })
        }
    }

    function handle_task_status_update(task_id) {
        const url = `{{route('bdm.task.status.update')}}/${task_id}`;
        const modal = new bootstrap.Modal('#manageTaskStatusModal');
        task_status_update_form.action = url;
        modal.show();
    }
    function handle_meeting_status_update(meeting_id) {
        const url = `{{route('bdm.meeting.status.update')}}/${meeting_id}`;
        const modal = new bootstrap.Modal('#manageMeetingStatusModal');
        meeting_status_update_form.action = url;
        modal.show();
    }

    function handle_lead_status(elem){
        const modal = new bootstrap.Modal("#manageLeadStatusModal");
            modal.show();
    }



</script>
@endsection
