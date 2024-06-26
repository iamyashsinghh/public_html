@extends('admin.layouts.app')
@section('header-css')
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">
@endsection
@section('title', $page_heading . ' | Venue CRM')
@if (!isset($filter_params['dashboard_filters']))
    @section('navbar-right-links')
        <li class="nav-item">
            <a class="nav-link" title="Filters" data-widget="control-sidebar" data-controlsidebar-slide="true" href="#"
                role="button">
                <i class="fas fa-filter"></i>
            </a>
        </li>
    @endsection
@endif
@section('main')
    @php
        $filter_start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
        $filter_end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
    @endphp
    <style>
        .buttons-prints {
            color: fff;
            border: none !important;
            background-color: var(--wb-renosand) !important;
            border-radius: 3px !important;
        }

        .buttons-prints span {
            color: fff !important;
        }
    </style>
    <div class="content-wrapper pb-5">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">{{ $page_heading }}</h1>
                    </div>
                </div>
                @if (!isset($filter_params['dashboard_filters']))
                    <div class="filter-container text-center d-none">
                        <form action="{{ route('admin.visit.list') }}" method="post">
                            @csrf
                            <label for="">Filter by visit schedule date</label>
                            <input type="date" name="visit_schedule_from_date"
                                value="{{ isset($filter_params['visit_schedule_from_date']) ? $filter_params['visit_schedule_from_date'] : '' }}"
                                class="form-control form-control-sm d-inline-block" style="width: unset;" required>
                            <span class="">To:</span>
                            <input type="date" name="visit_schedule_to_date"
                                value="{{ isset($filter_params['visit_schedule_to_date']) ? $filter_params['visit_schedule_to_date'] : '' }}"
                                class="form-control form-control-sm d-inline-block" style="width: unset;">
                            <button type="submit" class="btn text-light btn-sm"
                                style="background-color: var(--wb-dark-red)">Submit</button>
                            <a href="{{ route('admin.visit.list') }}" class="btn btn-secondary btn-sm">Reset</a>
                        </form>
                    </div>
                @endif
            </div>
        </section>
        <section class="content">
            <div class="container-fluid">
                <div class="table-responsive">
                    <table id="serverTable" class="table text-sm">
                        <thead class="sticky_head bg-light" style="position: sticky; top: 0;">
                            <tr>
                                <th class="text-nowrap">Lead ID</th>
                                <th class="text-nowrap">Lead Date</th>
                                <th class="text-nowrap">Source</th>
                                <th class="text-nowrap">VM Name</th>
                                <th class="text-nowrap">Venue Name</th>
                                <th class="text-nowrap">Name</th>
                                <th class="text-nowrap">Mobile</th>
                                <th class="text-nowrap">Lead Status</th>
                                <th class="text-nowrap">Visit Schedule Date</th>
                                <th class="text-nowrap">Visit Status</th>
                                <th class="text-nowrap">Event Name</th>
                                <th class="text-nowrap">Event Date</th>
                                <th class="text-nowrap">Pax</th>
                                <th class="text-nowrap">Visit Created Date</th>
                                <th class="text-nowrap">Visit Done Date</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </section>
        <aside class="control-sidebar control-sidebar-dark" style="display: none;">
            <div class="p-3 control-sidebar-content">
                <h5>Visit Filters</h5>
                <hr class="mb-2">
                <form action="{{ route('admin.visit.list') }}" method="post">
                    @csrf
                    <div class="accordion text-sm" id="accordionExample">

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="btn btn-block btn-sm btn-secondary text-left text-bold text-light"
                                    type="button" data-bs-toggle="collapse" data-bs-target="#collapse111"
                                    aria-expanded="true" aria-controls="collapse111">VMs</button>
                            </h2>
                            <div id="collapse111"
                                class="accordion-collapse collapse {{ isset($filter_params['vm_source']) ? 'show' : '' }}"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body pl-2 pb-4">
                                    <?php foreach ($vm_id_name as $vm) {?>
                                        <div class="custom-control custom-checkbox my-1">
                                            <input id="checkbox_<?php echo $vm->name; ?>" class="custom-control-input"
                                                type="checkbox" name="vm_source[]" value="<?php echo $vm->name; ?>"
                                                <?php echo isset($filter_params['vm_source']) && in_array($vm->name, $filter_params['vm_source']) ? 'checked' : ''; ?>>
                                            <label for="checkbox_<?php echo $vm->name; ?>" class="custom-control-label"><?php echo $vm->name; ?></label>
                                        </div>
                                        <?php } ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="btn btn-block btn-sm btn-secondary text-left text-bold text-light"
                                    type="button" data-bs-toggle="collapse" data-bs-target="#collapse112"
                                    aria-expanded="true" aria-controls="collapse112">Venue's</button>
                            </h2>
                            <div id="collapse112"
                                class="accordion-collapse collapse {{ isset($filter_params['venues_source']) ? 'show' : '' }}"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body pl-2 pb-4">
                                    <?php foreach ($all_venues as $venue) {?>
                                        <div class="custom-control custom-checkbox my-1">
                                            <input id="checkbox_<?php echo $venue->name; ?>" class="custom-control-input"
                                                type="checkbox" name="venues_source[]" value="<?php echo $venue->name; ?>"
                                                <?php echo isset($filter_params['venues_source']) && in_array($venue->name, $filter_params['venues_source']) ? 'checked' : ''; ?>>
                                            <label for="checkbox_<?php echo $venue->name; ?>"
                                                class="custom-control-label"><?php echo $venue->name; ?></label>
                                        </div>
                                        <?php } ?>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="btn btn-block btn-sm btn-secondary text-left text-bold text-light"
                                    type="button" data-bs-toggle="collapse" data-bs-target="#collapse11"
                                    aria-expanded="true" aria-controls="collapse11">Visits Source</button>
                            </h2>
                            <div id="collapse11"
                                class="accordion-collapse collapse {{ isset($filter_params['visits_source']) ? 'show' : '' }}"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body pl-2 pb-4">
                                    <div class="custom-control custom-checkbox my-1">
                                        <input id="checkbox1" class="custom-control-input" type="checkbox"
                                            name="visits_source[]" value="WB|Team"
                                            {{ isset($filter_params['visits_source']) && in_array('WB|Team', $filter_params['visits_source']) ? 'checked' : '' }}>
                                        <label for="checkbox1" class="custom-control-label">WB|Team</label>
                                    </div>
                                    <div class="custom-control custom-checkbox my-1">
                                        <input id="checkbox5" class="custom-control-input" type="checkbox"
                                            name="visits_source[]" value="VM|Reference"
                                            {{ isset($filter_params['visits_source']) && in_array('VM|Reference', $filter_params['visits_source']) ? 'checked' : '' }}>
                                        <label for="checkbox5" class="custom-control-label">VM|Reference</label>
                                    </div>
                                    <div class="custom-control custom-checkbox my-1">
                                        <input id="checkbox2" class="custom-control-input" type="checkbox"
                                            name="visits_source[]" value="WB|Call"
                                            {{ isset($filter_params['visits_source']) && in_array('WB|Call', $filter_params['visits_source']) ? 'checked' : '' }}>
                                        <label for="checkbox2" class="custom-control-label">WB|Call</label>
                                    </div>
                                    <div class="custom-control custom-checkbox my-1">
                                        <input id="checkbox3" class="custom-control-input" type="checkbox"
                                            name="visits_source[]" value="Walk-in"
                                            {{ isset($filter_params['visits_source']) && in_array('Walk-in', $filter_params['visits_source']) ? 'checked' : '' }}>
                                        <label for="checkbox3" class="custom-control-label">Walk-in</label>
                                    </div>
                                    <div class="custom-control custom-checkbox my-1">
                                        <input id="checkbox4" class="custom-control-input" type="checkbox"
                                            name="visits_source[]" value="Other"
                                            {{ isset($filter_params['visits_source']) && in_array('Other', $filter_params['visits_source']) ? 'checked' : '' }}>
                                        <label for="checkbox4" class="custom-control-label">Other</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="btn btn-block btn-sm btn-secondary text-left text-bold text-light"
                                    type="button" data-bs-toggle="collapse" data-bs-target="#collapse1"
                                    aria-expanded="true" aria-controls="collapse1">Visit Status</button>
                            </h2>
                            <div id="collapse1"
                                class="accordion-collapse collapse {{ isset($filter_params['visit_status']) ? 'show' : '' }}"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body pl-2 pb-4">
                                    <div class="custom-control custom-checkbox my-1">
                                        <input class="custom-control-input" type="checkbox"
                                            id="visit_status_upcoming_checkbox" name="visit_status[]" value="Upcoming"
                                            {{ isset($filter_params['visit_status']) && in_array('Upcoming', $filter_params['visit_status']) ? 'checked' : '' }}>
                                        <label for="visit_status_upcoming_checkbox"
                                            class="custom-control-label">Upcoming</label>
                                    </div>
                                    <div class="custom-control custom-checkbox my-1">
                                        <input class="custom-control-input" type="checkbox" id="visit_status_today_checkbox"
                                            name="visit_status[]" value="Today"
                                            {{ isset($filter_params['visit_status']) && in_array('Today', $filter_params['visit_status']) ? 'checked' : '' }}>
                                        <label for="visit_status_today_checkbox" class="custom-control-label">Today</label>
                                    </div>
                                    <div class="custom-control custom-checkbox my-1">
                                        <input class="custom-control-input" type="checkbox"
                                            id="visit_status_overdue_checkbox" name="visit_status[]" value="Overdue"
                                            {{ isset($filter_params['visit_status']) && in_array('Overdue', $filter_params['visit_status']) ? 'checked' : '' }}>
                                        <label for="visit_status_overdue_checkbox"
                                            class="custom-control-label">Overdue</label>
                                    </div>
                                    <div class="custom-control custom-checkbox my-1">
                                        <input class="custom-control-input" type="checkbox" id="visit_status_done_checkbox"
                                            name="visit_status[]" value="Done"
                                            {{ isset($filter_params['visit_status']) && in_array('Done', $filter_params['visit_status']) ? 'checked' : '' }}>
                                        <label for="visit_status_done_checkbox" class="custom-control-label">Done</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="btn btn-block btn-sm btn-secondary text-left text-bold text-light"
                                    type="button" data-bs-toggle="collapse" data-bs-target="#collapse55"
                                    aria-expanded="true" aria-controls="collapse55">Pax</button>
                            </h2>
                            <div id="collapse55"
                                class="accordion-collapse collapse {{ isset($filter_params['pax_min_value']) ? 'show' : '' }}"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body pl-2 pb-4">
                                    <div class="form-group">
                                        <label for="pax_min_value">Min</label>
                                        <input type="text" class="form-control" id="pax_min_value"
                                            name="pax_min_value"
                                            value="{{ isset($filter_params['pax_min_value']) ? $filter_params['pax_min_value'] : '' }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="pax_max_value">Max</label>
                                        <input type="text" class="form-control" id="pax_max_value"
                                            name="pax_max_value"
                                            value="{{ isset($filter_params['pax_max_value']) ? $filter_params['pax_max_value'] : '' }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="btn btn-block btn-sm btn-secondary text-left text-bold text-light"
                                    type="button" data-bs-toggle="collapse" data-bs-target="#collapse2"
                                    aria-expanded="true" aria-controls="collapse2">Visit Created Date</button>
                            </h2>
                            <div id="collapse2"
                                class="accordion-collapse collapse {{ isset($filter_params['visit_created_from_date']) ? 'show' : '' }}"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body pl-2 pb-4">
                                    <div class="form-group">
                                        <label for="visit_created_from_date_inp">From</label>
                                        <input type="date" class="form-control" id="visit_created_from_date_inp"
                                            name="visit_created_from_date"
                                            value="{{ isset($filter_params['visit_created_from_date']) ? $filter_params['visit_created_from_date'] : '' }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="visit_created_to_date_inp">To</label>
                                        <input type="date" class="form-control" id="visit_created_to_date_inp"
                                            name="visit_created_to_date"
                                            value="{{ isset($filter_params['visit_created_to_date']) ? $filter_params['visit_created_to_date'] : '' }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="btn btn-block btn-sm btn-secondary text-left text-bold text-light"
                                    type="button" data-bs-toggle="collapse" data-bs-target="#collapse55894"
                                    aria-expanded="true" aria-controls="collapse55894">Event Date</button>
                            </h2>
                            <div id="collapse55894"
                                class="accordion-collapse collapse {{ isset($filter_params['event_from_date']) ? 'show' : '' }}"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body pl-2 pb-4">
                                    <div class="form-group">
                                        <label for="event_from_date_inp">From</label>
                                        <input type="date" class="form-control" id="event_from_date_inp"
                                            name="event_from_date"
                                            value="{{ isset($filter_params['event_from_date']) ? $filter_params['event_from_date'] : '' }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="event_to_date_inp">To</label>
                                        <input type="date" class="form-control" id="event_to_date_inp"
                                            name="event_to_date"
                                            value="{{ isset($filter_params['event_to_date']) ? $filter_params['event_to_date'] : '' }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="btn btn-block btn-sm btn-secondary text-left text-bold text-light"
                                    type="button" data-bs-toggle="collapse" data-bs-target="#collapse3"
                                    aria-expanded="true" aria-controls="collapse3">visit Done Date</button>
                            </h2>
                            <div id="collapse3"
                                class="accordion-collapse collapse {{ isset($filter_params['visit_done_from_date']) ? 'show' : '' }}"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body pl-2 pb-4">
                                    <div class="form-group">
                                        <label for="visit_done_from_date_inp">From</label>
                                        <input type="date" class="form-control" id="visit_done_from_date_inp"
                                            name="visit_done_from_date"
                                            value="{{ isset($filter_params['visit_done_from_date']) ? $filter_params['visit_done_from_date'] : '' }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="visit_done_to_date_inp">To</label>
                                        <input type="date" class="form-control" id="visit_done_to_date_inp"
                                            name="visit_done_to_date"
                                            value="{{ isset($filter_params['visit_done_to_date']) ? $filter_params['visit_done_to_date'] : '' }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="btn btn-block btn-sm btn-secondary text-left text-bold text-light"
                                    type="button" data-bs-toggle="collapse" data-bs-target="#collapse4"
                                    aria-expanded="true" aria-controls="collapse3">Visit Schedule Date</button>
                            </h2>
                            <div id="collapse4"
                                class="accordion-collapse collapse {{ isset($filter_params['visit_schedule_from_date']) ? 'show' : '' }}"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body pl-2 pb-4">
                                    <div class="form-group">
                                        <label for="visit_schedule_from_date_inp">From</label>
                                        <input type="date" class="form-control" id="visit_schedule_from_date_inp"
                                            name="visit_schedule_from_date"
                                            value="{{ isset($filter_params['visit_schedule_from_date']) ? $filter_params['visit_schedule_from_date'] : '' }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="visit_schedule_to_date">To</label>
                                        <input type="date" class="form-control" id="visit_schedule_to_date_inp"
                                            name="visit_schedule_to_date"
                                            value="{{ isset($filter_params['visit_schedule_to_date']) ? $filter_params['visit_schedule_to_date'] : '' }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="my-5">
                        <button type="submit" class="btn btn-sm text-light btn-block"
                            style="background-color: var(--wb-renosand);">Apply</button>
                        <a href="{{ route('admin.visit.list') }}" type="submit"
                            class="btn btn-sm btn-secondary btn-block">Reset</a>
                    </div>
                </form>
            </div>
        </aside>
    </div>
