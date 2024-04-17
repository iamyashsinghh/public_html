@extends('admin.layouts.app')
@section('header-css')
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
@endsection
@section('title', $page_heading . ' | BDM CRM')
@section('navbar-right-links')
        <li class="nav-item">
            <a class="nav-link" title="Filters" data-widget="control-sidebar" data-controlsidebar-slide="true" href="#"
                role="button">
                <i class="fas fa-filter"></i>
            </a>
        </li>
@endsection
@section('main')
    @php
        $auth_user = Auth::guard('admin')->user();
    @endphp
    <div class="content-wrapper pb-5">
        <section class="content-header">
            <div class="container-fluid">
                <div class="d-flex justify-content-between mb-2">
                    <h1 class="m-0">{{ $page_heading }}</h1>
                    <a href="{{ route('admin.bdm.lead.list') }}" class="btn btn-secondary btn-sm">Refresh</a>
                </div>
                <div class="d-flex">
                <div class="button-group my-4">
                    <button class="btn btn-sm text-light buttons-print" onclick="handle_create_bdm_lead()" style="background-color: var(--wb-renosand)"><i class="fa fa-plus"></i> Create Lead</a>
                </div>
                <a href="javascript:void(0);" class="btn my-4 text-light btn-sm buttons-print mx-1"
                        onclick="handle_forward_leads_to_bdm(this)" style="background-color: var(--wb-dark-red)"><i
                            class="fa fa-paper-plane"></i> Assign to Bdm</a>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="container-fluid">
                <div class="table-responsive" style="overflow-x: visible;">
                    <table id="serverTable" class="table text-sm">
                        <thead class="sticky_head bg-light" style="position: sticky; top: 0;">
                            <tr>
                                <th class="text-nowrap"> <input type="checkbox" id="select-all-checkbox"></th>
                                <th class="text-nowrap">Lead ID</th>
                                <th class="">Assigned Bdm Name</th>
                                <th class="text-nowrap">Lead Date</th>
                                <th class="">Business Category</th>
                                <th class="">Business Name</th>
                                <th class="">Vendor Name</th>
                                <th class="text-nowrap">Mobile</th>
                                <th class="">City</th>
                                <th class="">Source</th>
                                <th class="">Service Status</th>
                                <th class="">Created or Done By</th>
                                <th class="text-nowrap">Lead Status</th>
                                <th class="">Action</th>
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
                <form action="{{ route('admin.bdm.lead.list') }}" method="post" id="filters-form">
                    @csrf
                    <div class="accordion text-sm" id="accordionExample">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="btn btn-block btn-sm btn-secondary text-left text-bold text-light"
                                        type="button" data-bs-toggle="collapse" data-bs-target="#collapse41"
                                        aria-expanded="true" aria-controls="collapse41">Lead assigned to Bdm</button>
                                </h2>
                                <div id="collapse41"
                                    class="accordion-collapse collapse {{ isset($filter_params['team_members']) ? 'show' : '' }}"
                                    data-bs-parent="#accordionExample">
                                    <div class="accordion-body pl-2 pb-4">
                                        @foreach ($getBdm as $bdm)
                                            <div class="custom-control custom-radio my-1">
                                                <input class="custom-control-input" type="radio"
                                                    id="team_member_{{ $bdm->id }}" name="team_members"
                                                    value="{{ $bdm->id }}"
                                                    {{ isset($filter_params['team_members']) && $filter_params['team_members'] == $rm->id ? 'checked' : '' }}>
                                                <label for="team_member_{{ $bdm->id }}"
                                                    class="custom-control-label">{{ $bdm->name }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="btn btn-block btn-sm btn-secondary text-left text-bold text-light"
                                        type="button" data-bs-toggle="collapse" data-bs-target="#collapse941"
                                        aria-expanded="true" aria-controls="collapse41">Lead Business Category</button>
                                </h2>
                                <div id="collapse941"
                                    class="accordion-collapse collapse {{ isset($filter_params['business_cat']) ? 'show' : '' }}"
                                    data-bs-parent="#accordionExample">
                                    <div class="accordion-body pl-2 pb-4">
                                        @foreach ($vendor_categories as $vendor)
                                            <div class="custom-control custom-radio my-1">
                                                <input class="custom-control-input" type="radio"
                                                    id="team_member_{{ $vendor->id }}" name="business_cat"
                                                    value="{{ $vendor->id }}"
                                                    {{ isset($filter_params['business_cat']) && $filter_params['business_cat'] == $vendor->id ? 'checked' : '' }}>
                                                <label for="team_member_{{ $vendor->id }}"
                                                    class="custom-control-label">{{ $vendor->name }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="btn btn-block btn-sm btn-secondary text-left text-bold text-light"
                                        type="button" data-bs-toggle="collapse" data-bs-target="#collapse491"
                                        aria-expanded="true" aria-controls="collapse491">Lead Source</button>
                                </h2>
                                <div id="collapse491"
                                    class="accordion-collapse collapse {{ isset($filter_params['lead_source']) ? 'show' : '' }}"
                                    data-bs-parent="#accordionExample">
                                    <div class="accordion-body pl-2 pb-4">
                                            <div class="custom-control custom-radio my-1">
                                                <input class="custom-control-input" type="radio"
                                                    id="lead_source_sheet" name="lead_source"
                                                    value="WB|Sheet"
                                                    {{ isset($filter_params['lead_source']) && $filter_params['lead_source'] == 'WB|Sheet' ? 'WB|Sheet' : '' }}>
                                                <label for="lead_source_sheet"
                                                    class="custom-control-label">WB|Sheet</label>
                                            </div>
                                            <div class="custom-control custom-radio my-1">
                                                <input class="custom-control-input" type="radio"
                                                    id="lead_source_Team" name="lead_source"
                                                    value="WB|Team"
                                                    {{ isset($filter_params['lead_source']) && $filter_params['lead_source'] == 'WB|Team' ? 'WB|Team' : '' }}>
                                                <label for="lead_source_Team"
                                                    class="custom-control-label">WB|Team</label>
                                            </div>
                                            <div class="custom-control custom-radio my-1">
                                                <input class="custom-control-input" type="radio"
                                                    id="lead_source_Site" name="lead_source"
                                                    value="WB|Site"
                                                    {{ isset($filter_params['lead_source']) && $filter_params['lead_source'] == 'WB|Site' ? 'WB|Site' : '' }}>
                                                <label for="lead_source_Site"
                                                    class="custom-control-label">WB|Site</label>
                                            </div>
                                            <div class="custom-control custom-radio my-1">
                                                <input class="custom-control-input" type="radio"
                                                    id="lead_source_Api" name="lead_source"
                                                    value="WB|Api"
                                                    {{ isset($filter_params['lead_source']) && $filter_params['lead_source'] == 'WB|Api' ? 'WB|Api' : '' }}>
                                                <label for="lead_source_Api"
                                                    class="custom-control-label">WB|Api</label>
                                            </div>
                                    </div>
                                </div>
                            </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="btn btn-block btn-sm btn-secondary text-left text-bold text-light"
                                    type="button" data-bs-toggle="collapse" data-bs-target="#collapse1"
                                    aria-expanded="true" aria-controls="collapse1">Lead Status</button>
                            </h2>
                            <div id="collapse1"
                                class="accordion-collapse collapse {{ isset($filter_params['lead_status']) ? 'show' : '' }}"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body pl-2 pb-4">
                                    <div class="custom-control custom-radio my-1">
                                        <input class="custom-control-input" type="radio" id="lead_status_active_radio"
                                            name="lead_status" value="Active"
                                            {{ isset($filter_params['lead_status']) && $filter_params['lead_status'] == 'Active' ? 'checked' : '' }}>
                                        <label for="lead_status_active_radio" class="custom-control-label">Active</label>
                                    </div>
                                    <div class="custom-control custom-radio my-1">
                                        <input class="custom-control-input" type="radio" id="lead_status_hot_radio"
                                            name="lead_status" value="Hot"
                                            {{ isset($filter_params['lead_status']) && $filter_params['lead_status'] == 'Hot' ? 'checked' : '' }}>
                                        <label for="lead_status_hot_radio" class="custom-control-label">Hot</label>
                                    </div>
                                    <div class="custom-control custom-radio my-1">
                                        <input class="custom-control-input" type="radio"
                                            id="lead_status_super_hot_radio" name="lead_status" value="Super Hot"
                                            {{ isset($filter_params['lead_status']) && $filter_params['lead_status'] == 'Super Hot' ? 'checked' : '' }}>
                                        <label for="lead_status_super_hot_radio" class="custom-control-label">Super
                                            Hot</label>
                                    </div>
                                    <div class="custom-control custom-radio my-1">
                                        <input class="custom-control-input" type="radio"
                                            id="lead_status_done_radio" name="lead_status" value="Done"
                                            {{ isset($filter_params['lead_status']) && $filter_params['lead_status'] == 'Done' ? 'checked' : '' }}>
                                        <label for="lead_status_done_radio" class="custom-control-label">Done</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="btn btn-block btn-sm btn-secondary text-left text-bold text-light"
                                    type="button" data-bs-toggle="collapse" data-bs-target="#collapse2"
                                    aria-expanded="true" aria-controls="collapse2">Lead Read Status</button>
                            </h2>
                            <div id="collapse2"
                                class="accordion-collapse collapse {{ isset($filter_params['lead_read_status']) ? 'show' : '' }}"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body pl-2 pb-4">
                                    <div class="custom-control custom-radio my-1">
                                        <input class="custom-control-input" type="radio" id="read_status_readed_radio"
                                            name="lead_read_status" value="1"
                                            {{ isset($filter_params['lead_read_status']) && $filter_params['lead_read_status'] == '1' ? 'checked' : '' }}>
                                        <label for="read_status_readed_radio" class="custom-control-label">Readed</label>
                                    </div>
                                    <div class="custom-control custom-radio my-1">
                                        <input class="custom-control-input" type="radio"
                                            id="read_status_unreaded_radio" name="lead_read_status" value="0"
                                            {{ isset($filter_params['lead_read_status']) && $filter_params['lead_read_status'] == '0' ? 'checked' : '' }}>
                                        <label for="read_status_unreaded_radio"
                                            class="custom-control-label">Unreaded</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="btn btn-block btn-sm btn-secondary text-left text-bold text-light"
                                    type="button" data-bs-toggle="collapse" data-bs-target="#collapse3"
                                    aria-expanded="true" aria-controls="collapse3">Service Status</button>
                            </h2>
                            <div id="collapse3"
                                class="accordion-collapse collapse {{ isset($filter_params['service_status']) ? 'show' : '' }}"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body pl-2 pb-4">
                                    <div class="custom-control custom-radio my-1">
                                        <input class="custom-control-input" type="radio"
                                            id="service_status_contacted_radio" name="service_status" value="1"
                                            {{ isset($filter_params['service_status']) && $filter_params['service_status'] == '1' ? 'checked' : '' }}>
                                        <label for="service_status_contacted_radio"
                                            class="custom-control-label">Contacted</label>
                                    </div>
                                    <div class="custom-control custom-radio my-1">
                                        <input class="custom-control-input" type="radio"
                                            id="service_status_not_contacted_radio" name="service_status" value="0"
                                            {{ isset($filter_params['service_status']) && $filter_params['service_status'] == '0' ? 'checked' : '' }}>
                                        <label for="service_status_not_contacted_radio" class="custom-control-label">Not
                                            Contacted</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="btn btn-block btn-sm btn-secondary text-left text-bold text-light"
                                    type="button" data-bs-toggle="collapse" data-bs-target="#collapse7"
                                    aria-expanded="true" aria-controls="collapse7">Lead Done Date</button>
                            </h2>
                            <div id="collapse7"
                                class="accordion-collapse collapse {{ isset($filter_params['lead_done_from_date']) ? 'show' : '' }}"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body pl-2 pb-4">
                                    <div class="form-group">
                                        <label for="lead_done_from_date">From</label>
                                        <input type="date" class="form-control" id="event_date_inp"
                                            name="lead_done_from_date"
                                            value="{{ isset($filter_params['lead_done_from_date']) ? $filter_params['lead_done_from_date'] : '' }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="lead_done_to_date">To</label>
                                        <input type="date" class="form-control" id="event_date_inp"
                                            name="lead_done_to_date"
                                            value="{{ isset($filter_params['lead_done_to_date']) ? $filter_params['lead_done_to_date'] : '' }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="btn btn-block btn-sm btn-secondary text-left text-bold text-light"
                                    type="button" data-bs-toggle="collapse" data-bs-target="#collapse6"
                                    aria-expanded="true" aria-controls="collapse6">Lead Date</button>
                            </h2>
                            <div id="collapse6"
                                class="accordion-collapse collapse {{ isset($filter_params['lead_from_date']) ? 'show' : '' }}"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body pl-2 pb-4">
                                    <div class="form-group">
                                        <label for="lead_from_date_inp">From</label>
                                        <input type="date" class="form-control" id="lead_from_date_inp"
                                            name="lead_from_date"
                                            value="{{ isset($filter_params['lead_from_date']) ? $filter_params['lead_from_date'] : '' }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="lead_to_date_inp">To</label>
                                        <input type="date" class="form-control" id="lead_to_date_inp"
                                            name="lead_to_date"
                                            value="{{ isset($filter_params['lead_to_date']) ? $filter_params['lead_to_date'] : '' }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="my-5">
                        <button type="submit" class="btn btn-sm text-light btn-block"
                            style="background-color: var(--wb-renosand);">Apply</button>
                        <a href="{{ route('admin.bdm.lead.list') }}" type="submit"
                            class="btn btn-sm btn-secondary btn-block">Reset</a>
                    </div>
                </form>
            </div>
        </aside>
    </div>
@endsection
@section('footer-script')
    @include('whatsapp.chat');
    @include('whatsapp.multiplemsg');
    @include('admin.bdmCrm.lead.manage_lead_modal');
    @include('admin.bdmCrm.lead.forward_leads_modal');

    @php
        $filter = '';
        if (isset($filter_params['dashboard_filters'])) {
            $filter = 'dashboard_filters=' . $filter_params['dashboard_filters'];
        }
        $dashfilters = isset($filter_params['dashboard_filters']) ? $filter_params['dashboard_filters'] : null;
    @endphp
    <script src="//cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
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
            const wa_status_url = `{{ route('whatsapp_chat.status') }}`;
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

        function handle_create_lead() {
            const manageLeadModal = new bootstrap.Modal(document.getElementById('manageLeadModal'));
            manageLeadModal.show();
        }
        var dashfilters = @json($dashfilters);
        const data_url = `{{ route('admin.bdm.lead.list.ajax') }}?{!! $filter !!}`;
        $(document).ready(function() {
            var dataTable;
            if (dashfilters) {
                $('#serverTable').DataTable({
                    pageLength: 10,
                    processing: true,
                    loading: true,
                    language: {
                        search: "_INPUT_",
                        searchPlaceholder: "Type here to search..",
                        processing: `<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>`, // loader
                    },
                    serverSide: true,
                    ajax: {
                        url: data_url,
                    },
                    columns: [{
                            targets: 0,
                            name: "lead_id",
                            data: "lead_id",
                            orderable: false,
                            searchable: false,
                        },
                        {
                            targets: 1,
                            name: "lead_id",
                            data: "lead_id",
                        },
                        {
                            targets: 2,
                            name: "assign_to",
                            data: "assign_to",
                        },
                        {
                            targets: 3,
                            name: "lead_datetime",
                            data: "lead_datetime",
                        },
                        {
                            targets: 4,
                            name: "business_cat",
                            data: "business_cat",
                        },
                        {
                            targets: 5,
                            name: "business_name",
                            data: "business_name",
                        },
                        {
                            targets: 6,
                            name: "name",
                            data: "name",
                        },
                        {
                            targets: 7,
                            name: "mobile",
                            data: "mobile",
                        },
                        {
                            targets: 8,
                            name: "city",
                            data: "city",
                        },
                        {
                            targets: 9,
                            name: "source",
                            data: "source",
                        },
                        {
                            targets: 10,
                            name: "service_status",
                            data: "service_status",
                        },
                        {
                            targets: 11,
                            name: "created_by",
                            data: "created_by",
                        },
                        {
                            targets: 12,
                            name: "lead_status",
                            data: "lead_status",
                        },
                        {
                            targets: 13,
                            name: "whatsapp_msg_time",
                            data: "whatsapp_msg_time",
                        }
                    ],
                    order : [
                        [13, 'desc'],[3, 'desc']
                    ],
                    rowCallback: function(row, data, index) {
                        const td_elements = row.querySelectorAll('td');
                        td_elements[0].innerHTML = `<i class="fa fa-arrow-rotate-right"></i><span class="mx-1">${data.enquiry_count}</span><br>
                    <input type="checkbox" onchange="handle_select_single_lead(this)" class="forward_lead_checkbox" value="${data.lead_id}">`;

                    td_elements[3].innerText = moment(data.lead_datetime).format("DD-MMM-YYYY hh:mm a");

                    if (data.is_whatsapp_msg === 1) {
                        td_elements[7].innerHTML =
                            `<div class="d-flex"><div>${data.mobile} </div> &nbsp;&nbsp;&nbsp;<i class="fa-brands fa-square-whatsapp" onclick="handle_whatsapp_msg(${data.mobile})" id="what_id-${data.mobile}" style="font-size: 25px; color: green;"></i></div>`;
                    } else {
                        td_elements[7].innerHTML =
                            `<div class="d-flex"><div>${data.mobile} </div>&nbsp;&nbsp;&nbsp;<i class="fab fa-whatsapp" onclick="handle_whatsapp_msg(${data.mobile})" style="font-size: 25px; color: green;"></i></div>`;
                    }
                    if (data.service_status == 1) {
                                td_elements[10].innerHTML =
                                    `<span class="badge badge-success">Contacted</span>`;
                            } else {
                                td_elements[10].innerHTML =
                                    `<span class="badge badge-danger">Not-Contacted</span>`;
                            }
                        const action_btns =
                        `<a href="{{ route('admin.bdm.lead.view') }}/${data.lead_id}" target="_blank" class="text-dark mx-2" title="View"><i class="fa fa-eye" style="font-size: 15px;"></i></a>
                        <a href="{{ route('admin.bdm.lead.delete') }}/${data.lead_id}" onclick="return confirm('Are you sure want to delete?')" class="text-danger mx-2" title="Delete"><i class="fa fa-trash-alt" style="font-size: 15px;"></i></a>`;
                    td_elements[13].classList.add('text-nowrap');
                    td_elements[13].innerHTML = action_btns;
                    }
                });
            } else {
                dataTable = $('#serverTable').DataTable({
                    pageLength: 10,
                    processing: true,
                    loading: true,
                    language: {
                        search: "_INPUT_", // Removes the 'Search' field label
                        searchPlaceholder: "Type here to search..", // Placeholder for the search box
                    },
                    serverSide: true,
                    ajax: {
                        url: "{{ route('admin.bdm.lead.list.ajax') }}",
                        data: function(d) {
                            var formData = $('#filters-form').serializeArray();
                            formData.forEach(function(item) {
                                d[item.name] = item.value;
                            });
                        },
                    },
                    columns: [{
                            targets: 0,
                            name: "lead_id",
                            data: "lead_id",
                            orderable: false,
                            searchable: false,
                        },
                        {
                            targets: 1,
                            name: "lead_id",
                            data: "lead_id",
                        },
                        {
                            targets: 2,
                            name: "assign_to",
                            data: "assign_to",
                        },
                        {
                            targets: 3,
                            name: "lead_datetime",
                            data: "lead_datetime",
                        },
                        {
                            targets: 4,
                            name: "business_cat",
                            data: "business_cat",
                        },
                        {
                            targets: 5,
                            name: "business_name",
                            data: "business_name",
                        },
                        {
                            targets: 6,
                            name: "name",
                            data: "name",
                        },
                        {
                            targets: 7,
                            name: "mobile",
                            data: "mobile",
                        },
                        {
                            targets: 8,
                            name: "city",
                            data: "city",
                        },
                        {
                            targets: 9,
                            name: "source",
                            data: "source",
                        },
                        {
                            targets: 10,
                            name: "service_status",
                            data: "service_status",
                        },
                        {
                            targets: 11,
                            name: "created_by",
                            data: "created_by",
                        },
                        {
                            targets: 12,
                            name: "lead_status",
                            data: "lead_status",
                        },
                        {
                            targets: 13,
                            name: "whatsapp_msg_time",
                            data: "whatsapp_msg_time",
                        }
                    ],
                    order : [
                        [13, 'desc'],[3, 'desc']
                    ],
                    rowCallback: function(row, data, index) {
                        const td_elements = row.querySelectorAll('td');
                        td_elements[0].innerHTML = `<i class="fa fa-arrow-rotate-right"></i><span class="mx-1">${data.enquiry_count}</span><br>
                    <input type="checkbox" onchange="handle_select_single_lead(this)" class="forward_lead_checkbox" value="${data.lead_id}">`;

                    td_elements[3].innerText = moment(data.lead_datetime).format("DD-MMM-YYYY hh:mm a");

                    if (data.is_whatsapp_msg === 1) {
                        td_elements[7].innerHTML =
                            `<div class="d-flex"><div>${data.mobile} </div> &nbsp;&nbsp;&nbsp;<i class="fa-brands fa-square-whatsapp" onclick="handle_whatsapp_msg(${data.mobile})" id="what_id-${data.mobile}" style="font-size: 25px; color: green;"></i></div>`;
                    } else {
                        td_elements[7].innerHTML =
                            `<div class="d-flex"><div>${data.mobile} </div>&nbsp;&nbsp;&nbsp;<i class="fab fa-whatsapp" onclick="handle_whatsapp_msg(${data.mobile})" style="font-size: 25px; color: green;"></i></div>`;
                    }
                    if (data.service_status == 1) {
                                td_elements[10].innerHTML =
                                    `<span class="badge badge-success">Contacted</span>`;
                            } else {
                                td_elements[10].innerHTML =
                                    `<span class="badge badge-danger">Not-Contacted</span>`;
                            }
                            const action_btns =
                        `<a href="{{ route('admin.bdm.lead.view') }}/${data.lead_id}" target="_blank" class="text-dark mx-2" title="View"><i class="fa fa-eye" style="font-size: 15px;"></i></a>
                        <a href="{{ route('admin.bdm.lead.delete') }}/${data.lead_id}" onclick="return confirm('Are you sure want to delete?')" class="text-danger mx-2" title="Delete"><i class="fa fa-trash-alt" style="font-size: 15px;"></i></a>`;
                    td_elements[13].classList.add('text-nowrap');
                    td_elements[13].innerHTML = action_btns;
                    }
                });
            }

            $('#filters-form').on('submit', function(e) {
                e.preventDefault();
                dataTable.ajax.reload();
                document.querySelector('[data-widget="control-sidebar"]').click();
            });
        });


        $('#select-all-checkbox').on('change', function() {
            var isChecked = $(this).prop('checked');
            $('.forward_lead_checkbox').prop('checked', isChecked);
        });
        $('.forward_lead_checkbox').on('change', function() {
            $('#select-all-checkbox').prop('checked', $('.forward_lead_checkbox:checked').length === $(
                '.forward_lead_checkbox').length);
        });

        function getSelectedCheckboxValues() {
            const manageWhatsappChatModal = new bootstrap.Modal(document.getElementById('wa_msg_multiple'));
            var selectedValues = [];
            $('.forward_lead_checkbox:checked').each(function() {
                selectedValues.push($(this).val());
            });
            let phonenum = document.getElementById('phone_inp_id_m');
            phonenum.value = selectedValues;
            if (selectedValues.length > 0) {
                manageWhatsappChatModal.show();
            } else {
                toastr.info("Select the lead's which you want to send messages.");
            }
        }

        function handle_view_lead(lead_id) {
            window.open(`{{ route('admin.bdm.lead.view') }}/${lead_id}`);
        }
        function handle_forward_leads_to_bdm(elem) {
            var selectedValues = [];
            $('.forward_lead_checkbox:checked').each(function() {
                selectedValues.push($(this).val());
            });
            if (selectedValues.length > 0) {
                document.querySelector('input[name="forward_leads_id"]').value = selectedValues;
                const forward_rms_chekbox = document.querySelectorAll(`input[name="forward_bdm_id"]`);
                const modal = new bootstrap.Modal("#forwardLeadModal");
                modal.show();
            } else {
                toastr.info("Select the lead's which you want to forward.");
            }
        }
    </script>
@endsection
