@extends('team.layouts.app')
@section('header-css')
<link rel="stylesheet" href="//cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
@endsection
@section('title', $page_heading." | Venue CRM")
@if (!isset($filter_params['dashboard_filters']))
    @section('navbar-right-links')
    <li class="nav-item">
        <a class="nav-link" title="Filters" data-widget="control-sidebar" data-controlsidebar-slide="true" href="#" role="button">
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
<div class="content-wrapper pb-5">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{$page_heading}}</h1>
                </div>
            </div>
            @if (!isset($filter_params['dashboard_filters']))
                <div class="filter-container text-center d-none">
                    <form action="{{route('team.task.list')}}" method="post">
                        @csrf
                        <label for="">Filter by task schedule date</label>
                        <input type="date" name="task_schedule_from_date" value="{{isset($filter_params['task_schedule_from_date']) ? $filter_params['task_schedule_from_date'] : ''}}" class="form-control form-control-sm d-inline-block" style="width: unset;" required>
                        <span class="">To:</span>
                        <input type="date" name="task_schedule_to_date" value="{{isset($filter_params['task_schedule_to_date']) ? $filter_params['task_schedule_to_date'] : ''}}" class="form-control form-control-sm d-inline-block" style="width: unset;">
                        <button type="submit" class="btn text-light btn-sm" style="background-color: var(--wb-dark-red)">Submit</button>
                        <a href="{{route('team.task.list')}}" class="btn btn-secondary btn-sm">Reset</a>
                    </form>
                </div>
            @endif
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="table-responsive">
                <table id="serverTable" class="table text-sm">
                    <thead>
                        <tr>
                            <th class="text-nowrap">Lead ID</th>
                            <th class="text-nowrap">Lead Date</th>
                            <th class="text-nowrap">Name</th>
                            <th class="text-nowrap">Mobile</th>
                            <th class="text-nowrap">Task Schedule Date</th>
                            <th class="text-nowrap">Task Status</th>
                            <th class="text-nowrap">Event Date</th>
                            <th class="text-nowrap">Task Created Date</th>
                            <th class="text-nowrap">Task Done Date</th>
                        </tr>
                    </thead>
                </table>

            </div>
        </div>
    </section>
    <aside class="control-sidebar control-sidebar-dark" style="display: none;">
        {{-- filter sidebar for task list page --}}
        <div class="p-3 control-sidebar-content">
            <h5>Task Filters</h5>
            <hr class="mb-2">
            <form action="{{route('team.task.list')}}" method="post">
                @csrf
                <div class="accordion text-sm" id="accordionExample">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="btn btn-block btn-sm btn-secondary text-left text-bold text-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="true" aria-controls="collapse1">Task Status</button>
                        </h2>
                        <div id="collapse1" class="accordion-collapse collapse {{isset($filter_params['task_status']) ? 'show' : ''}}" data-bs-parent="#accordionExample">
                            <div class="accordion-body pl-2 pb-4">
                                <div class="custom-control custom-checkbox my-1">
                                    <input class="custom-control-input" type="checkbox" id="task_status_upcoming_checkbox" name="task_status[]" value="Upcoming" {{isset($filter_params['task_status']) && in_array('Upcoming', $filter_params['task_status']) ? 'checked' : ''}}>
                                    <label for="task_status_upcoming_checkbox" class="custom-control-label">Upcoming</label>
                                </div>
                                <div class="custom-control custom-checkbox my-1">
                                    <input class="custom-control-input" type="checkbox" id="task_status_today_checkbox" name="task_status[]" value="Today" {{isset($filter_params['task_status']) && in_array('Today', $filter_params['task_status']) ? 'checked' : ''}}>
                                    <label for="task_status_today_checkbox" class="custom-control-label">Today</label>
                                </div>
                                <div class="custom-control custom-checkbox my-1">
                                    <input class="custom-control-input" type="checkbox" id="task_status_overdue_checkbox" name="task_status[]" value="Overdue" {{isset($filter_params['task_status']) && in_array('Overdue', $filter_params['task_status']) ? 'checked' : ''}}>
                                    <label for="task_status_overdue_checkbox" class="custom-control-label">Overdue</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="btn btn-block btn-sm btn-secondary text-left text-bold text-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2" aria-expanded="true" aria-controls="collapse2">Task Created Date</button>
                        </h2>
                        <div id="collapse2" class="accordion-collapse collapse {{isset($filter_params['task_created_from_date']) ? 'show' : ''}}" data-bs-parent="#accordionExample">
                            <div class="accordion-body pl-2 pb-4">
                                <div class="form-group">
                                    <label for="task_created_from_date_inp">From</label>
                                    <input type="date" class="form-control" id="task_created_from_date_inp" name="task_created_from_date" value="{{isset($filter_params['task_created_from_date']) ? $filter_params['task_created_from_date'] : ''}}">
                                </div>
                                <div class="form-group">
                                    <label for="task_created_to_date_inp">To</label>
                                    <input type="date" class="form-control" id="task_created_to_date_inp" name="task_created_to_date" value="{{isset($filter_params['task_created_to_date']) ? $filter_params['task_created_to_date'] : ''}}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="btn btn-block btn-sm btn-secondary text-left text-bold text-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4" aria-expanded="true" aria-controls="collapse3">Task Schedule Date</button>
                        </h2>
                        <div id="collapse4" class="accordion-collapse collapse {{isset($filter_params['task_schedule_from_date']) ? 'show' : ''}}" data-bs-parent="#accordionExample">
                            <div class="accordion-body pl-2 pb-4">
                                <div class="form-group">
                                    <label for="task_schedule_from_date_inp">From</label>
                                    <input type="date" class="form-control" id="task_schedule_from_date_inp" name="task_schedule_from_date" value="{{isset($filter_params['task_schedule_from_date']) ? $filter_params['task_schedule_from_date'] : ''}}">
                                </div>
                                <div class="form-group">
                                    <label for="task_schedule_to_date">To</label>
                                    <input type="date" class="form-control" id="task_schedule_to_date_inp" name="task_schedule_to_date" value="{{isset($filter_params['task_schedule_to_date']) ? $filter_params['task_schedule_to_date'] : ''}}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="my-5">
                    <button type="submit" class="btn btn-sm text-light btn-block" style="background-color: var(--wb-renosand);">Apply</button>
                    <a href="{{route('team.task.list')}}" type="submit" class="btn btn-sm btn-secondary btn-block">Reset</a>
                </div>
            </form>
        </div>
    </aside>
