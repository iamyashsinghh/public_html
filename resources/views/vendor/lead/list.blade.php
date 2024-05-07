@extends('vendor.layouts.app')
@section('header-css')
<link rel="stylesheet" href="//cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
@endsection
@section('title', $page_heading." | Vendor CRM")
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
                    <form action="{{route('vendor.lead.list')}}" method="post">
                        @csrf
                        <label for="">Filter by lead date</label>
                        <input type="date" name="lead_from_date" value="{{isset($filter_params['lead_from_date']) ? $filter_params['lead_from_date'] : ''}}" class="form-control form-control-sm d-inline-block" style="width: unset;" required>
                        <span class="">To:</span>
                        <input type="date" name="lead_to_date" value="{{isset($filter_params['lead_to_date']) ? $filter_params['lead_to_date'] : ''}}" class="form-control form-control-sm d-inline-block" style="width: unset;">
                        <button type="submit" class="btn text-light btn-sm" style="background-color: var(--wb-dark-red)">Submit</button>
                        <a href="{{route('vendor.lead.list')}}" class="btn btn-secondary btn-sm">Reset</a>
                    </form>
                </div>
            @endif
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="table-responsive" style="overflow-x: visible;">
                <table id="serverTable" class="table text-sm">
                    <thead class="sticky_head bg-light" style="position: sticky; top: 0;">
                        <tr>
                            <th class="text-nowrap">Lead ID</th>
                            <th class="text-nowrap">Lead Date</th>
                            <th class="">Name</th>
                            <th class="text-nowrap">Mobile</th>
                            <th class="">Pax</th>
                            <th class="">Lead Status</th>
                            <th class="text-nowrap">Event Date</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </section>
    <aside class="control-sidebar control-sidebar-dark" style="display: none;">
        <div class="p-3 control-sidebar-content">
            <h5>Lead Filters</h5>
            <hr class="mb-2">
            <form action="{{route('vendor.lead.list')}}" method="post">
                @csrf
                <div class="accordion text-sm" id="accordionExample">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="btn btn-block btn-sm btn-secondary text-left text-bold text-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="true" aria-controls="collapse1">Lead Status</button>
                        </h2>
                        <div id="collapse1" class="accordion-collapse collapse {{isset($filter_params['lead_status']) ? 'show' : ''}}" data-bs-parent="#accordionExample">
                            <div class="accordion-body pl-2 pb-4">
                                <div class="custom-control custom-radio my-1">
                                    <input class="custom-control-input" type="radio" id="lead_status_active_radio" name="lead_status" value="Active" {{isset($filter_params['lead_status']) && $filter_params['lead_status'] == 'Active'  ? 'checked' : ''}}>
                                    <label for="lead_status_active_radio" class="custom-control-label">Active</label>
                                </div>
                                <div class="custom-control custom-radio my-1">
                                    <input class="custom-control-input" type="radio" id="lead_status_done_radio" name="lead_status" value="Done" {{isset($filter_params['lead_status']) && $filter_params['lead_status'] == 'Done'  ? 'checked' : ''}}>
                                    <label for="lead_status_done_radio" class="custom-control-label">Done</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="btn btn-block btn-sm btn-secondary text-left text-bold text-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2" aria-expanded="true" aria-controls="collapse2">Lead Read Status</button>
                        </h2>
                        <div id="collapse2" class="accordion-collapse collapse {{isset($filter_params['lead_read_status']) ? 'show' : ''}}" data-bs-parent="#accordionExample">
                            <div class="accordion-body pl-2 pb-4">
                                <div class="custom-control custom-radio my-1">
                                    <input class="custom-control-input" type="radio" id="read_status_readed_radio" name="lead_read_status" value="1" {{isset($filter_params['lead_read_status']) && $filter_params['lead_read_status'] == '1'  ? 'checked' : ''}}>
                                    <label for="read_status_readed_radio" class="custom-control-label">Readed</label>
                                </div>
                                <div class="custom-control custom-radio my-1">
                                    <input class="custom-control-input" type="radio" id="read_status_unreaded_radio" name="lead_read_status" value="0" {{isset($filter_params['lead_read_status']) && $filter_params['lead_read_status'] == '0'  ? 'checked' : ''}}>
                                    <label for="read_status_unreaded_radio" class="custom-control-label">Unreaded</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="btn btn-block btn-sm btn-secondary text-left text-bold text-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4" aria-expanded="true" aria-controls="collapse4">Has RM Message?</button>
                        </h2>
                        <div id="collapse4" class="accordion-collapse collapse {{isset($filter_params['has_rm_message']) ? 'show' : ''}}" data-bs-parent="#accordionExample">
                            <div class="accordion-body pl-2 pb-4">
                                <div class="custom-control custom-radio my-1">
                                    <input class="custom-control-input" type="radio" id="has_rm_message_no_radio" name="has_rm_message" value="no" {{isset($filter_params['has_rm_message']) && $filter_params['has_rm_message'] == 'no'  ? 'checked' : ''}}>
                                    <label for="has_rm_message_no_radio" class="custom-control-label">No</label>
                                </div>
                                <div class="custom-control custom-radio my-1">
                                    <input class="custom-control-input" type="radio" id="has_rm_message_yes_radio" name="has_rm_message" value="yes" {{isset($filter_params['has_rm_message']) && $filter_params['has_rm_message'] == 'yes'  ? 'checked' : ''}}>
                                    <label for="has_rm_message_yes_radio" class="custom-control-label">Yes</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="btn btn-block btn-sm btn-secondary text-left text-bold text-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapse55" aria-expanded="true" aria-controls="collapse55">Pax</button>
                        </h2>
                        <div id="collapse55" class="accordion-collapse collapse {{isset($filter_params['pax_min_value']) ? 'show' : ''}}" data-bs-parent="#accordionExample">
                            <div class="accordion-body pl-2 pb-4">
                                <div class="form-group">
                                    <label for="pax_min_value">MIN</label>
                                    <input type="text" class="form-control" id="pax_min_value" name="pax_min_value" value="{{isset($filter_params['pax_min_value']) ? $filter_params['pax_min_value'] : ''}}">
                                </div>
                                <div class="form-group">
                                    <label for="pax_max_value">To</label>
                                    <input type="text" class="form-control" id="pax_max_value" name="pax_max_value" value="{{isset($filter_params['pax_max_value']) ? $filter_params['pax_max_value'] : ''}}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="btn btn-block btn-sm btn-secondary text-left text-bold text-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapse5" aria-expanded="true" aria-controls="collapse5">Event Date</button>
                        </h2>
                        <div id="collapse5" class="accordion-collapse collapse {{isset($filter_params['event_from_date']) ? 'show' : ''}}" data-bs-parent="#accordionExample">
                            <div class="accordion-body pl-2 pb-4">
                                <div class="form-group">
                                    <label for="event_from_date_inp">From</label>
                                    <input type="date" class="form-control" id="event_from_date_inp" name="event_from_date" value="{{isset($filter_params['event_from_date']) ? $filter_params['event_from_date'] : ''}}">
                                </div>
                                <div class="form-group">
                                    <label for="event_to_date_inp">To</label>
                                    <input type="date" class="form-control" id="event_to_date_inp" name="event_to_date" value="{{isset($filter_params['event_to_date']) ? $filter_params['event_to_date'] : ''}}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="btn btn-block btn-sm btn-secondary text-left text-bold text-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapse6" aria-expanded="true" aria-controls="collapse6">Lead Date</button>
                        </h2>
                        <div id="collapse6" class="accordion-collapse collapse {{isset($filter_params['lead_from_date']) ? 'show' : ''}}" data-bs-parent="#accordionExample">
                            <div class="accordion-body pl-2 pb-4">
                                <div class="form-group">
                                    <label for="lead_from_date_inp">From</label>
                                    <input type="date" class="form-control" id="lead_from_date_inp" name="lead_from_date" value="{{isset($filter_params['lead_from_date']) ? $filter_params['lead_from_date'] : ''}}">
                                </div>
                                <div class="form-group">
                                    <label for="lead_to_date_inp">To</label>
                                    <input type="date" class="form-control" id="lead_to_date_inp" name="lead_to_date" value="{{isset($filter_params['lead_to_date']) ? $filter_params['lead_to_date'] : ''}}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="btn btn-block btn-sm btn-secondary text-left text-bold text-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapse7" aria-expanded="true" aria-controls="collapse7">Lead Done Date</button>
                        </h2>
                        <div id="collapse7" class="accordion-collapse collapse {{isset($filter_params['lead_done_from_date']) ? 'show' : ''}}" data-bs-parent="#accordionExample">
                            <div class="accordion-body pl-2 pb-4">
                                <div class="form-group">
                                    <label for="lead_done_from_date">From</label>
                                    <input type="date" class="form-control" id="event_date_inp" name="lead_done_from_date" value="{{isset($filter_params['lead_done_from_date']) ? $filter_params['lead_done_from_date'] : ''}}">
                                </div>
                                <div class="form-group">
                                    <label for="lead_done_to_date">To</label>
                                    <input type="date" class="form-control" id="event_date_inp" name="lead_done_to_date" value="{{isset($filter_params['lead_done_to_date']) ? $filter_params['lead_done_to_date'] : ''}}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="my-5">
                    <button type="submit" class="btn btn-sm text-light btn-block" style="background-color: var(--wb-renosand);">Apply</button>
                    <a href="{{route('vendor.lead.list')}}" type="submit" class="btn btn-sm btn-secondary btn-block">Reset</a>
                </div>
            </form>
        </div>
    </aside>