@endsection
@section('footer-script')
    @php
        $filters = [];
        if (isset($filter_params['visit_status']) && is_array($filter_params['visit_status'])) {
            foreach ($filter_params['visit_status'] as $source) {
                $filters[] = 'visit_status[]=' . urlencode($source);
            }
        }
        if (isset($filter_params['vm_source']) && is_array($filter_params['vm_source'])) {
            foreach ($filter_params['vm_source'] as $source) {
                $filters[] = 'vm_source[]=' . urlencode($source);
            }
        }
        if (isset($filter_params['venues_source']) && is_array($filter_params['venues_source'])) {
            foreach ($filter_params['venues_source'] as $source) {
                $filters[] = 'venues_source[]=' . urlencode($source);
            }
        }
        if (isset($filter_params['visit_created_from_date'])) {
            $filters[] =
                'visit_created_from_date=' .
                $filter_params['visit_created_from_date'] .
                '&visit_created_to_date=' .
                $filter_params['visit_created_to_date'];
        }
        if (isset($filter_params['visits_source']) && is_array($filter_params['visits_source'])) {
            foreach ($filter_params['visits_source'] as $source) {
                $filters[] = 'visits_source[]=' . urlencode($source);
            }
        }
        if (isset($filter_params['visit_done_from_date'])) {
            $filters[] =
                'visit_done_from_date=' .
                $filter_params['visit_done_from_date'] .
                '&visit_done_to_date=' .
                $filter_params['visit_done_to_date'];
        }
        if (isset($filter_params['visit_schedule_from_date'])) {
            $filters[] =
                'visit_schedule_from_date=' .
                $filter_params['visit_schedule_from_date'] .
                '&visit_schedule_to_date=' .
                $filter_params['visit_schedule_to_date'];
        }
        if (isset($filter_params['pax_min_value'])) {
            $filters[] =
                'pax_min_value=' .
                $filter_params['pax_min_value'] .
                '&pax_max_value=' .
                $filter_params['pax_max_value'];
        }
        if (isset($filter_params['event_from_date'])) {
            $filters[] =
                'event_from_date=' .
                $filter_params['event_from_date'] .
                '&event_to_date=' .
                $filter_params['event_to_date'];
        }
        if (isset($filter_params['dashboard_filters'])) {
            $filters[] = 'dashboard_filters=' . $filter_params['dashboard_filters'];
        }

        $filter = implode('&', $filters);
    @endphp

