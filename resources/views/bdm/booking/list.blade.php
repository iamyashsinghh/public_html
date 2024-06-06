@extends('bdm.layouts.app')
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
        $filter_start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
        $filter_end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
    @endphp
    <div class="content-wrapper pb-5">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">{{ $page_heading }}</h1>
                    </div>
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
                                <th class="">Business Name</th>
                                <th class="">Business Category</th>
                                <th class="text-nowrap">Vendor Name</th>
                                <th class="text-nowrap">Mobile</th>
                                <th class="text-nowrap">Lead Status</th>
                                <th class="text-nowrap">Package Name</th>
                                <th class="text-nowrap">Booking Date</th>
                                <th class="">Payment Method</th>
                                <th class="">Amount</th>
                                <th class="text-nowrap">Created At</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </section>
        <aside class="control-sidebar control-sidebar-dark" style="display: none;">
            <div class="p-3 control-sidebar-content">
                <h5>Task Filters</h5>
                <hr class="mb-2">
                <form action="{{ route('bdm.booking.list') }}" method="post" id="filters-form">
                    @csrf
                    <div class="accordion text-sm" id="accordionExample">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="btn btn-block btn-sm btn-secondary text-left text-bold text-light"
                                    type="button" data-bs-toggle="collapse" data-bs-target="#collapse1"
                                    aria-expanded="true" aria-controls="collapse1">Package Name</button>
                            </h2>
                            <div id="collapse1"
                                class="accordion-collapse collapse {{ isset($filter_params['package_name']) ? 'show' : '' }}"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body pl-2 pb-4">
                                    <div class="custom-control custom-checkbox my-1">
                                        <input class="custom-control-input" type="checkbox" id="package_name_elite"
                                            name="package_name[]" value="Elite Package"
                                            {{ isset($filter_params['package_name']) && in_array('Elite Package', $filter_params['package_name']) ? 'checked' : '' }}>
                                        <label for="package_name_elite" class="custom-control-label">Elite Package</label>
                                    </div>
                                    <div class="custom-control custom-checkbox my-1">
                                        <input class="custom-control-input" type="checkbox" id="package_name_gold"
                                            name="package_name[]" value="Gold Package"
                                            {{ isset($filter_params['package_name']) && in_array('Gold Package', $filter_params['package_name']) ? 'checked' : '' }}>
                                        <label for="package_name_gold" class="custom-control-label">Gold Package</label>
                                    </div>
                                    <div class="custom-control custom-checkbox my-1">
                                        <input class="custom-control-input" type="checkbox" id="package_name_premium"
                                            name="package_name[]" value="Premium Listing"
                                            {{ isset($filter_params['package_name']) && in_array('Premium Listing', $filter_params['package_name']) ? 'checked' : '' }}>
                                        <label for="package_name_premium" class="custom-control-label">Premium
                                            Listing</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="btn btn-block btn-sm btn-secondary text-left text-bold text-light"
                                    type="button" data-bs-toggle="collapse" data-bs-target="#collapse2"
                                    aria-expanded="true" aria-controls="collapse2">Booking Date</button>
                            </h2>
                            <div id="collapse2"
                                class="accordion-collapse collapse {{ isset($filter_params['booking_date_from']) ? 'show' : '' }}"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body pl-2 pb-4">
                                    <div class="form-group">
                                        <label for="booking_date_from_inp">From</label>
                                        <input type="date" class="form-control" id="booking_date_from_inp"
                                            name="booking_date_from"
                                            value="{{ isset($filter_params['booking_date_from']) ? $filter_params['booking_date_from'] : '' }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="booking_date_to_inp">To</label>
                                        <input type="date" class="form-control" id="booking_date_to_inp"
                                            name="booking_date_to"
                                            value="{{ isset($filter_params['booking_date_to']) ? $filter_params['booking_date_to'] : '' }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="btn btn-block btn-sm btn-secondary text-left text-bold text-light"
                                    type="button" data-bs-toggle="collapse" data-bs-target="#collapse5"
                                    aria-expanded="true" aria-controls="collapse5">Payment Method</button>
                            </h2>
                            <div id="collapse5"
                                class="accordion-collapse collapse {{ isset($filter_params['payment_method']) ? 'show' : '' }}"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body pl-2 pb-4">
                                    <div class="custom-control custom-checkbox my-1">
                                        <input class="custom-control-input" type="checkbox" id="payment_method_card"
                                            name="payment_method[]" value="Card"
                                            {{ isset($filter_params['payment_method']) && in_array('Card', $filter_params['package_name']) ? 'checked' : '' }}>
                                        <label for="payment_method_card" class="custom-control-label">Card</label>
                                    </div>
                                    <div class="custom-control custom-checkbox my-1">
                                        <input class="custom-control-input" type="checkbox" id="payment_method_Cash"
                                            name="payment_method[]" value="Cash"
                                            {{ isset($filter_params['payment_method']) && in_array('Cash', $filter_params['package_name']) ? 'checked' : '' }}>
                                        <label for="payment_method_Cash" class="custom-control-label">Cash</label>
                                    </div>
                                    <div class="custom-control custom-checkbox my-1">
                                        <input class="custom-control-input" type="checkbox" id="payment_method_Cheque"
                                            name="payment_method[]" value="Cheque"
                                            {{ isset($filter_params['payment_method']) && in_array('Cheque', $filter_params['package_name']) ? 'checked' : '' }}>
                                        <label for="payment_method_Cheque" class="custom-control-label">Cheque</label>
                                    </div>
                                    <div class="custom-control custom-checkbox my-1">
                                        <input class="custom-control-input" type="checkbox" id="payment_method_qrcode"
                                            name="payment_method[]" value="QR-Code"
                                            {{ isset($filter_params['payment_method']) && in_array('QR-Code', $filter_params['package_name']) ? 'checked' : '' }}>
                                        <label for="payment_method_qrcode" class="custom-control-label">QR-Code</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="my-5">
                        <button type="submit" class="btn btn-sm text-light btn-block"
                            style="background-color: var(--wb-renosand);">Apply</button>
                        <a href="{{ route('bdm.booking.list') }}" type="submit"
                            class="btn btn-sm btn-secondary btn-block">Reset</a>
                    </div>
                </form>
            </div>
        </aside>
    </div>
