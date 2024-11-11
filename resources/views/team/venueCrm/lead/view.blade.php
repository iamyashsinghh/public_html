@extends('team.layouts.app')
@php
    $page_title = $lead->name ?: 'N/A';
    $page_title .= " | $lead->mobile | View Lead | Venue CRM";
@endphp
@section('title', $page_title)
@section('header-css')
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
@endsection
@section('main')
    @php
        use App\Models\Availability;
        $food_type = ['Lunch', 'Dinner'];
        $current_date = date('Y-m-d');
        $auth_user = Auth::guard('team')->user();
        $active_task_count = 0;
        $active_visit_count = 0;
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
                    @if ($auth_user->role_id == 4)
                        <button class="btn btn-xs text-light px-2 m-1" style="background-color: var(--wb-renosand)"
                            data-bs-toggle="modal" data-bs-target="#manageRmMessageModal"><i class="fa fa-plus"></i> Add RM
                            Message</button>
                    @endif
                    <button class="btn btn-xs text-light px-2 m-1" style="background-color: var(--wb-dark-red)"
                        onclick="handle_event_information(`{{ route('team.event.add.process') }}`)"><i
                            class="fa fa-plus"></i> Add Event</button>
                    <button class="btn btn-xs text-light px-2 m-1" style="background-color: var(--wb-renosand)"
                        data-bs-toggle="modal" data-bs-target="#manageVisitModal"><i class="fa fa-plus"></i> Add
                        Visit</button>
                    <button class="btn btn-xs text-light px-2 m-1" style="background-color: var(--wb-dark-red)"
                        data-bs-toggle="modal" data-bs-target="#manageTaskModal"><i class="fa fa-plus"></i> Add
                        Task</button>
                    <button class="btn btn-xs text-light px-2 m-1" style="background-color: var(--wb-renosand)"
                        onclick="handle_note_information(`{{ route('team.note.manage.process') }}`)"><i
                            class="fa fa-plus"></i> Add Note</button>
                    @if ($auth_user->role_id == 5)
                        <button class="btn btn-xs text-light px-2 m-1" style="background-color: var(--wb-dark-red)"
                            data-bs-toggle="modal" data-bs-target="#manageBookingModal"><i class="fa fa-plus"></i> Add
                            Booking</button>
                    @endif
                    <div class="dropdown d-inline-block">
                        @if ($lead->service_status == 1)
                            <button class="btn btn-success dropdown-toggle btn-xs px-2 m-1" data-bs-toggle="dropdown"><i
                                    class="fa fa-phone"></i> Service Status: Contacted</button>
                        @else
                            <button class="btn btn-danger dropdown-toggle btn-xs px-2 m-1" data-bs-toggle="dropdown"><i
                                    class="fa fa-phone-slash"></i> Service Status: Not Contacted</button>
                        @endif
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item"
                                    href="{{ route('team.lead.serviceStatus.update', $lead->lead_id) }}/1">Contacted</a>
                            </li>
                            <li><a class="dropdown-item"
                                    href="{{ route('team.lead.serviceStatus.update', $lead->lead_id) }}/0">Not Contacted</a>
                            </li>
                        </ul>
                    </div>
                    <div class="dropdown d-inline-block">
                        <a href="javascript:void(0);"
                            class="btn dropdown-toggle text-light btn-xs px-2 mx-1 {{ $lead->lead_status == 'Done' ? 'bg-secondary' : '' }}"
                            data-bs-toggle="dropdown" style="background-color: var(--wb-renosand);"><i
                                class="fa fa-chart-line"></i> Lead:
                            {{ $lead->lead_status != 'Done' ? 'Active' : 'Done' }}</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item {{ $lead->lead_status != 'Done' ? 'disabled' : '' }}"
                                    onclick="return confirm('Are you sure want to active this lead?')"
                                    href="{{ route('team.lead.status.update', $lead->lead_id) }}/Active">Active</a></li>
                            <li><a class="dropdown-item" href="javascript:void(0);"
                                    onclick="handle_lead_status(this)">Done</a></li>
                        </ul>
                    </div>
                    @if ($auth_user->role_id == 4)
                        <button class="btn btn-xs text-light px-2 m-1" style="background-color: yellow;"
                            onclick="handle_forward_lead_btn(this)"><i class="fa fa-paper-plane"></i> Forward to VM</button>
                        <a onclick="nvrm_forword_preloader(this)" class="btn text-light btn-sm buttons-print mx-1"
                            style="background-color: #FFA500"><i class="fa fa-paper-plane"></i>Forward to
                            NVRM</a>
                    @endif
                </div>
                <div class="card-body">
                    <div class="container-fluid">
                        <div class="card mb-5">
                            <div class="card-header text-light" style="background-color: var(--wb-renosand);">
                                <h3 class="card-title">RM Message's</h3>
                                @if ($auth_user->role_id == 4)
                                    <button class="btn p-0 text-light float-right" title="Add RM Message."
                                        data-bs-toggle="modal" data-bs-target="#manageRmMessageModal"><i
                                            class="fa fa-plus"></i></button>
                                @endif
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

                                        <tbody>
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
                                                        @if ($auth_user->role_id == 4)
                                                            <td>
                                                                <button class="btn btn-sm btn-primary"
                                                                    onclick="handleEditRmMessage( '{{ $list->id }}', '{{ $list->title }}', '{{ $list->message }}' )"><i
                                                                        class="fa fa-edit"></i> Edit</button>
                                                                <form
                                                                    action="{{ route('team.rm_message.delete') }}/{{ $list->id }}"
                                                                    method="POST" style="display:inline-block;">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                                        onclick="return confirm('Are you sure you want to delete this message?')"><i
                                                                            class="fa fa-trash"></i></button>
                                                                </form>
                                                            </td>
                                                        @endif
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td class="text-center text-muted" colspan="5">No data available in
                                                        table</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
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
                                        @if ($auth_user->role_id == 4)
                                            <div class="phone_action_btns d-flex"
                                                style="position: absolute; top: -8px; left: 11rem;">
                                                <a href="#" class="d-flex">
                                                    <div> </div>&nbsp;&nbsp;&nbsp;<i class="fab fa-whatsapp"
                                                        onclick="handle_whatsapp_msg({{ $lead->mobile }})"
                                                        style="font-size: 25px; color: green;"></i>
                                                </a>
                                                <a href="tel:{{ $lead->mobile }}" class="text-primary text-bold mx-1"
                                                    style="font-size: 20px;"><i class="fa fa-phone-alt"></i></a>
                                            </div>
                                        @else
                                            <div class="phone_action_btns"
                                                style="position: absolute; top: -8px; left: 11rem;">
                                                <a target="_blank" href="https://wa.me/{{ $lead->mobile }}"
                                                    class="text-success text-bold mx-1" style="font-size: 20px;"><i
                                                        class="fab fa-whatsapp"></i></a>
                                                <a href="tel:{{ $lead->mobile }}" class="text-primary text-bold mx-1"
                                                    style="font-size: 20px;"><i class="fa fa-phone-alt"></i></a>
                                            </div>
                                        @endif
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
                                    @if ($auth_user->role_id == 4)
                                        <div class="col-sm-6">
                                            <span class="text-bold mx-1" style="color: var(--wb-wood)">Lead Created or
                                                Done By: </span>
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
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card mb-5">
                            <div class="card-header text-light" style="background-color: var(--wb-renosand);">
                                <h3 class="card-title">Event Information</h3>
                                <button href="javascript:void(0);"
                                    onclick="handle_event_information(`{{ route('team.event.add.process') }}`)"
                                    class="btn p-0 text-light float-right" title="Add Event."><i
                                        class="fa fa-plus"></i></button>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="serverTable" class="table mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-nowrap">S.No.</th>
                                                <th class="">Event Name</th>
                                                <th class="text-nowrap">Event Date</th>
                                                <th class="text-nowrap">Pax</th>
                                                <th class="text-center">Budget (in INR)</th>
                                                <th class="">Slot</th>
                                                <th class="">Food Preference</th>
                                                <th class="text-center">Action</th>
                                            </tr>
                                        </thead>

                                        <body>
                                            @php
                                                if ($auth_user->role_id == 4) {
                                                    $events = $lead->get_primary_events();
                                                } else {
                                                    $events = $lead->get_vm_events();
                                                }
                                            @endphp
                                            @if (sizeof($events) > 0)
                                                @foreach ($events as $key => $list)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>{{ $list['event_name'] }}</td>
                                                        <td class="text-nowrap">
                                                            {{ date('d-M-Y', strtotime($list['event_datetime'])) }}</td>
                                                        <td>{{ $list['pax'] }}</td>
                                                        <td class="text-center">₹ {{ number_format($list['budget']) }}
                                                        </td>
                                                        <td>{{ $list['event_slot'] }}</td>
                                                        <td>{{ $list['food_preference'] }}</td>
                                                        <td class="text-center">
                                                            <button
                                                                onclick="handle_event_information(`{{ route('team.event.edit.process', $list->id) }}`, `{{ route('vm_event.fetch', $list->id) }}`)"
                                                                class="btn p-0 text-success" title="Edit Event."><i
                                                                    class="fa fa-edit"></i></button>
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
                        @if ($auth_user->role_id == 5)
                            <div class="card mb-5">
                                <div class="card-header text-light" style="background-color: var(--wb-renosand);">
                                    <h3 class="card-title">Party Areas Availability</h3>
                                    <a href="{{ route('team.availability.manage') }}" target="_blank"
                                        class="btn p-0 text-light float-right" title="Manage Availabilities."><i
                                            class="fa fa-arrow-turn-right"></i></a>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="serverTable" class="table mb-0">
                                            <thead>
                                                <tr>
                                                    <th class="text-nowrap">Event Date</th>
                                                    @foreach ($lead->get_party_areas as $list)
                                                        <th class="text-center">{{ $list->name }}</th>
                                                    @endforeach
                                                </tr>
                                            </thead>

                                            <body>
                                                <tr>
                                                    <td>{{ date('d-M-Y', strtotime($lead->event_datetime)) }}</td>
                                                    @foreach ($lead->get_party_areas as $area)
                                                        <td class="text-center">
                                                            <div class="btn-group" role="group" style="gap: 0.3rem">
                                                                @foreach ($food_type as $food)
                                                                    @php
                                                                        $is_avail = Availability::where([
                                                                            'created_by' => $auth_user->id,
                                                                            'date' => date(
                                                                                'Y-m-d',
                                                                                strtotime($lead->event_datetime),
                                                                            ),
                                                                            'party_area_id' => $area->id,
                                                                            'food_type' => $food,
                                                                        ])->first();
                                                                    @endphp
                                                                    <button
                                                                        title="{{ $food }} | PAX: {{ $is_avail->pax ?? 0 }}"
                                                                        type="button"
                                                                        class="btn btn-{{ $is_avail ? 'danger' : 'success' }}"></button>
                                                                @endforeach
                                                            </div>
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            </body>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div id="task_card_container" class="card mb-5">
                            <div class="card-header text-light" style="background-color: var(--wb-renosand);">
                                <h3 class="card-title">Task Details</h3>
                                <button data-bs-toggle="modal" data-bs-target="#manageTaskModal"
                                    class="btn p-0 text-light float-right" title="Add Task."><i
                                        class="fa fa-plus"></i></button>
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
                                                if ($auth_user->role_id == 4) {
                                                    $tasks = $lead->get_rm_tasks();
                                                } else {
                                                    $tasks = $lead->get_tasks();
                                                }
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
                                                                    class="btn btn-{{ $elem_class }} dropdown-toggle btn-xs"
                                                                    data-bs-toggle="dropdown"
                                                                    style="font-size: 75% !important;">{{ $elem_text }}</button>
                                                                <ul class="dropdown-menu">
                                                                    <li>
                                                                        <a class="dropdown-item"
                                                                            href="javascript:void(0);"
                                                                            onclick="handle_task_status_update({{ $list->id }})">Task
                                                                            Update</a>
                                                                    </li>
                                                                </ul>
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
                                                        <td class="text-nowrap">
                                                            @if ($list->done_datetime == null)
                                                                <a href="{{ route('team.task.delete', $list->id) }}"
                                                                    onclick="return confirm('Are you sure want to delete the task?')"
                                                                    class="text-danger mx-2"><i
                                                                        class="fa fa-trash-alt"></i></a>
                                                            @else
                                                                <button class="btn p-0 text-secondary mx-2" disabled><i
                                                                        class="fa fa-trash-alt"
                                                                        title="Done task cannot be deleted."></i></button>
                                                            @endif
                                                        </td>
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
                        @if ($auth_user->role_id == 5)
                            <div id="visit_card_container" class="card mb-5">
                                <div class="card-header text-light" style="background-color: var(--wb-renosand);">
                                    <h3 class="card-title">Visit Details</h3>
                                    <button data-bs-toggle="modal" data-bs-target="#manageVisitModal"
                                        class="btn p-0 text-light float-right" title="Add Visit."><i
                                            class="fa fa-plus"></i></button>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="serverTable" class="table mb-0">
                                            <thead>
                                                <tr>
                                                    <th class="">S.No.</th>
                                                    <th class="">Visit Schedule Date</th>
                                                    <th class="">Message</th>
                                                    <th class="text-nowrap">Status</th>
                                                    <th class="">Event Name</th>
                                                    @if ($auth_user->role_id == 5)
                                                        <th class="">Event Date</th>
                                                        <th class="">Menu Selected</th>
                                                        <th class="">Party Area</th>
                                                        <th class="">Price Quoted</th>
                                                        <th class="">Done Message</th>
                                                    @endif
                                                    <th class="text-nowrap text-center">Action</th>
                                                </tr>
                                            </thead>

                                            <body>
                                                @php
                                                    $visits = $lead->get_visits();
                                                @endphp
                                                @if (sizeof($visits) > 0)
                                                    @foreach ($visits as $key => $list)
                                                        <tr>
                                                            <td>{{ $key + 1 }}</td>
                                                            <td class="text-nowrap">
                                                                {{ date('d-M-Y H:i a', strtotime($list->visit_schedule_datetime)) }}
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
                                                                @if ($list->done_datetime == null)
                                                                    <button
                                                                        class="btn btn-{{ $elem_class }} dropdown-toggle btn-xs"
                                                                        data-bs-toggle="dropdown"
                                                                        style="font-size: 75% !important;">{{ $elem_text }}</button>
                                                                    <ul class="dropdown-menu">
                                                                        <li>
                                                                            @if ($auth_user->role_id == 4)
                                                                                <a class="dropdown-item"
                                                                                    href="{{ route('team.RmVisit.status.update', $list->id) }}">Visit
                                                                                    Update</a>
                                                                            @else
                                                                                <a class="dropdown-item"
                                                                                    href="javascript:void(0);"
                                                                                    onclick="handle_visit_status_update({{ $list->id }})">Visit
                                                                                    Update</a>
                                                                            @endif
                                                                        </li>
                                                                    </ul>
                                                                    @php
                                                                        $active_visit_count++;
                                                                    @endphp
                                                                @else
                                                                    <span
                                                                        class="badge badge-{{ $elem_class }}">{{ $elem_text }}</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $list->event_name }}</td>
                                                            @if ($auth_user->role_id == 5)
                                                                <td>{{ $list->event_datetime ? date('d-M-Y', strtotime($list->event_datetime)) : 'N/A' }}
                                                                </td>
                                                                <td>{{ $list->menu_selected ?: 'N/A' }}</td>
                                                                <td>{{ $list->party_area ?: 'N/A' }}</td>
                                                                <td class="text-center">
                                                                    {{ $list->price_quoted ? '₹ ' . number_format($list->price_quoted) : 'N/A' }}
                                                                </td>
                                                                <td>
                                                                    <button class="btn"
                                                                        onclick="handle_view_message(`{{ $list->done_message ?: 'N/A' }}`)"><i
                                                                            class="fa fa-comment-dots"
                                                                            style="color: var(--wb-renosand);"></i></button>
                                                                </td>
                                                            @endif
                                                            <td class="text-nowrap text-center">
                                                                @if ($list->done_datetime == null)
                                                                    <a href="{{ route('team.visit.delete', $list->id) }}"
                                                                        onclick="return confirm('Are you sure want to delete the visit?')"
                                                                        class="text-{{ $list->referred_by == null ? 'danger' : 'primary' }} mx-2"><i
                                                                            class="fa fa-trash-alt"></i></a>
                                                                @else
                                                                    <button class="btn p-0 text-secondary mx-2" disabled><i
                                                                            class="fa fa-trash-alt"
                                                                            title="Done visit cannot be deleted."></i></button>
                                                                @endif
                                                                @if ($auth_user->role_id == 4)
                                                                    <button
                                                                        onclick="handle_get_visit_forward_info({{ $list->id }})"
                                                                        class="btn mx-2 p-0 px-2 btn-info"
                                                                        title="Forward info"><i
                                                                            class="fa fa-share-alt mr-1"
                                                                            style="font-size: 15px;"></i>{{ $list->vm_visits_id != null ? sizeof(explode(',', $list->vm_visits_id)) : 0 }}</button>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td class="text-center text-muted"
                                                            colspan="{{ $auth_user->role_id == 4 ? 6 : 11 }}">No data
                                                            available in table</td>
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
                                    <button data-bs-toggle="modal" data-bs-target="#manageBookingModal"
                                        class="btn p-0 text-light float-right" title="Add Booking."><i
                                            class="fa fa-plus"></i></button>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="serverTable" class="table mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Booking Date</th>
                                                    <th>VM Name</th>
                                                    <th>Booking Source</th>
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
                                                </tr>
                                            </thead>

                                            <body>
                                                @php
                                                    $bookings = $lead->get_bookings();
                                                @endphp
                                                @if (sizeof($bookings) > 0)
                                                    @foreach ($bookings as $key => $booking)
                                                        <tr>
                                                            <td class="text-nowrap">
                                                                {{ date('d-M-Y h:i a', strtotime($booking->created_at)) }}
                                                            </td>
                                                            <td class="text-nowrap">{{ $booking->get_vm->name }}</td>
                                                            <td class="text-nowrap">{{ $booking->booking_source }}</td>
                                                            <td class="text-nowrap">{{ $booking->get_event->event_name }}
                                                            </td>
                                                            <td class="text-nowrap">
                                                                {{ date('d-M-Y h:i a', strtotime($booking->get_event->event_datetime)) }}
                                                            </td>
                                                            <td class="text-nowrap">{{ $booking->get_event->event_slot }}
                                                            </td>
                                                            <td class="text-nowrap">
                                                                {{ $booking->get_event->food_preference }}</td>
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
                                                                    <button class="btn btn-danger dropdown-toggle btn-xs"
                                                                        data-bs-toggle="dropdown"
                                                                        style="font-size: 75% !important;">Not
                                                                        Completed</button>
                                                                    <ul class="dropdown-menu">
                                                                        <li>
                                                                            <a class="dropdown-item"
                                                                                href="javascript:void(0);"
                                                                                onclick="manage_add_more_advance_amount({{ $booking->id }})">Add
                                                                                more advance</a>
                                                                        </li>
                                                                    </ul>
                                                                @else
                                                                    <span class="badge badge-success">Received</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td class="text-center text-muted" colspan="14">No data
                                                            available in table</td>
                                                    </tr>
                                                @endif
                                            </body>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if ($auth_user->role_id == 4)
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
                                                    <th class="">Done Message</th>
                                                    <th class="">Created By</th>
                                                    <th class="">Created At</th>
                                                </tr>
                                            </thead>

                                            <body>
                                                @if (sizeof($lead->get_visits_for_rm) > 0)
                                                    @foreach ($lead->get_visits_for_rm as $key => $list)
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
                                                            <td>
                                                                <button class="btn"
                                                                    onclick="handle_view_message(`{{ $list->done_message ?: 'N/A' }}`)"><i
                                                                        class="fa fa-comment-dots"
                                                                        style="color: var(--wb-renosand);"></i></button>
                                                            </td>
                                                            <td>{{ $list->get_created_by->name ?? '' }} -
                                                                {{ $list->get_created_by->venue_name ?? '' }}</td>
                                                            <td>{{ date('d-m-Y h:i a', strtotime($list->created_at)) }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td class="text-center text-muted" colspan="7">No data
                                                            available in
                                                            table</td>
                                                    </tr>
                                                @endif
                                            </body>
                                        </table>
                                    </div>
                                </div>
                            </div>

                    </div>
                    @endif
                    <div class="card mb-5">
                        <div class="card-header text-light" style="background-color: var(--wb-renosand);">
                            <h3 class="card-title">Notes</h3>
                            <button onclick="handle_note_information(`{{ route('team.note.manage.process') }}`)"
                                class="btn p-0 text-light float-right" title="Add Note."><i
                                    class="fa fa-plus"></i></button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="serverTable" class="table mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-nowrap">S.No.</th>
                                            <th class="">Message</th>
                                            <th class="">Created At</th>
                                            <th class="">Action</th>
                                        </tr>
                                    </thead>

                                    <body>
                                        @php
                                            if ($auth_user->role_id == 4) {
                                                $notes = $lead->get_rm_notes();
                                            } else {
                                                $notes = $lead->get_notes();
                                            }
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
                                                    <td>
                                                        <button
                                                            onclick="handle_note_information(`{{ route('team.note.manage.process', $list->id) }}`, `{{ route('team.note.edit', $list->id) }}`)"
                                                            class="btn p-0 text-success mx-2"><i
                                                                class="fa fa-edit"></i></button>
                                                        <a href="{{ route('team.note.delete', $list->id) }}"
                                                            onclick="return confirm('Are you sure want to delete the note?')"
                                                            class="text-danger mx-2"><i class="fa fa-trash-alt"></i></a>
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
                    @if ($auth_user->role_id == 4)
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
                                                            'caller_agent' => $metadata['caller_agent'],
                                                        ];
                                                    }
                                                }
                                            @endphp
                                            @if (count($recording_urls_with_metadata) > 0)
                                                @foreach ($recording_urls_with_metadata as $key => $data)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>
                                                            <audio controls>
                                                                <source src="{{ $data['url'] }}" type="audio/mpeg">
                                                                Your browser does not support the audio element.
                                                            </audio>
                                                        </td>
                                                        <td>
                                                            Datetime:
                                                            {{ date('d-M-Y h:i a', strtotime($data['datetime'])) }}<br>
                                                            Caller Agent: {{ $data['caller_agent'] }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td class="text-center text-muted" colspan="3">No data
                                                        available in table</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
    </div>
    </section>
    <div class="modal fade" id="manageRmMessageModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add RM Message</h4>
                    <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i
                            class="fa fa-times"></i></button>
                </div>
                <form action="{{ route('team.rm_message.manage.process') }}" method="post">
                    <div class="modal-body text-sm">
                        @csrf
                        <div class="form-group">
                            <label for="msg_title_inp">Title</label>
                            <input type="hidden" name="lead_id" value="{{ $lead->lead_id }}">
                            <input type="text" class="form-control" id="msg_title_inp" placeholder="Enter title"
                                name="title">
                        </div>
                        <div class="form-group">
                            <label for="msg_desc_inp">Message</label>
                            <textarea type="text" class="form-control" id="msg_desc_inp" placeholder="Type message" name="message"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm text-light"
                            style="background-color: var(--wb-dark-red);">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="updateRmMessageModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Edit RM Message</h4>
                    <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i
                            class="fa fa-times"></i></button>
                </div>
                <form method="post">
                    @csrf
                    <div class="modal-body text-sm">
                        <div class="form-group">
                            <label for="msg_title_inp">Title</label>
                            <input type="hidden" name="rm_msg_id">
                            <input type="text" class="form-control" id="msg_title_inp" placeholder="Enter title"
                                name="title">
                        </div>
                        <div class="form-group">
                            <label for="msg_desc_inp">Message</label>
                            <textarea type="text" class="form-control" id="msg_desc_inp" placeholder="Type message" name="message"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm text-light"
                            style="background-color: var(--wb-dark-red);">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="forwardLeadModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header align-items-center">
                    <h4 class="modal-title">Forward Lead to VM's</h4>
                    <input class="form-check-input position-static" id="select_all_rms"
                        onclick="handle_select_all(this, '.checkbox_for_vm')" style="height: 1.5rem; width: 1.5rem;"
                        type="checkbox">
                    <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i
                            class="fa fa-times"></i></button>
                </div>
                <form action="{{ route('team.lead.forward') }}" method="post">
                    <div class="modal-body text-sm">
                        @csrf
                        <input type="hidden" name="lead_id" value="{{ $lead->lead_id }}">
                        <div class="row">
                            <div class="col-sm-8">
                                <div class="row">
                                    @foreach ($commonVenue as $list)
                                        <div class="col-sm-6 mb-3">
                                            <div class="form-check d-flex align-items-center">
                                                <input class="form-check-input checkbox_for_vm"
                                                    id="forward_vms_id_checkbox{{ $list->id }}" type="checkbox"
                                                    name="forward_vms_id[]" value="{{ $list->id }}">
                                                <label class="form-check-label"
                                                    for="forward_vms_id_checkbox{{ $list->id }}">{{ $list->name }}
                                                    ({{ $list->venue_name }})
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-sm-4">

                                <div class="row">
                                    @foreach ($uncommonVenue as $list)
                                        <div class="col-sm-12 mb-3">
                                            <div class="form-check d-flex align-items-center">
                                                <input class="form-check-input checkbox_for_vm"
                                                    id="forward_vms_id_checkbox{{ $list->id }}" type="checkbox"
                                                    name="forward_vms_id[]" value="{{ $list->id }}">
                                                <label class="form-check-label"
                                                    for="forward_vms_id_checkbox{{ $list->id }}">{{ $list->name }}
                                                    ({{ $list->venue_name }})
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm bg-secondary" data-bs-dismiss="modal">Cancel</button>
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
                <form action="{{ route('team.lead.status.update', $lead->lead_id) }}" method="post">
                    <div class="modal-body text-sm">
                        @csrf
                        <div class="form-group">
                            <input type="hidden" name="lead_di" value="{{ $lead->lead_id }}">
                            <label for="done_title_select">Done Title <span class="text-danger">*</span></label>
                            <select class="form-control" id="done_title_select" name="done_title" required>
                                <option value="" selected disabled>Select title</option>
                                <option
                                    value="Date not available: The date that customer is looking for is not available at this venue [across relevant party areas]">
                                    Date not available: The date that customer is looking for is not available at this
                                    venue [across relevant party areas]</option>
                                <option value="Budget low. Customer budget is too low. We cannot serve at this venue.">
                                    Budget low. Customer budget is too low. We cannot serve at this venue. </option>
                                <option value=" Venue small: This venue is small for customer pax."> Venue small: This
                                    venue is small for customer pax.</option>
                                <option
                                    value="Venue too big: This venue [acros	s relevant party areas) -- too big. Customer PAX low.">
                                    Venue too big: This venue [acros s relevant party areas) -- too big. Customer PAX
                                    low.</option>
                                <option
                                    value="Food not great: Customer does not like the food. Customer tasted the food and hates it.">
                                    Food not great: Customer does not like the food. Customer tasted the food and hates
                                    it.</option>
                                <option value="Does not like area around: Customer does not like things around the venue.">
                                    Does not like area around: Customer does not like things around the venue.</option>
                                <option
                                    value="Did not like venue: Customer did not like the venue. For one or many of the following: Interior/ Cleanliness/AC Hall etc">
                                    Did not like venue: Customer did not like the venue. For one or many of the
                                    following: Interior/ Cleanliness/AC Hall etc</option>
                                <option
                                    value="Different locality: Customer is not looking in this locality or part of the town. Some other area of the city.">
                                    Different locality: Customer is not looking in this locality or part of the town.
                                    Some other area of the city.</option>
                                <option value="Looking for more premium: Customer is looking for a more upmarket venue">
                                    Looking for more premium: Customer is looking for a more upmarket venue</option>
                                <option
                                    value="Not picking calls: Cannot say a reason as the customer is not picking calls">
                                    Not picking calls: Cannot say a reason as the customer is not picking calls</option>
                                <option value="Already Booked: Already booked">Already Booked: Already booked</option>
                                <option value="No requirment : customer not looking for venue">No requirment : customer
                                    not looking for venue</option>
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
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm text-light"
                            style="background-color: var(--wb-dark-red);">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="manageNoteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Note</h4>
                    <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i
                            class="fa fa-times"></i></button>
                </div>
                <form id="manage_note_form" method="post">
                    <div class="modal-body text-sm">
                        @csrf
                        <div class="form-group">
                            <input type="hidden" name="lead_id" value="{{ $lead->lead_id }}">
                            <label for="note_message_textarea">Message</label>
                            <textarea type="text" class="form-control" id="note_message_textarea" placeholder="Type message"
                                name="note_message" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
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
                <form id="manage_lead_form" action="{{ route('team.lead.edit.process', $lead->lead_id) }}"
                    method="post">
                    <div class="modal-body text-sm">
                        @csrf
                        <div class="row">
                            <div class="col-sm-4 mb-3">
                                <div class="form-group">
                                    <label for="name_inp">Name</label>
                                    <input type="text" class="form-control" id="name_inp" placeholder="Enter name"
                                        name="name" value="{{ $lead->name }}">
                                </div>
                            </div>
                            <div class="col-sm-4 mb-3">
                                <div class="form-group">
                                    <label for="email_inp">Email</label>
                                    <input type="email" class="form-control" id="email_inp" placeholder="Enter email"
                                        name="email" value="{{ $lead->email }}">
                                </div>
                            </div>
                            <div class="col-sm-4 mb-3">
                                <div class="form-group">
                                    <label for="mobile_inp">Mobile No. <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="mobile_inp"
                                        placeholder="Enter mobile no." name="mobile_number" value="{{ $lead->mobile }}"
                                        disabled title="Primary phone number cannot be edit.">
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
                                    <label for="lead_status_select">Lead Status</label>
                                    <select class="form-control" id="lead_status_select" name="lead_status" required>
                                        <option value="Active" {{ $lead->lead_status == 'Active' ? 'selected' : '' }}>
                                            Active</option>
                                        <option value="Hot" {{ $lead->lead_status == 'Hot' ? 'selected' : '' }}>
                                            Hot</option>
                                        <option value="Super Hot"
                                            {{ $lead->lead_status == 'Super Hot' ? 'selected' : '' }}>Super Hot
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer text-sm">
                        <button type="button" class="btn btn-sm bg-secondary" data-bs-dismiss="modal">Cancel</button>
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
                    <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i
                            class="fa fa-times"></i></button>
                </div>
                <form id="manage_event_form" method="post">
                    <div class="modal-body text-sm">
                        @csrf
                        <div class="row">
                            <div class="col-sm-4 mb-3">
                                <div class="form-group">
                                    <input type="hidden" name="lead_id" value="{{ $lead->lead_id }}">
                                    <label for="event_name_inp">Event Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="event_name_inp"
                                        placeholder="Enter event name" name="event_name" required>
                                </div>
                            </div>
                            <div class="col-sm-4 mb-3">
                                <div class="form-group">
                                    <label for="event_date_inp">Event Date <span class="text-danger">*</span></label>
                                    <input type="date" min="{{ date('Y-m-d') }}" class="form-control"
                                        id="event_date_inp" name="event_date" required>
                                </div>
                            </div>
                            <div class="col-sm-4 mb-3">
                                <div class="form-group">
                                    <label for="event_slot_select">Event Slot <span class="text-danger">*</span></label>
                                    <select class="form-control" id="event_slot_select" name="event_slot" required>
                                        <option value="" selected disabled>Select event slot</option>
                                        <option value="Lunch">Lunch</option>
                                        <option value="Dinner">Dinner</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4 mb-3">
                                <div class="form-group">
                                    <label for="food_Preference_select">Food Preference <span
                                            class="text-danger">*</span></label>
                                    <select class="form-control" id="food_Preference_select" name="food_Preference"
                                        required>
                                        <option value="" disabled selected>Select food preference</option>
                                        <option value="Veg">Veg</option>
                                        <option value="Non-Veg">Non-Veg</option>
                                        <option value="Both">Both</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4 mb-3">
                                <div class="form-group">
                                    <label for="number_of_guest_inp">Number of Guest <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="number_of_guest_inp"
                                        placeholder="Enter number of guest" name="number_of_guest" required>
                                </div>
                            </div>
                            <div class="col-sm-4 mb-3">
                                <div class="form-group">
                                    <label for="budget_inp">Budget (in INR)</label>
                                    <input type="text" class="form-control" id="budget_inp"
                                        placeholder="Enter budget" name="budget" onblur="integer_validate(this)">
                                    <span class="text-danger ml-1 position-absolute d-none">Invalid integer
                                        value</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer text-sm">
                        <div class="col">
                            <p>
                                <span class="text-danger text-bold">*</span>
                                Fields are required.
                            </p>
                        </div>
                        <button type="button" class="btn btn-sm bg-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm text-light"
                            style="background-color: var(--wb-dark-red);">Submit</button>
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
                    <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i
                            class="fa fa-times"></i></button>
                </div>
                <form action="{{ route('team.task.add.process') }}" id="manage_task_form" method="post">
                    <div class="modal-body text-sm">
                        @csrf
                        <div class="row">
                            <div class="col-sm-6 mb-3">
                                <div class="form-group">
                                    <input type="hidden" name="lead_id" value="{{ $lead->lead_id }}">
                                    <label for="task_schedule_datetime_inp">Task Schedule Date Time <span
                                            class="text-danger">*</span></label>
                                    <input type="datetime-local" id="task_schedule_datetime_inp"
                                        min="{{ date('Y-m-d H:i') }}" class="form-control"
                                        name="task_schedule_datetime" required>
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
                                    <textarea type="text" class="form-control" id="task_message_textarea" placeholder="Enter task message."
                                        name="task_message"></textarea>
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
                        <button type="submit" class="btn btn-sm text-light"
                            style="background-color: var(--wb-dark-red);">Submit</button>
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
                    <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i
                            class="fa fa-times"></i></button>
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
                            <label for="task_done_message_textarea">Done Message <span
                                    class="text-danger">*</span></label>
                            <textarea type="text" class="form-control" id="task_done_message_textarea" placeholder="Enter done message."
                                name="task_done_message"></textarea>
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
                        <button type="submit" class="btn btn-sm text-light"
                            style="background-color: var(--wb-dark-red);">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="manageVisitModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Visit</h4>
                    <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i
                            class="fa fa-times"></i></button>
                </div>
                <form action="{{ route('team.visit.add.process') }}" method="post">
                    <div class="modal-body text-sm">
                        @csrf
                        <div class="row">
                            <div class="col-sm-6 mb-3">
                                <div class="form-group">
                                    <input type="hidden" name="lead_id" value="{{ $lead->lead_id }}">
                                    <label for="visit_schedule_datetime_inp">Visit Schedule Date Time <span
                                            class="text-danger">*</span></label>
                                    <input type="datetime-local" id="visit_schedule_datetime_inp"
                                        min="{{ date('Y-m-d H:i') }}" class="form-control"
                                        name="visit_schedule_datetime" required>
                                </div>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <div class="form-group">
                                    <label for="visit_event_name_select">Event Name<span
                                            class="text-danger">*</span></label>
                                    <select class="form-control" id="visit_event_name_select" name="visit_event_name"
                                        required>
                                        <option value="" disabled selected>Select Event Name</option>
                                        @foreach ($events as $item)
                                            <option value="{{ $item['event_name'] }}">{{ $item['event_name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12 mb-3">
                                <div class="form-group">
                                    <label for="visit_message_textarea">Message</label>
                                    <textarea type="text" class="form-control" id="visit_message_textarea" placeholder="Enter visit message."
                                        name="visit_message"></textarea>
                                </div>
                            </div>
                            @if ($auth_user->role_id == 4)
                                <div class="col-sm-12 mb-3">
                                    <div class="form-group">
                                        <div class="d-flex" style="column-gap: 15px;">
                                            <label for="visit_venue_select">Select Venue</label>
                                            <input id="select_all_rms"
                                                onclick="handle_select_all(this, '.checkbox_for_vm_to_forward_visit')"
                                                style="height: 1.2rem; width: 1.2rem;" type="checkbox">
                                        </div>
                                        <div class="row">
                                            @foreach ($current_lead_having_vm_members as $list)
                                                <div class="col-sm-4 mb-3">
                                                    <div class="form-check d-flex align-items-center">
                                                        <input class="form-check-input checkbox_for_vm_to_forward_visit"
                                                            id="visit_forward_vms_id_checkbox{{ $list->id }}"
                                                            type="checkbox" name="visit_venue[]"
                                                            value="{{ $list->id }}">
                                                        <label class="form-check-label"
                                                            for="visit_forward_vms_id_checkbox{{ $list->id }}">{{ $list->name }}
                                                            ({{ $list->venue_name }})
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
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
                        <button type="submit" class="btn btn-sm text-light" onclick="btn_preloader(this)"
                            style="background-color: var(--wb-dark-red);">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @if ($auth_user->role_id == 5)
        <div class="modal fade" id="manageVisitStatusModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Update Visit Status</h4>
                        <button type="button" class="btn text-secondary" data-bs-dismiss="modal"
                            aria-label="Close"><i class="fa fa-times"></i></button>
                    </div>
                    <form id="visit_status_update_form" method="post">
                        <div class="modal-body text-sm">
                            @csrf
                            <div class="row">
                                <div class="col-sm-6 mb-3">
                                    <div class="form-group">
                                        <label for="party_area_select">Party Area <span
                                                class="text-danger">*</span></label>
                                        <select class="form-control" id="party_area_select" name="party_area"
                                            required>
                                            <option value="" selected disabled>Select Party Area</option>
                                            @if ($lead->get_party_areas)
                                                @foreach ($lead->get_party_areas as $list)
                                                    <option value="{{ $list->name }}">{{ $list->name }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6 mb-3">
                                    <div class="form-group">
                                        <label for="menu_selected_select">Menu Selected <span
                                                class="text-danger">*</span></label>
                                        <select class="form-control" id="menu_selected_select" name="menu_selected"
                                            required>
                                            <option value="" selected disabled>Select Menu Type</option>
                                            @if ($lead->get_food_preferences)
                                                @foreach ($lead->get_food_preferences as $list)
                                                    <option value="{{ $list->name }}">{{ $list->name }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6 mb-3">
                                    <div class="form-group">
                                        <label for="event_date_inp_for_visit_done">Event Date <span
                                                class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="event_date_inp_for_visit_done"
                                            name="event_date" required>
                                    </div>
                                </div>
                                <div class="col-sm-6 mb-3">
                                    <div class="form-group">
                                        <label for="price_quoted_inp">Price Quoted (In INR) <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="price_quoted_inp"
                                            name="price_quoted" required onblur="integer_validate(this)">
                                        <span class="text-danger ml-1 position-absolute d-none">Invalid integer
                                            value</span>
                                    </div>
                                </div>
                                <div class="col-sm-12 mb-3">
                                    <div class="form-group">
                                        <label for="done_message_textarea">Done Message</label>
                                        <textarea type="text" class="form-control" id="done_message_textarea" placeholder="Enter done message."
                                            name="done_message"></textarea>
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
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Add Booking</h4>
                        <button type="button" class="btn text-secondary" data-bs-dismiss="modal"
                            aria-label="Close"><i class="fa fa-times"></i></button>
                    </div>
                    <form id="manageBookingForm" action="{{ route('team.booking.add_process') }}" method="post">
                        <div class="modal-body text-sm">
                            @csrf
                            <div class="row">
                                <div class="col-sm-4 mb-3">
                                    <div class="form-group">
                                        <label>Predefined Events<span class="text-danger">*</span></label>
                                        <select class="form-control" name="predefined_event" required
                                            onchange="fetch_event_details_for_booking(`{{ route('team.event.manage_ajax') }}`, this.value)">
                                            <option value="" disabled selected>Select the Event</option>
                                            @foreach ($events as $item)
                                                <option value="{{ $item['id'] }}">{{ $item['event_name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <div class="form-group">
                                        <label for="menu_selected_select">Menu Selected <span
                                                class="text-danger">*</span></label>
                                        <select class="form-control" id="menu_selected_select" name="menu_selected"
                                            required>
                                            <option value="" selected disabled>Select Menu Type</option>
                                            @if ($lead->get_food_preferences)
                                                @foreach ($lead->get_food_preferences as $list)
                                                    <option value="{{ $list->name }}">{{ $list->name }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <div class="form-group">
                                        <label for="party_area_select">Party Area <span
                                                class="text-danger">*</span></label>
                                        <select class="form-control" id="party_area_select" name="party_area"
                                            required>
                                            <option value="" selected disabled>Select Party Area</option>
                                            @if ($lead->get_party_areas)
                                                @foreach ($lead->get_party_areas as $list)
                                                    <option value="{{ $list->name }}">{{ $list->name }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <div class="form-group">
                                        <input type="hidden" name="lead_id" value="{{ $lead->lead_id }}">
                                        <label>Event Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control booking_event_info"
                                            placeholder="Enter event name" name="event_name" required disabled>
                                    </div>
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <div class="form-group">
                                        <label>Event Date <span class="text-danger">*</span></label>
                                        <input type="date" min="{{ date('Y-m-d') }}"
                                            class="form-control booking_event_info" name="event_date" required disabled>
                                    </div>
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <div class="form-group">
                                        <label>Event Slot <span class="text-danger">*</span></label>
                                        <select class="form-control booking_event_info" name="event_slot" required
                                            disabled>
                                            <option value="" selected disabled>Select event slot</option>
                                            <option value="Lunch">Lunch</option>
                                            <option value="Dinner">Dinner</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3 mb-3">
                                    <div class="form-group">
                                        <label>Food Preference <span class="text-danger">*</span></label>
                                        <select class="form-control booking_event_info" name="food_Preference" required
                                            disabled>
                                            <option value="" disabled selected>Select food preference</option>
                                            <option value="Veg">Veg</option>
                                            <option value="Non-Veg">Non-Veg</option>
                                            <option value="Both">Both</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3 mb-3">
                                    <div class="form-group">
                                        <label>Number of Guest (PAX) <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control booking_event_info"
                                            placeholder="Enter number of guest" name="number_of_guest"
                                            onblur="calculate_gmv(this)" required disabled>
                                        <span class="text-danger ml-1 position-absolute d-none">Invalid value</span>
                                    </div>
                                </div>
                                <div class="col-sm-3 mb-3">
                                    <div class="form-group">
                                        <label>Price per Plate <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control booking_event_info"
                                            placeholder="Enter the price" name="price_per_plate"
                                            onblur="calculate_gmv(this)" disabled required>
                                        <span class="text-danger ml-1 position-absolute d-none">Invalid value</span>
                                    </div>
                                </div>
                                <div class="col-sm-3 mb-3">
                                    <div class="form-group">
                                        <label>Total Amount (GMV)</label>
                                        <input type="text" class="form-control" placeholder="Enter the amount"
                                            name="total_gmv" readonly>
                                        <span class="text-danger ml-1 position-absolute d-none">Invalid integer
                                            value</span>
                                    </div>
                                </div>
                                <div class="col-sm-12 mb-3">
                                    <label>Advance Amount</label>
                                    <button type="button" class="btn btn-success btn-xs ml-3"
                                        onclick="add_more_advance_amount_field('advance_amount_field_container')"><i
                                            class="fa fa-add"></i></button>
                                    <div id="advance_amount_field_container" class="row">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer text-sm">
                            <div class="col">
                                <p>
                                    <span class="text-danger text-bold">*</span>
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
        <div id="addMoreAdvanceModal" class="modal fade" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Add more advance amount <button type="button"
                                class="btn btn-success btn-xs ml-3"
                                onclick="add_more_advance_amount_field('advance_amount_field_container2')"><i
                                    class="fa fa-add"></i></button></h4>
                        <button type="button" class="btn text-secondary" data-bs-dismiss="modal"
                            aria-label="Close"><i class="fa fa-times"></i></button>
                    </div>
                    <form method="post">
                        <div class="modal-body text-sm">
                            @csrf
                            <div class="col-sm-12 mb-3">
                                <div id="advance_amount_field_container2" class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <label class="text-xs pb-1">Amount</label>
                                            </div>
                                            <input type="text" class="form-control" placeholder="Enter the amount"
                                                name="advance_amount[]" onblur="integer_validate(this)">
                                            <span class="text-danger ml-1 position-absolute d-none">Invalid integer
                                                value</span>
                                        </div>
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
    @endif
    @include('team.venueCrm.lead.visit_forwarded_member_info_modal')
    </div>
@endsection
@section('footer-script')
    @include('whatsapp.chat');
    <script src="{{ asset('plugins/select2/js/select2.min.js') }}"></script>
    <script>
        function manage_add_more_advance_amount(booking_id) {
            const addMoreAdvanceModal = document.getElementById("addMoreAdvanceModal");
            const modal = new bootstrap.Modal(addMoreAdvanceModal)
            addMoreAdvanceModal.querySelector('form').action =
                `{{ route('team.booking.add_more_advance_amount') }}/${booking_id}`;
            modal.show();
        }

        $(document).ready(function() {
            $("#visit_venue_select").select2({
                theme: "classic",
            });

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
                        manageEventModal.querySelector('#event_name_inp').value = data.event.event_name;
                        manageEventModal.querySelector('#event_date_inp').value = data.event.event_date;
                        manageEventModal.querySelector('#number_of_guest_inp').value = data.event.pax;
                        manageEventModal.querySelector('#budget_inp').value = data.event.budget;

                        event_slot_select = manageEventModal.querySelector(
                            `option[value="${data.event.event_slot}"]`);
                        event_slot_select ? event_slot_select.selected = true : '';
                        food_preference_select = manageEventModal.querySelector(
                            `option[value="${data.event.food_preference}"]`);
                        food_preference_select ? food_preference_select.selected = true : "";
                        modal.show();
                    } else {
                        toastr[data.alert_type](data.message)
                    }
                })
            }
        }

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
            const url = `{{ route('team.task.status.update') }}/${task_id}`;
            const modal = new bootstrap.Modal('#manageTaskStatusModal');
            task_status_update_form.action = url;
            modal.show();
        }

        function handle_visit_status_update(visit_id) {
            const url = `{{ route('team.visit.status.update') }}/${visit_id}`;
            const modal = new bootstrap.Modal('#manageVisitStatusModal');
            visit_status_update_form.action = url;
            modal.show();
        }

        function handle_forward_lead_btn(elem) {
            const event_count = Number("{{ $total_events_count }}") || 0;
            const rm_msg = Number("{{ $total_rm_msg }}") || 0;
            const modal = new bootstrap.Modal("#forwardLeadModal");

            if (event_count > 0) {
                if (rm_msg > 0) {
                    modal.show();
                } else {
                    toastr.info("This lead does not have any Rm Message, please create an Rm Msg first.");
                }
            } else {
                toastr.info("This lead does not have any Event or Rm Message, please create an Event & Rm Msg first.");
            }
        }


        function handle_lead_status(elem) {
            const active_task_count = "{{ $active_task_count }}";
            const active_visit_count = "{{ $active_visit_count }}";
            const modal = new bootstrap.Modal("#manageLeadStatusModal");
            let message = "";
            if (active_task_count > 0 || active_visit_count > 0) {
                message = "This lead has an active task or active visit, please complete it first.";
                toastr.info(message);
            } else {
                modal.show();
            }
        }

        var postUrl = "{{ route('team.lead.forwardnvrm') }}";

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
            const wa_status_url = `{{ route('whatsapp_chat.status.bdm') }}`;
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

        function handleEditRmMessage(rm_msg_id, title, message) {
            const updateRmMessageModal = new bootstrap.Modal('#updateRmMessageModal');

            const form = document.querySelector('#updateRmMessageModal form');
            form.action = `{{ route('team.rm_message.update.process') }}/${rm_msg_id}`;

            form.querySelector('input[name="rm_msg_id"]').value = rm_msg_id;
            form.querySelector('input[name="title"]').value = title;
            form.querySelector('textarea[name="message"]').value = message;

            updateRmMessageModal.show();
        }
    </script>
@endsection
