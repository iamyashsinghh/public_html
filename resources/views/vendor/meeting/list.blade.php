@extends('vendor.layouts.app')
@section('header-css')
<link rel="stylesheet" href="//cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
@endsection
@section('title', $page_heading." | Vendor CRM")
@section('navbar-right-links')
<li class="nav-item">
    <a class="nav-link" title="Filters" data-widget="control-sidebar" data-controlsidebar-slide="true" href="#" role="button">
        <i class="fas fa-filter"></i>
    </a>
</li>
@endsection
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
            <div class="filter-container text-center">
                <form action="{{route('vendor.meeting.list')}}" method="post">
                    @csrf
                    <label for="">Filter by meeting schedule date</label>
                    <input type="date" name="meeting_schedule_from_date" value="{{isset($filter_params['meeting_schedule_from_date']) ? $filter_params['meeting_schedule_from_date'] : ''}}" class="form-control form-control-sm d-inline-block" style="width: unset;" required>
                    <span class="">To:</span>
                    <input type="date" name="meeting_schedule_to_date" value="{{isset($filter_params['meeting_schedule_to_date']) ? $filter_params['meeting_schedule_to_date'] : ''}}" class="form-control form-control-sm d-inline-block" style="width: unset;">
                    <button type="submit" class="btn text-light btn-sm" style="background-color: var(--wb-dark-red)">Submit</button>
                    <a href="{{route('vendor.meeting.list')}}" class="btn btn-secondary btn-sm">Reset</a>
                </form>
            </div>
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
                            <th class="text-nowrap">Lead Status</th>
                            <th class="text-nowrap">Meeting Schedule Date</th>
                            <th class="text-nowrap">Meeting Status</th>
                            <th class="text-nowrap">Event Date</th>
                            <th class="text-nowrap">Meeting Created Date</th>
                            <th class="text-nowrap">Meeting Done Date</th>
                        </tr>
                    </thead>
                </table>

            </div>
        </div>
    </section>
    <aside class="control-sidebar control-sidebar-dark" style="display: none;">
        <div class="p-3 control-sidebar-content">
            <h5>Meeting Filters</h5>
            <hr class="mb-2">
            <form action="{{route('vendor.meeting.list')}}" method="post">
                @csrf
                <div class="accordion text-sm" id="accordionExample">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="btn btn-block btn-sm btn-secondary text-left text-bold text-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="true" aria-controls="collapse1">Meeting Status</button>
                        </h2>
                        <div id="collapse1" class="accordion-collapse collapse {{isset($filter_params['meeting_status']) ? 'show' : ''}}" data-bs-parent="#accordionExample">
                            <div class="accordion-body pl-2 pb-4">
                                <div class="custom-control custom-radio my-1">
                                    <input class="custom-control-input" type="radio" id="meeting_status_upcoming_radio" name="meeting_status" value="Upcoming" {{isset($filter_params['meeting_status']) && $filter_params['meeting_status'] == 'Upcoming'  ? 'checked' : ''}}>
                                    <label for="meeting_status_upcoming_radio" class="custom-control-label">Upcoming</label>
                                </div>
                                <div class="custom-control custom-radio my-1">
                                    <input class="custom-control-input" type="radio" id="meeting_status_today_radio" name="meeting_status" value="Today" {{isset($filter_params['meeting_status']) && $filter_params['meeting_status'] == 'Today'  ? 'checked' : ''}}>
                                    <label for="meeting_status_today_radio" class="custom-control-label">Today</label>
                                </div>
                                <div class="custom-control custom-radio my-1">
                                    <input class="custom-control-input" type="radio" id="meeting_status_overdue_radio" name="meeting_status" value="Overdue" {{isset($filter_params['meeting_status']) && $filter_params['meeting_status'] == 'Overdue'  ? 'checked' : ''}}>
                                    <label for="meeting_status_overdue_radio" class="custom-control-label">Overdue</label>
                                </div>
                                <div class="custom-control custom-radio my-1">
                                    <input class="custom-control-input" type="radio" id="meeting_status_done_radio" name="meeting_status" value="Done" {{isset($filter_params['meeting_status']) && $filter_params['meeting_status'] == 'Done'  ? 'checked' : ''}}>
                                    <label for="meeting_status_done_radio" class="custom-control-label">Done</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="btn btn-block btn-sm btn-secondary text-left text-bold text-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2" aria-expanded="true" aria-controls="collapse2">Meeting Created Date</button>
                        </h2>
                        <div id="collapse2" class="accordion-collapse collapse {{isset($filter_params['meeting_created_from_date']) ? 'show' : ''}}" data-bs-parent="#accordionExample">
                            <div class="accordion-body pl-2 pb-4">
                                <div class="form-group">
                                    <label for="meeting_created_from_date_inp">From</label>
                                    <input type="date" class="form-control" id="meeting_created_from_date_inp" name="meeting_created_from_date" value="{{isset($filter_params['meeting_created_from_date']) ? $filter_params['meeting_created_from_date'] : ''}}">
                                </div>
                                <div class="form-group">
                                    <label for="meeting_created_to_date_inp">To</label>
                                    <input type="date" class="form-control" id="meeting_created_to_date_inp" name="meeting_created_to_date" value="{{isset($filter_params['meeting_created_to_date']) ? $filter_params['meeting_created_to_date'] : ''}}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="btn btn-block btn-sm btn-secondary text-left text-bold text-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3" aria-expanded="true" aria-controls="collapse3">Meeting Done Date</button>
                        </h2>
                        <div id="collapse3" class="accordion-collapse collapse {{isset($filter_params['meeting_done_from_date']) ? 'show' : ''}}" data-bs-parent="#accordionExample">
                            <div class="accordion-body pl-2 pb-4">
                                <div class="form-group">
                                    <label for="meeting_done_from_date_inp">From</label>
                                    <input type="date" class="form-control" id="meeting_done_from_date_inp" name="meeting_done_from_date" value="{{isset($filter_params['meeting_done_from_date']) ? $filter_params['meeting_done_from_date'] : ''}}">
                                </div>
                                <div class="form-group">
                                    <label for="meeting_done_to_date_inp">To</label>
                                    <input type="date" class="form-control" id="meeting_done_to_date_inp" name="meeting_done_to_date" value="{{isset($filter_params['meeting_done_to_date']) ? $filter_params['meeting_done_to_date'] : ''}}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="btn btn-block btn-sm btn-secondary text-left text-bold text-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4" aria-expanded="true" aria-controls="collapse3">Meeting Schedule Date</button>
                        </h2>
                        <div id="collapse4" class="accordion-collapse collapse {{isset($filter_params['meeting_schedule_from_date']) ? 'show' : ''}}" data-bs-parent="#accordionExample">
                            <div class="accordion-body pl-2 pb-4">
                                <div class="form-group">
                                    <label for="meeting_schedule_from_date_inp">From</label>
                                    <input type="date" class="form-control" id="meeting_schedule_from_date_inp" name="meeting_schedule_from_date" value="{{isset($filter_params['meeting_schedule_from_date']) ? $filter_params['meeting_schedule_from_date'] : ''}}">
                                </div>
                                <div class="form-group">
                                    <label for="meeting_schedule_to_date">To</label>
                                    <input type="date" class="form-control" id="meeting_schedule_to_date_inp" name="meeting_schedule_to_date" value="{{isset($filter_params['meeting_schedule_to_date']) ? $filter_params['meeting_schedule_to_date'] : ''}}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="my-5">
                    <button type="submit" class="btn btn-sm text-light btn-block" style="background-color: var(--wb-renosand);">Apply</button>
                    <a href="{{route('vendor.meeting.list')}}" type="submit" class="btn btn-sm btn-secondary btn-block">Reset</a>
                </div>
            </form>
        </div>
    </aside>