@section('footer-script')
    <script src="//cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="//cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="//cdn.datatables.net/buttons/2.3.6/js/buttons.flash.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="//cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
    <script src="//cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
    <script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
    <script>
        const data_url = `{{ route('admin.visit.list.ajax') }}?{!! $filter !!}`;
        $(document).ready(function() {
            $('#serverTable').DataTable({
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                processing: true,
                dom: '<""lfB>rtip',
                buttons: [{
                    extend: 'excelHtml5',
                    text: '<i class="fas fa-file-excel"></i> Export to Excel',
                    className: 'btn text-light btn-sm buttons-prints mx-1'
                }],
                language: {
                    "search": "_INPUT_",
                    "searchPlaceholder": "Type here to search..",
                    processing: `<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>`,
                },
                serverSide: true,
                lengthMenu: [
                    [10, 25, 50, 100, 200, 500],
                    [10, 25, 50, 100, 200, 500]
                ],
                ajax: {
                    url: data_url,
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    },
                    method: "get",
                    dataSrc: "data",
                },
                columns: [{
                        name: "lead_id",
                        data: "lead_id"
                    },
                    {
                        name: "lead_datetime",
                        data: "lead_datetime"
                    },
                    {
                        name: "source",
                        data: "source"
                    },
                    {
                        name: "vm_name",
                        data: "vm_name"
                    },
                    {
                        name: "venue_name",
                        data: "venue_name"
                    },
                    {
                        name: "name",
                        data: "name"
                    },
                    {
                        name: "mobile",
                        data: "mobile"
                    },
                    {
                        name: "lead_status",
                        data: "lead_status"
                    },
                    {
                        name: "visit_schedule_datetime",
                        data: "visit_schedule_datetime"
                    },
                    {
                        name: "lead_id",
                        data: "lead_id"
                    },
                    {
                        name: "event_name",
                        data: "event_name"
                    },
                    {
                        name: "event_datetime",
                        data: "event_datetime"
                    },
                    {
                        name: "pax",
                        data: "pax"
                    },
                    {
                        name: "visit_created_datetime",
                        data: "visit_created_datetime"
                    },
                    {
                        name: "visit_done_datetime",
                        data: "visit_done_datetime"
                    },
                ],
                order: [
                    [1, 'desc']
                ],
                rowCallback: function(row, data) {
                    row.style.cursor = "pointer";
                    row.setAttribute('onclick', `handle_view_lead(${data.lead_id})`);

                    const td_elements = row.querySelectorAll('td');
                    td_elements[1].innerText = moment(data.lead_datetime).format("DD-MMM-YYYY hh:mm a");
                    td_elements[1].classList.add('text-nowrap');
                    if (data.lead_status == "Done") {
                        td_elements[7].innerHTML = `<span class="badge badge-secondary">Done</span>`;
                    } else {
                        td_elements[7].innerHTML =
                            `<span class="badge badge-success">${data.lead_status}</span>`;
                    }
                    td_elements[8].innerHTML = moment(data.visit_schedule_datetime).format(
                        "DD-MMM-YYYY hh:mm a");

                    const visit_schedule_date = moment(data.visit_schedule_datetime).format(
                        "YYYY-MM-DD");
                    const current_date = moment().format("YYYY-MM-DD");
                    let elem_class, elem_text;
                    if (data.visit_done_datetime != null) {
                        elem_class = "secondary";
                        elem_text = "Done";
                    } else if (visit_schedule_date > current_date) {
                        elem_class = "info";
                        elem_text = "Upcoming";
                    } else if (visit_schedule_date == current_date) {
                        elem_class = "warning";
                        elem_text = "Today";
                    } else if (visit_schedule_date < current_date) {
                        elem_class = "danger";
                        elem_text = "Overdue";
                    }
                    td_elements[9].innerHTML =
                        `<span class="badge badge-${elem_class}">${elem_text}</span>`;
                    td_elements[10].innerText = data.event_name;
                    td_elements[11].innerText = moment(data.event_datetime).format("DD-MMM-YYYY");
                    td_elements[13].innerText = moment(data.visit_created_datetime).format(
                        "DD-MMM-YYYY hh:mm a");
                    td_elements[14].innerText = data.visit_done_datetime ? moment(data
                        .visit_done_datetime).format("DD-MMM-YYYY hh:mm a") : 'N/A';
                }
            });
        });

        function handle_view_lead(forward_id) {
            window.open(`{{ route('admin.lead.view') }}/${forward_id}#visit_card_container`);
        }
    </script>
@endsection