</div>
@endsection
@section('footer-script')
@php
$filters = [];
if (isset($filter_params['task_status']) && is_array($filter_params['task_status'])) {
            foreach ($filter_params['task_status'] as $source) {
                $filters[] = 'task_status[]=' . urlencode($source);
            }
        }

if (isset($filter_params['task_created_from_date'])) {
    $filters[] = "task_created_from_date=" . $filter_params['task_created_from_date'] . "&task_created_to_date=" . $filter_params['task_created_to_date'];
}
if (isset($filter_params['task_done_from_date'])) {
    $filters[] = "task_done_from_date=" . $filter_params['task_done_from_date'] . "&task_done_to_date=" . $filter_params['task_done_to_date'];
}
if (isset($filter_params['task_schedule_from_date'])) {
    $filters[] = "task_schedule_from_date=" . $filter_params['task_schedule_from_date'] . "&task_schedule_to_date=" . $filter_params['task_schedule_to_date'];
}
if(isset($filter_params['dashboard_filters'])){
    $filters[] = "dashboard_filters=" . $filter_params['dashboard_filters'];
}

$filter = implode('&', $filters);
@endphp
<script src="//cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script src="{{asset('plugins/moment/moment.min.js')}}"></script>
<script>
    const data_url = `{{route('team.task.list.ajax')}}?{!!$filter!!}`;
    $(document).ready(function() {
        $('#serverTable').DataTable({
            pageLength: 10,
            language: {
                "search": "_INPUT_", // Removes the 'Search' field label
                "searchPlaceholder": "Type here to search..", // Placeholder for the search box
                processing: `<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>`, // loader
            },
            serverSide: true,
            loading: true,
            ajax: {
                url: data_url,
                headers: {
                    'X-CSRF-TOKEN': "{{csrf_token()}}",
                },
                method: "get",
                dataSrc: "data",
            },

            columns: [{
                    targets: 0,
                    name: "lead_id",
                    data: "lead_id",
                },
                {
                    targets: 1,
                    name: "lead_datetime",
                    data: "lead_datetime",
                },
                {
                    targets: 2,
                    name: "name",
                    data: "name",
                },
                {
                    targets: 3,
                    name: "mobile",
                    data: "mobile",
                },
                {
                    targets: 4,
                    name: "task_schedule_datetime",
                    data: "task_schedule_datetime",
                },
                {
                    targets: 5,
                    name: "lead_id",
                    data: "lead_id",
                },
                {
                    targets: 6,
                    name: "event_datetime",
                    data: "event_datetime",
                },
                {
                    targets: 7,
                    name: "task_created_datetime",
                    data: "task_created_datetime",
                },
                {
                    targets: 8,
                    name: "task_done_datetime",
                    data: "task_done_datetime",
                },
            ],
            order: [
                [5, 'asc']
            ],
            rowCallback: function(row, data, index) {
                row.style.cursor = "pointer";
                row.setAttribute('onclick', `handle_view_lead(${data.lead_id})`);

                const td_elements = row.querySelectorAll('td');
                td_elements[1].innerText = moment(data.lead_datetime).format("DD-MMM-YYYY hh:mm a");
                td_elements[1].classList.add('text-nowrap');

                td_elements[4].innerHTML = moment(data.task_schedule_datetime).format("DD-MMM-YYYY hh:mm a");;

                const task_schedule_date = moment(data.task_schedule_datetime).format("YYYY-MM-DD");
                const current_date = moment().format("YYYY-MM-DD");
                if (data.task_done_datetime != null) {
                    elem_class = "secondary";
                    elem_text = "Done";
                } else if (task_schedule_date > current_date) {
                    elem_class = "info";
                    elem_text = "Upcoming";
                } else if (task_schedule_date == current_date) {
                    elem_class = "warning";
                    elem_text = "Today";
                } else if (task_schedule_date < current_date) {
                    elem_class = "danger";
                    elem_text = "Overdue";
                }
                td_elements[5].innerHTML = `<span class="badge badge-${elem_class}">${elem_text}</span>`;

                td_elements[6].innerText = moment(data.event_datetime).format("DD-MMM-YYYY");
                td_elements[7].innerText = moment(data.task_created_datetime).format("DD-MMM-YYYY hh:mm a");
                td_elements[8].innerText = data.task_done_datetime ? moment(data.task_done_datetime).format("DD-MMM-YYYY hh:mm a") : 'N/A';
            }
        });
    });

    function handle_view_lead(forward_id) {
        window.open(`{{route('team.lead.view')}}/${forward_id}#task_card_container`);
    }

    function handle_get_forward_info(lead_id) {
        fetch(`{{route('team.lead.getForwardInfo')}}/${lead_id}`).then(response => response.json()).then(data => {
            const forward_info_table_body = document.getElementById('forward_info_table_body');
            const modal = new bootstrap.Modal("#leadForwardedMemberInfo")
            forward_info_table_body.innerHTML = "";
            if (data.success == true) {
                if(data.lead_forwards.length > 0){
                    let i = 1;
                    for (let item of data.lead_forwards) {
                        let tr = document.createElement('tr');
                        let tds = `<td>${i}</td>
                        <td>${item.name}</td>
                        <td>${item.venue_name}</td>
                        <td>${item.read_status == 0 ? 'Unread': 'Read'}</td>
                        <td>${moment(data.lead_datetime).format("DD-MMM-YYYY hh:mm a")}</td>`;

                        tr.innerHTML = tds;
                        forward_info_table_body.appendChild(tr);
                        i++;
                    }
                }else{
                    forward_info_table_body.innerHTML = `<tr>
                        <td colspan="5">No data available in table</td>
                    </tr>`
                }
                modal.show();
            } else {
                toastr[data.alert_type](data.message);
            }
        })
    }
</script>
@endsection