</div>
@endsection
@section('footer-script')
@php
$filter = "";
if (isset($filter_params['meeting_status'])) {
    $filter = "meeting_status=" . $filter_params['meeting_status'];
} elseif (isset($filter_params['meeting_created_from_date'])) {
    $filter = "meeting_created_from_date=" . $filter_params['meeting_created_from_date'] . "&meeting_created_to_date=" . $filter_params['meeting_created_to_date'];
}elseif (isset($filter_params['meeting_done_from_date'])) {
    $filter = "meeting_done_from_date=" . $filter_params['meeting_done_from_date'] . "&meeting_done_to_date=" . $filter_params['meeting_done_to_date'];
}elseif (isset($filter_params['meeting_schedule_from_date'])) {
    $filter = "meeting_schedule_from_date=" . $filter_params['meeting_schedule_from_date'] . "&meeting_schedule_to_date=" . $filter_params['meeting_schedule_to_date'];
}
@endphp
<script src="//cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script src="{{asset('plugins/moment/moment.min.js')}}"></script>
<script>
    const data_url = `{{route('vendor.meeting.list.ajax')}}?{!!$filter!!}`;
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
                    name: "lead_status",
                    data: "lead_status",
                },
                {
                    targets: 5,
                    name: "meeting_schedule_datetime",
                    data: "meeting_schedule_datetime",
                },
                {
                    targets: 6,
                    name: "lead_id",
                    data: "lead_id",
                },
                {
                    targets: 7,
                    name: "event_datetime",
                    data: "event_datetime",
                },
                {
                    targets: 8,
                    name: "meeting_created_datetime",
                    data: "meeting_created_datetime",
                },
                {
                    targets: 9,
                    name: "meeting_done_datetime",
                    data: "meeting_done_datetime",
                },
            ],
            order: [
                [5, 'asc']
            ],
            rowCallback: function(row, data, index) {
                if(data.read_status == 1){
                    row.style.background = "#3636361f";
                }
                row.style.cursor = "pointer";
                row.setAttribute('onclick', `handle_view_lead(${data.lead_id})`);
        
                const td_elements = row.querySelectorAll('td');
                td_elements[1].innerText = moment(data.lead_datetime).format("DD-MMM-YYYY hh:mm a");
                td_elements[1].classList.add('text-nowrap');
                if(data.lead_status == "Done"){
                    td_elements[4].innerHTML = `<span class="badge badge-secondary">Done</span>`;
                }else{
                    td_elements[4].innerHTML = `<span class="badge badge-success">${data.lead_status}</span>`;
                }
                
                td_elements[5].innerHTML = moment(data.meeting_schedule_datetime).format("DD-MMM-YYYY hh:mm a");; 
                
                const meeting_schedule_date = moment(data.meeting_schedule_datetime).format("DD-MMM-YYYY");
                const current_date = moment().format("DD-MMM-YYYY");
                if (data.meeting_done_datetime != null) {
                    elem_class = "success";
                    elem_text = "Done";
                } else if (meeting_schedule_date > current_date) {
                    elem_class = "info";
                    elem_text = "Upcoming";
                } else if (meeting_schedule_date == current_date) {
                    elem_class = "warning";
                    elem_text = "Today";
                } else if (meeting_schedule_date < current_date) { 
                    elem_class = "danger";
                    elem_text = "Overdue";
                } 
                td_elements[6].innerHTML = `<span class="badge badge-${elem_class}">${elem_text}</span>`; 

                td_elements[7].innerText = moment(data.event_datetime).format("DD-MMM-YYYY");
                td_elements[8].innerText = moment(data.meeting_created_datetime).format("DD-MMM-YYYY hh:mm a");
                td_elements[9].innerText = data.meeting_done_datetime ? moment(data.meeting_done_datetime).format("DD-MMM-YYYY hh:mm a") : 'N/A';
            }
        });
    });

    function handle_view_lead(forward_id) {
        window.open(`{{route('vendor.lead.view')}}/${forward_id}#meeting_card_container`);
    }
</script>
@endsection