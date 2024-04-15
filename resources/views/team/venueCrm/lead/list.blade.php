@extends('team.layouts.app')
@section('header-css')
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
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
        $auth_user = Auth::guard('team')->user();
    @endphp
    <div class="content-wrapper pb-5">
        <section class="content-header">
            <div class="container-fluid">
                <div class="d-flex justify-content-between mb-2">
                    <h1 class="m-0">{{ $page_heading }}</h1>
                    <a href="{{ route('team.lead.list') }}" class="btn btn-secondary btn-sm">Refresh</a>
                    @if ($auth_user->role_id == 4)
                        <div>

                            <button class="btn btn-sm text-light" onclick="getSelectedCheckboxValues()"
                                style="background-color: var(--wb-renosand);">Whatsapp</button>
                        </div>
                    @endif
                </div>
                @if (!isset($filter_params['dashboard_filters']))
                    <div class="filter-container text-center" style="display: none">
                        <form action="{{ route('team.lead.list') }}" method="post">
                            @csrf
                            <label for="">Filter by lead date</label>
                            <input type="date" name="lead_from_date"
                                value="{{ isset($filter_params['lead_from_date']) ? $filter_params['lead_from_date'] : '' }}"
                                class="form-control form-control-sm d-inline-block" style="width: unset;" required>
                            <span class="">To:</span>
                            <input type="date" name="lead_to_date"
                                value="{{ isset($filter_params['lead_to_date']) ? $filter_params['lead_to_date'] : '' }}"
                                class="form-control form-control-sm d-inline-block" style="width: unset;">
                            <button type="submit" class="btn text-light btn-sm"
                                style="background-color: var(--wb-dark-red)">Submit</button>
                            <a href="{{ route('team.lead.list') }}" class="btn btn-secondary btn-sm">Reset</a>
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
                                @if ($auth_user->role_id == 4)
                                <th class="text-nowrap"> <input type="checkbox" id="select-all-checkbox"></th>
                                <th class="text-nowrap">Lead ID</th>
                                <th class="">Assigned Rm Name</th>
                                <th class="text-nowrap">Lead Date</th>
                                <th class="">Name</th>
                                <th class="text-nowrap">Mobile</th>
                                <th class="">Source</th>
                                <th class="text-nowrap">Event Date</th>
                                <th class="">Service Status</th>
                                <th class="">Catagory</th>
                                <th class="">Preference</th>
                                <th class="">Locality</th>
                                <th class="">Created or Done By</th>
                                <th class="">Last Forwarded By</th>
                                <th class="text-nowrap">Lead Status</th>
                                <th class="">Action</th>

                                @endif
                                @if ($auth_user->role_id == 5)
                                <th class="text-nowrap"> <input type="checkbox" id="select-all-checkbox"></th>
                                <th class="text-nowrap">Lead ID</th>
                                <th class="text-nowrap">Lead Date</th>
                                <th class="">Name</th>
                                <th class="text-nowrap">Mobile</th>
                                <th class="text-nowrap">Event Date</th>
                                <th class="text-nowrap">Lead Status</th>
                                <th class="">Service Status</th>
                                @endif
                            </tr>
                        </thead>
                    </table>

                </div>
            </div>
        </section>
        <div class="modal fade" id="leadForwardedMemberInfo" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Forward Information</h4>
                        <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i
                                class="fa fa-times"></i></button>
                    </div>
                    <div class="modal-body">
                        <p id="last_forwarded_info_paragraph" class="text-sm mb-2"></p>
                        <div class="table-responsive text-center">
                            <table id="clientTable" class="table text-sm">
                                <thead>
                                    <tr>
                                        <th class="text-nowrap">S.No.</th>
                                        <th class="text-nowrap">Name</th>
                                        <th class="text-nowrap">Venue Name</th>
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
        <aside class="control-sidebar control-sidebar-dark" style="display: none;">
            <div class="p-3 control-sidebar-content">
                <h5>Lead Filters</h5>
                <hr class="mb-2">
                <form action="{{ route('team.lead.list') }}" method="post" id="filters-form">
                    @csrf
                    <div class="accordion text-sm" id="accordionExample">
                        @if ($auth_user->role_id == 4)
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="btn btn-block btn-sm btn-secondary text-left text-bold text-light"
                                        type="button" data-bs-toggle="collapse" data-bs-target="#collapse41"
                                        aria-expanded="true" aria-controls="collapse41">Lead assigned to Rm</button>
                                </h2>
                                <div id="collapse41"
                                    class="accordion-collapse collapse {{ isset($filter_params['team_members']) ? 'show' : '' }}"
                                    data-bs-parent="#accordionExample">
                                    <div class="accordion-body pl-2 pb-4">
                                        @foreach ($getRm as $rm)
                                            <div class="custom-control custom-radio my-1">
                                                <input class="custom-control-input" type="radio"
                                                    id="team_member_{{ $rm->id }}" name="team_members"
                                                    value="{{ $rm->id }}"
                                                    {{ isset($filter_params['team_members']) && $filter_params['team_members'] == $rm->id ? 'checked' : '' }}>
                                                <label for="team_member_{{ $rm->id }}"
                                                    class="custom-control-label">{{ $rm->name }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
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
                        @if ($auth_user->role_id == 4)
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="btn btn-block btn-sm btn-secondary text-left text-bold text-light"
                                        type="button" data-bs-toggle="collapse" data-bs-target="#collapse4"
                                        aria-expanded="true" aria-controls="collapse4">Has RM Message?</button>
                                </h2>
                                <div id="collapse4"
                                    class="accordion-collapse collapse {{ isset($filter_params['has_rm_message']) ? 'show' : '' }}"
                                    data-bs-parent="#accordionExample">
                                    <div class="accordion-body pl-2 pb-4">
                                        <div class="custom-control custom-radio my-1">
                                            <input class="custom-control-input" type="radio"
                                                id="has_rm_message_no_radio" name="has_rm_message" value="no"
                                                {{ isset($filter_params['has_rm_message']) && $filter_params['has_rm_message'] == 'no' ? 'checked' : '' }}>
                                            <label for="has_rm_message_no_radio" class="custom-control-label">No</label>
                                        </div>
                                        <div class="custom-control custom-radio my-1">
                                            <input class="custom-control-input" type="radio"
                                                id="has_rm_message_yes_radio" name="has_rm_message" value="yes"
                                                {{ isset($filter_params['has_rm_message']) && $filter_params['has_rm_message'] == 'yes' ? 'checked' : '' }}>
                                            <label for="has_rm_message_yes_radio" class="custom-control-label">Yes</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="btn btn-block btn-sm btn-secondary text-left text-bold text-light"
                                    type="button" data-bs-toggle="collapse" data-bs-target="#collapse5"
                                    aria-expanded="true" aria-controls="collapse5">Event Date</button>
                            </h2>
                            <div id="collapse5"
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
                        <a href="{{ route('team.lead.list') }}" type="submit"
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
        var isUserRole4 = @json($auth_user->role_id == 4);
        var dashfilters = @json($dashfilters);
        const data_url = `{{ route('team.lead.list.ajax') }}?{!! $filter !!}`;
        if(isUserRole4){
            var roleID = @json($auth_user->role_id);
        var columnsConfig = [null, null, null, null, null, null, null, null, null, null, null, null, null, null,];
        if(roleID == 4) {
            orderConfig = [[14, 'desc']];
        columnsConfig.push({target: 14, data: 17 });
        }
            $(document).ready(function() {
            var dataTable;
            if (dashfilters) {
                $('#serverTable').DataTable({
                    pageLength: 10,
                    processing: true,
                    loading: true,
                    language: {
                        search: "_INPUT_", // Removes the 'Search' field label
                        searchPlaceholder: "Type here to search..", // Placeholder for the search box
                        // processing: `<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>`, // loader
                    },
                    serverSide: true,
                    ajax: {
                        url: data_url,
                    },
                    columns: columnsConfig,
                    order: orderConfig,
                    columnDefs: [{
                        targets: 0,
                        orderable: false
                    },{
                        targets: 15,
                        orderable: false
                    },
                ],
                    rowCallback: function(row, data, index) {
                        const td_elements = row.querySelectorAll('td');
                        if (isUserRole4) {
                            td_elements[0].innerHTML =
                                `<i class="fa fa-arrow-rotate-right"></i><span class="mx-1">${data[16]}</span><br/><input type="checkbox" class="forward_lead_checkbox" value="${data[3]}">`
                        } else {
                            td_elements[0].innerHTML =
                                `<input type="checkbox" class="forward_lead_checkbox" value="">`;
                        }

                            td_elements[1].innerText = data[0];
                            td_elements[3].innerText = moment(data[1]).format("DD-MMM-YYYY hh:mm a")?? 'N/A';
                            td_elements[4].innerText = data[2] ?? 'N/A';
                            td_elements[2].innerText = data[9] ? data[9] : 'N/A';

                            td_elements[6].innerText = data[10] ?? 'N/A';

                            td_elements[7].innerText = data[4] ? moment(data[4]).format("DD-MMM-YYYY") :
                                'N/A';
                            if (data[6] == 1) {
                                td_elements[8].innerHTML =
                                    `<span class="badge badge-success">Contacted</span>`;
                            } else {
                                td_elements[8].innerHTML =
                                    `<span class="badge badge-danger">Not-Contacted</span>`;
                            }


                            if (data[7] == 1) {
                                row.style.background = "#3636361f";
                            }
                            if (data[17] === 1) {
                                td_elements[5].innerHTML =
                                    `<div class="d-flex"><div>${data[3]} </div> &nbsp;&nbsp;&nbsp;<i class="fa-brands fa-square-whatsapp" onclick="handle_whatsapp_msg(${data[3]})" id="what_id-${data[3]}" style="font-size: 25px; color: green;"></i></div>`;
                            } else {
                                td_elements[5].innerHTML =
                                    `<div class="d-flex"><div>${data[3]} </div>&nbsp;&nbsp;&nbsp;<i class="fab fa-whatsapp" onclick="handle_whatsapp_msg(${data[3]})" style="font-size: 25px; color: green;"></i></div>`;
                            }



                            row.style.background = data[8];
                            td_elements[9].innerText = data[19] ?? 'N/A';
                            td_elements[10].innerHTML = data[11] ?? 'N/A';
                            td_elements[11].innerHTML = data[12] ?? 'N/A';
                            td_elements[12].innerHTML = data[13] ?? 'N/A';
                            td_elements[14].innerHTML = data[5] ?? 'N/A';
                            if (data[15] != null) {
                                last_forward_by = data[15].replace('&lt;', '<');
                                last_forward_by = last_forward_by.replace('&gt;', '>');
                            } else {
                                last_forward_by = 'N/A';
                            }
                            td_elements[13].innerHTML = last_forward_by;
                            td_elements[15].innerHTML =
                                `<button onclick="handle_get_forward_info(${data[0]})" class="btn mx-2 p-0 px-2 btn-info d-flex align-items-center" title="Forward info" style="column-gap: 5px;"><i class="fa fa-share-alt" style="font-size: 15px;"></i>${data[18]}</button>`


                        for (let i = 1; i < 12; i++) {
                            if (i !== 5 && td_elements[i]) {
                                td_elements[i].style.cursor = "pointer";
                                td_elements[i].setAttribute('onclick', `handle_view_lead(${data[0]})`);
                            }
                        }
                    }
                });
            } else {
                dataTable = $('#serverTable').DataTable({
                    pageLength: 10,
                    processing: true,
                    loading: true,
                    language: {
                        search: "_INPUT_",
                        searchPlaceholder: "Type here to search..",
                    },
                    serverSide: true,
                    ajax: {
                        url: "{{ route('team.lead.list.ajax') }}",
                        data: function(d) {
                            var formData = $('#filters-form').serializeArray();
                            formData.forEach(function(item) {
                                d[item.name] = item.value;
                            });
                        },
                    },
                    columns: columnsConfig,
                    order: orderConfig,
                    columnDefs: [{
                        targets: 0,
                        orderable: false
                    },{
                        targets: 15,
                        orderable: false
                    },
                ],
                    rowCallback: function(row, data, index) {
                        const td_elements = row.querySelectorAll('td');
                        if (isUserRole4) {
                            td_elements[0].innerHTML =
                                `<i class="fa fa-arrow-rotate-right"></i><span class="mx-1">${data[16]}</span><br/><input type="checkbox" class="forward_lead_checkbox" value="${data[3]}">`
                        } else {
                            td_elements[0].innerHTML =
                                `<input type="checkbox" class="forward_lead_checkbox" value="">`;
                        }

                            td_elements[1].innerText = data[0];
                            td_elements[3].innerText = moment(data[1]).format("DD-MMM-YYYY hh:mm a")?? 'N/A';
                            td_elements[4].innerText = data[2] ?? 'N/A';
                            td_elements[2].innerText = data[9] ? data[9] : 'N/A';

                            td_elements[6].innerText = data[10] ?? 'N/A';

                            td_elements[7].innerText = data[4] ? moment(data[4]).format("DD-MMM-YYYY") :
                                'N/A';
                            if (data[6] == 1) {
                                td_elements[8].innerHTML =
                                    `<span class="badge badge-success">Contacted</span>`;
                            } else {
                                td_elements[8].innerHTML =
                                    `<span class="badge badge-danger">Not-Contacted</span>`;
                            }


                            if (data[7] == 1) {
                                row.style.background = "#3636361f";
                            }
                            if (data[17] === 1) {
                                td_elements[5].innerHTML =
                                    `<div class="d-flex"><div>${data[3]} </div> &nbsp;&nbsp;&nbsp;<i class="fa-brands fa-square-whatsapp" onclick="handle_whatsapp_msg(${data[3]})" id="what_id-${data[3]}" style="font-size: 25px; color: green;"></i></div>`;
                            } else {
                                td_elements[5].innerHTML =
                                    `<div class="d-flex"><div>${data[3]} </div>&nbsp;&nbsp;&nbsp;<i class="fab fa-whatsapp" onclick="handle_whatsapp_msg(${data[3]})" style="font-size: 25px; color: green;"></i></div>`;
                            }



                            row.style.background = data[8];
                            td_elements[9].innerText = data[19] ?? 'N/A';
                            td_elements[10].innerHTML = data[11] ?? 'N/A';
                            td_elements[11].innerHTML = data[12] ?? 'N/A';
                            td_elements[12].innerHTML = data[13] ?? 'N/A';
                            td_elements[14].innerHTML = data[5] ?? 'N/A';
                            if (data[15] != null) {
                                last_forward_by = data[15].replace('&lt;', '<');
                                last_forward_by = last_forward_by.replace('&gt;', '>');
                            } else {
                                last_forward_by = 'N/A';
                            }
                            td_elements[13].innerHTML = last_forward_by;
                            td_elements[15].innerHTML =
                                `<button onclick="handle_get_forward_info(${data[0]})" class="btn mx-2 p-0 px-2 btn-info d-flex align-items-center" title="Forward info" style="column-gap: 5px;"><i class="fa fa-share-alt" style="font-size: 15px;"></i>${data[18]}</button>`


                        for (let i = 1; i < 12; i++) {
                            if (i !== 5 && td_elements[i]) {
                                td_elements[i].style.cursor = "pointer";
                                td_elements[i].setAttribute('onclick', `handle_view_lead(${data[0]})`);
                            }
                        }
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
            console.log(selectedValues);
            let phonenum = document.getElementById('phone_inp_id_m');
            phonenum.value = selectedValues;
            if (selectedValues.length > 0) {
                manageWhatsappChatModal.show();
            } else {
                toastr.info("Select the lead's which you want to send messages.");
            }
        }

        function handle_view_lead(lead_id) {
            window.open(`{{ route('team.lead.view') }}/${lead_id}`);
        }

        function handle_get_forward_info(lead_id) {
            fetch(`{{ route('team.lead.getForwardInfo') }}/${lead_id}`).then(response => response.json()).then(data => {
                const forward_info_table_body = document.getElementById('forward_info_table_body');
                const modal = new bootstrap.Modal("#leadForwardedMemberInfo")
                forward_info_table_body.innerHTML = "";
                if (data.success == true) {
                    last_forwarded_info_paragraph.innerText = data.last_forwarded_info;
                    if (data.lead_forwards.length > 0) {
                        let i = 1;
                        for (let item of data.lead_forwards) {
                            let tr = document.createElement('tr');
                            let tds = `<td>${i}</td>
                        <td>${item.name}</td>
                        <td>${item.venue_name}</td>
                        <td>${moment(item.lead_forwarded_at).format("DD-MMM-YYYY hh:mm a")}</td>`;

                            tr.innerHTML = tds;
                            forward_info_table_body.appendChild(tr);
                            i++;
                        }
                    } else {
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
        }else{

            // vm start
            $(document).ready(function() {
            var dataTable;
            if (dashfilters) {
                $('#serverTable').DataTable({
                    pageLength: 10,
                    processing: true,
                    loading: true,
                    language: {
                        search: "_INPUT_", // Removes the 'Search' field label
                        searchPlaceholder: "Type here to search..", // Placeholder for the search box
                        // processing: `<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>`, // loader
                    },
                    serverSide: true,
                    ajax: {
                        url: data_url,
                    },
                    order: [
                        [1, 'desc']
                    ],
                    columnDefs: [{
                        targets: 0,
                        orderable: false
                    }],
                    rowCallback: function(row, data, index) {
                        const td_elements = row.querySelectorAll('td');

                            td_elements[0].innerHTML =
                                `<input type="checkbox" class="forward_lead_checkbox" value="">`;

                            td_elements[1].innerText = data[0];
                        td_elements[3].innerText = data[2] ?? 'N/A';
                            td_elements[2].innerText = moment(data[1]).format("DD-MMM-YYYY hh:mm a");
                            td_elements[4].innerText = data[3];
                            td_elements[5].innerText = data[4] ? moment(data[4]).format("DD-MMM-YYYY") : 'N/A';
                            if (data[6] == 1) {
                                td_elements[7].innerHTML =
                                    `<span class="badge badge-success">Contacted</span>`;
                            } else {
                                td_elements[7].innerHTML =
                                    `<span class="badge badge-danger">Not-Contacted</span>`;
                            }
                            td_elements[6].innerText = data[5];
                            if (data[7] == 1) {
                                row.style.background = "#3636361f";
                            }

                        for (let i = 1; i < 7; i++) {
                            if (i !== 4 && td_elements[i]) {
                                td_elements[i].style.cursor = "pointer";
                                td_elements[i].setAttribute('onclick', `handle_view_lead(${data[0]})`);
                            }
                        }
                    }
                });
            } else {
                dataTable = $('#serverTable').DataTable({
                    pageLength: 10,
                    processing: true,
                    loading: true,
                    language: {
                        search: "_INPUT_",
                        searchPlaceholder: "Type here to search..",
                    },
                    serverSide: true,
                    ajax: {
                        url: "{{ route('team.lead.list.ajax') }}",
                        data: function(d) {
                            var formData = $('#filters-form').serializeArray();
                            formData.forEach(function(item) {
                                d[item.name] = item.value;
                            });
                        },
                    },
                    order: [
                        [1, 'desc']
                    ],
                    columnDefs: [{

                        targets: 0,
                        orderable: false
                    }],
                    rowCallback: function(row, data, index) {
                        const td_elements = row.querySelectorAll('td');

                            td_elements[0].innerHTML =
                                `<input type="checkbox" class="forward_lead_checkbox" value="">`;

                            td_elements[1].innerText = data[0];
                        td_elements[3].innerText = data[2] ?? 'N/A';
                            td_elements[2].innerText = moment(data[1]).format("DD-MMM-YYYY hh:mm a");
                            td_elements[4].innerText = data[3];
                            td_elements[5].innerText = data[4] ? moment(data[4]).format("DD-MMM-YYYY") : 'N/A';
                            if (data[6] == 1) {
                                td_elements[7].innerHTML =
                                    `<span class="badge badge-success">Contacted</span>`;
                            } else {
                                td_elements[7].innerHTML =
                                    `<span class="badge badge-danger">Not-Contacted</span>`;
                            }
                            td_elements[6].innerText = data[5];
                            if (data[7] == 1) {
                                row.style.background = "#3636361f";
                            }

                        for (let i = 1; i < 7; i++) {
                                td_elements[i].style.cursor = "pointer";
                                td_elements[i].setAttribute('onclick', `handle_view_lead(${data[0]})`);
                        }
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
        function handle_view_lead(lead_id) {
            window.open(`{{ route('team.lead.view') }}/${lead_id}`);
        }

        }
    </script>
@endsection