@endsection
@section('footer-script')
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
        const data_url = `{{ route('bdm.booking.list.ajax') }}?{!! $filter !!}`;
        var dashfilters = @json($dashfilters);
        $(document).ready(function() {
            var dataTable;
            if (dashfilters) {
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
                            'X-CSRF-TOKEN': "{{ csrf_token() }}",
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
                            name: "business_name",
                            data: "business_name",
                        },
                        {
                            targets: 3,
                            name: "business_cat",
                            data: "business_cat",
                        },
                        {
                            targets: 4,
                            name: "name",
                            data: "name",
                        },
                        {
                            targets: 5,
                            name: "mobile",
                            data: "mobile",
                        },
                        {
                            targets: 6,
                            name: "lead_status",
                            data: "lead_status",
                        },
                        {
                            targets: 7,
                            name: "package_name",
                            data: "package_name",
                        },
                        {
                            targets: 8,
                            name: "booking_date",
                            data: "booking_date",
                        },
                        {
                            targets: 9,
                            name: "payment_method",
                            data: "payment_method",
                        },
                        {
                            targets: 10,
                            name: "price",
                            data: "price",
                        },
                        {
                            targets: 11,
                            name: "created_at",
                            data: "created_at",
                        },
                    ],
                    order: [
                        [11, 'asc']
                    ],
                    rowCallback: function(row, data, index) {
                        row.style.cursor = "pointer";
                        row.setAttribute('onclick', `handle_view_lead(${data.lead_id})`);

                        const td_elements = row.querySelectorAll('td');
                        td_elements[1].innerText = moment(data.lead_datetime).format(
                            "DD-MMM-YYYY hh:mm a");
                        td_elements[1].classList.add('text-nowrap');
                        if (data.lead_status == "Done") {
                            td_elements[6].innerHTML =
                            `<span class="badge badge-secondary">Done</span>`;
                        } else {
                            td_elements[6].innerHTML =
                                `<span class="badge badge-success">${data.lead_status}</span>`;
                        }
                        td_elements[8].innerHTML = moment(data.booking_date).format(
                            "DD-MMM-YYYY hh:mm a");
                        td_elements[11].innerText = moment(data.booking_date).format(
                            "DD-MMM-YYYY hh:mm a");


                    }
                });
            } else {
                dataTable = $('#serverTable').DataTable({
                    pageLength: 10,
                    language: {
                        "search": "_INPUT_", // Removes the 'Search' field label
                        "searchPlaceholder": "Type here to search..", // Placeholder for the search box
                        processing: `<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>`, // loader
                    },
                    serverSide: true,
                    loading: true,
                    ajax: {
                        url: "{{ route('bdm.booking.list.ajax') }}",
                        data: function(d) {
                            let formData = $('#filters-form').serializeArray();
                            formData.forEach(function(item) {
                                if (item.name.endsWith('[]')) {
                                    if (!d[item.name]) {
                                        d[item.name] = [];
                                    }
                                    d[item.name].push(item.value);
                                } else {
                                    d[item.name] = item.value;
                                }
                            });
                        },
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}",
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
                            name: "business_name",
                            data: "business_name",
                        },
                        {
                            targets: 3,
                            name: "business_cat",
                            data: "business_cat",
                        },
                        {
                            targets: 4,
                            name: "name",
                            data: "name",
                        },
                        {
                            targets: 5,
                            name: "mobile",
                            data: "mobile",
                        },
                        {
                            targets: 6,
                            name: "lead_status",
                            data: "lead_status",
                        },
                        {
                            targets: 7,
                            name: "package_name",
                            data: "package_name",
                        },
                        {
                            targets: 8,
                            name: "booking_date",
                            data: "booking_date",
                        },
                        {
                            targets: 9,
                            name: "payment_method",
                            data: "payment_method",
                        },
                        {
                            targets: 10,
                            name: "price",
                            data: "price",
                        },
                        {
                            targets: 11,
                            name: "created_at",
                            data: "created_at",
                        },
                    ],
                    order: [
                        [11, 'asc']
                    ],
                    rowCallback: function(row, data, index) {
                        row.style.cursor = "pointer";
                        row.setAttribute('onclick', `handle_view_lead(${data.lead_id})`);

                        const td_elements = row.querySelectorAll('td');
                        td_elements[1].innerText = moment(data.lead_datetime).format(
                            "DD-MMM-YYYY hh:mm a");
                        td_elements[1].classList.add('text-nowrap');
                        if (data.lead_status == "Done") {
                            td_elements[6].innerHTML =
                            `<span class="badge badge-secondary">Done</span>`;
                        } else {
                            td_elements[6].innerHTML =
                                `<span class="badge badge-success">${data.lead_status}</span>`;
                        }
                        td_elements[8].innerHTML = moment(data.booking_date).format(
                            "DD-MMM-YYYY hh:mm a");
                        td_elements[11].innerText = moment(data.booking_date).format(
                            "DD-MMM-YYYY hh:mm a");
                    }
                });
            }
            $('#filters-form').on('submit', function(e) {
                e.preventDefault();
                dataTable.ajax.reload();
                document.querySelector('[data-widget="control-sidebar"]').click();
            });
        });

        function handle_view_lead(forward_id) {
            window.open(`{{ route('bdm.lead.view') }}/${forward_id}#booking_card_container`);
        }
    </script>
@endsection
