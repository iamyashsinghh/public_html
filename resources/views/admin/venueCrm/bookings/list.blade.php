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
                <div class="d-flex justify-content-between mb-2">
                    <h1 class="m-0">{{ $page_heading }}</h1>
                </div>
                @if (!isset($filter_params['dashboard_filters']))
                    <div class="filter-container text-center d-none">
                        <form action="{{ route('admin.bookings.list') }}" method="post">
                            @csrf
                            <label for="">Filter by booking date</label>
                            <input type="date" name="booking_from_date"
                                value="{{ isset($filter_params['booking_from_date']) ? $filter_params['booking_from_date'] : '' }}"
                                class="form-control form-control-sm d-inline-block" style="width: unset;" required>
                            <span class="">To:</span>
                            <input type="date" name="booking_to_date"
                                value="{{ isset($filter_params['booking_to_date']) ? $filter_params['booking_to_date'] : '' }}"
                                class="form-control form-control-sm d-inline-block" style="width: unset;">
                            <button type="submit" class="btn text-light btn-sm"
                                style="background-color: var(--wb-dark-red)">Submit</button>
                            <a href="{{ route('admin.bookings.list') }}" class="btn btn-secondary btn-sm">Reset</a>
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
                                <th class="text-nowrap">Booking Date</th>
                                <th class="">Booking Source</th>
                                <th class="">Venue Name</th>
                                <th class="">VM Name</th>
                                <th class="">Name</th>
                                <th class="text-nowrap">Mobile</th>
                                <th class="text-nowrap">Event Name</th>
                                <th class="text-nowrap">Event Date</th>
                                <th class="">PAX</th>
                                <th class="">Total GMV</th>
                                <th class="">Advance Amount</th>
                                <th class="">25% Advance Collected</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </section>
        <aside class="control-sidebar control-sidebar-dark" style="display: none;">
            <div class="p-3 control-sidebar-content">
                <h5>Booking Filters</h5>
                <hr class="mb-2">
                <form action="{{ route('admin.bookings.list') }}" method="post">
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
                                        <label for="checkbox_<?php echo $vm->name; ?>"
                                            class="custom-control-label"><?php echo $vm->name; ?></label>
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
                                    type="button" data-bs-toggle="collapse" data-bs-target="#collapse1"
                                    aria-expanded="true" aria-controls="collapse1">Booking Source</button>
                            </h2>
                            <div id="collapse1"
                                class="accordion-collapse collapse {{ isset($filter_params['booking_source']) ? 'show' : '' }}"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body pl-2 pb-4">
                                    <?php
                                    $sources = ['WB|Team', 'VM|Reference', 'WB|Call', 'Walk-in', 'Other'];
                                    foreach ($sources as $source) {
                                    ?>
                                    <div class="custom-control custom-checkbox my-1">
                                        <input id="checkbox_<?php echo $source; ?>" class="custom-control-input"
                                            type="checkbox" name="booking_source[]" value="<?php echo $source; ?>"
                                            <?php echo isset($filter_params['booking_source']) && in_array($source, $filter_params['booking_source']) ? 'checked' : ''; ?>>
                                        <label for="checkbox_<?php echo $source; ?>"
                                            class="custom-control-label"><?php echo $source; ?></label>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="btn btn-block btn-sm btn-secondary text-left text-bold text-light"
                                    type="button" data-bs-toggle="collapse" data-bs-target="#collapse2"
                                    aria-expanded="true" aria-controls="collapse2">25% Advance Collected</button>
                            </h2>
                            <div id="collapse2"
                                class="accordion-collapse collapse {{ isset($filter_params['quarter_advance_collected']) ? 'show' : '' }}"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body pl-2 pb-4">
                                    <div class="custom-control custom-radio my-1">
                                        <input id="checkbox5" class="custom-control-input" type="radio"
                                            name="quarter_advance_collected" value="0"
                                            {{ isset($filter_params['quarter_advance_collected']) && $filter_params['quarter_advance_collected'] == '0' ? 'checked' : '' }}>
                                        <label for="checkbox5" class="custom-control-label">Not Collected</label>
                                    </div>
                                    <div class="custom-control custom-radio my-1">
                                        <input id="checkbox6" class="custom-control-input" type="radio"
                                            name="quarter_advance_collected" value="1"
                                            {{ isset($filter_params['quarter_advance_collected']) && $filter_params['quarter_advance_collected'] == '1' ? 'checked' : '' }}>
                                        <label for="checkbox6" class="custom-control-label">Completed</label>
                                    </div>
                                </div>
                            </div>
                        </div>
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
                                    type="button" data-bs-toggle="collapse" data-bs-target="#collapse6"
                                    aria-expanded="true" aria-controls="collapse6">Booking date</button>
                            </h2>
                            <div id="collapse6"
                                class="accordion-collapse collapse {{ isset($filter_params['booking_from_date']) ? 'show' : '' }}"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body pl-2 pb-4">
                                    <div class="form-group">
                                        <label for="booking_from_date_inp">From</label>
                                        <input type="date" class="form-control" id="booking_from_date_inp"
                                            name="booking_from_date"
                                            value="{{ isset($filter_params['booking_from_date']) ? $filter_params['booking_from_date'] : '' }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="booking_to_date">To</label>
                                        <input type="date" class="form-control" id="booking_to_date"
                                            name="booking_to_date"
                                            value="{{ isset($filter_params['booking_to_date']) ? $filter_params['booking_to_date'] : '' }}">
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
                    </div>
                    <div class="my-5">
                        <button type="submit" class="btn btn-sm text-light btn-block"
                            style="background-color: var(--wb-renosand);">Apply</button>
                        <a href="{{ route('admin.bookings.list') }}" type="submit"
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
        if (isset($filter_params['booking_source']) && is_array($filter_params['booking_source'])) {
            foreach ($filter_params['booking_source'] as $source) {
                $filters[] = 'booking_source[]=' . urlencode($source);
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
        if (isset($filter_params['quarter_advance_collected'])) {
            $filters[] = 'quarter_advance_collected=' . $filter_params['quarter_advance_collected'];
        }
        if (isset($filter_params['booking_from_date'])) {
            $filters[] =
                'booking_from_date=' .
                $filter_params['booking_from_date'] .
                '&booking_to_date=' .
                $filter_params['booking_to_date'];
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
    <script src="//cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="//cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="//cdn.datatables.net/buttons/2.3.6/js/buttons.flash.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="//cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
    <script src="//cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
    <script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
    <script>
        const data_url = `{{ route('admin.bookings.list.ajax') }}?{!! $filter !!}`;

        $(document).ready(function() {
            $('#serverTable').DataTable({
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                processing: true,
                processing: true,
                dom: '<""lfB>rtip',
                buttons: [{
                    extend: 'excelHtml5',
                    text: '<i class="fas fa-file-excel"></i> Export to Excel',
                    className: 'btn text-light btn-sm buttons-prints mx-1',
                }],
                loading: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Type here to search..", // Placeholder for the search box
                },
                serverSide: true,
                ajax: {
                    url: data_url
                },
                order: [
                    [1, 'desc']
                ],
                rowCallback: function(row, data, index) {
                    const td_elements = row.querySelectorAll('td');
                    td_elements[1].innerText = moment(data[1]).format("DD-MMM-YYYY hh:mm a");
                    td_elements[2].innerText = data[2] ?? 'N/A';
                    td_elements[7].innerText = data[7] ?? 'N/A';
                    td_elements[8].innerText = data[8] ? moment(data[8]).format("DD-MMM-YYYY") : 'N/A';
                    td_elements[9].innerText = data[9];
                    td_elements[10].innerText = Intl.NumberFormat('en-US').format(data[10]);
                    td_elements[11].innerText = Intl.NumberFormat('en-US').format(data[11]);
                    if (data[12] == 0) {
                        td_elements[12].innerHTML =
                            `<span class="badge badge-danger">Not Completed</span>`;
                    } else {
                        td_elements[12].innerHTML =
                            `<span class="badge badge-success">Completed</span>`;
                    }

                    for (let i = 0; i < 12; i++) {
                        if (td_elements[i]) {
                            td_elements[i].style.cursor = "pointer";
                            td_elements[i].setAttribute('onclick', `handle_view_lead(${data[0]})`);
                        }
                    }
                }
            });
        });

        function handle_view_lead(lead_id) {
            window.open(`{{ route('admin.lead.view') }}/${lead_id}#bookings_card_container`);
        }
    </script>
@endsection