</div>
@endsection
@section('footer-script')
@php
$filter = "";
if (isset($filter_params['lead_status'])) {
$filter = "lead_status=" . $filter_params['lead_status'];
} elseif (isset($filter_params['lead_read_status'])) {
$filter = "lead_read_status=" . $filter_params['lead_read_status'];
} elseif (isset($filter_params['has_rm_message'])) {
$filter = "has_rm_message=" . $filter_params['has_rm_message'];
} elseif (isset($filter_params['event_from_date'])) {
$filter = "event_from_date=" . $filter_params['event_from_date'] . "&event_to_date=" . $filter_params['event_to_date'];
} elseif (isset($filter_params['lead_from_date'])) {
$filter = "lead_from_date=" . $filter_params['lead_from_date'] . "&lead_to_date=" . $filter_params['lead_to_date'];
} elseif (isset($filter_params['pax_min_value'])) {
$filter = "pax_min_value=" . $filter_params['pax_min_value'] . "&pax_max_value=" . $filter_params['pax_max_value'];
} elseif (isset($filter_params['lead_done_from_date'])) {
$filter = "lead_done_from_date=" . $filter_params['lead_done_from_date'] . "&lead_done_to_date=" . $filter_params['lead_done_to_date'];
}elseif(isset($filter_params['dashboard_filters'])){
    $filter = "dashboard_filters=" . $filter_params['dashboard_filters'];
}
@endphp
<script src="//cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script src="{{asset('plugins/moment/moment.min.js')}}"></script>
<script>
    const data_url = `{{route('vendor.lead.list.ajax')}}?{!!$filter!!}`;

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
                    name: "lead_date",
                    data: "lead_date",
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
                    name: "pax",
                    data: "pax",
                },
                {
                    targets: 5,
                    name: "lead_status",
                    data: "lead_status",
                },
                {
                    targets: 6,
                    name: "event_date",
                    data: "event_date",
                },
            ],
            order: [
                [1, 'desc']
            ],
            rowCallback: function(row, data, index) {

                if (data.read_status == 1) {
                    row.style.background = "#3636361f";
                }
                const td_elements = row.querySelectorAll('td');
                row.style.cursor = "pointer";
                row.setAttribute('onclick', `handle_view_lead(${data.lead_id})`);

                td_elements[1].innerText = moment(data.lead_date).format("DD-MMM-YYYY hh:mm a");
                if (data.lead_status == "Done") {
                    td_elements[5].innerHTML = `<span class="badge badge-secondary">Done</span>`;
                } else {
                    td_elements[5].innerHTML = `<span class="badge badge-success">${data.lead_status}</span>`;
                }
                td_elements[6].innerText = data.event_date ? moment(data.event_date).format("DD-MMM-YYYY") : 'N/A';
            }
        });
    });

    function handle_view_lead(lead_id) {
        window.open(`{{route('vendor.lead.view')}}/${lead_id}`);
    }
</script>
@endsection
