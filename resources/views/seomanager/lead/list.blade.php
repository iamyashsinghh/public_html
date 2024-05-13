@extends('seomanager.layouts.app')
@section('header-css')
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
@endsection
@section('title', 'Lead List | SEO Manager')
@section('navbar-right-links')
    <li class="nav-item">
        <a class="nav-link" title="Filters" data-widget="control-sidebar" data-controlsidebar-slide="true"
            href="javascript:void(0);" role="button">
            <i class="fas fa-filter"></i>
        </a>
    </li>
@endsection
@section('main')
    <div class="content-wrapper pb-5">
        <section class="content-header">
            <div class="container-fluid">
                <div class="d-flex justify-content-between mb-2">
                    <h1 class="m-0">Leads Source</h1>
                </div>
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
                                <th class="">Source</th>
                                <th class="text-nowrap">Category</th>
                                <th class="">Preference</th>
                                <th class="">Locality</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </section>
        <div class="modal fade" id="leadForwardedMemberInfo" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header text-sm">
                        <h4 class="modal-title">Forward Information</h4>
                        <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i
                                class="fa fa-times"></i></button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table id="clientTable" class="table text-sm">
                                <thead>
                                    <tr>
                                        <th class="text-nowrap">S.No.</th>
                                        <th class="text-nowrap">Name</th>
                                        <th class="text-nowrap">Venue Name</th>
                                        <th class="text-nowrap">Read Status</th>
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
                <form action="{{ route('seomanager.lead.list') }}" id="filters-form" method="post">
                    @csrf
                    <div class="accordion text-sm" id="accordionExample">
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
                        <a href="{{ route('seomanager.lead.list') }}" type="submit"
                            class="btn btn-sm btn-secondary btn-block">Reset</a>
                    </div>
                </form>
            </div>
        </aside>
    </div>
@endsection
@section('footer-script')
    <script src="//cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
    <script>
        var dataTable;

        const data_url = `{{ route('seomanager.lead.list.ajax') }}`;
        $(document).ready(function() {
            dataTable = $('#serverTable').DataTable({
                pageLength: 10,
                language: {
                    "search": "_INPUT_",
                    "searchPlaceholder": "Type here to search..",
                    processing: `<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>`, // loader
                },
                serverSide: true,
                lengthMenu: [ [10, 25, 50, 100, 200, 500, 1000], [10, 25, 50, 100, 200, 500, 1000] ],
                loading: true,
                ajax: {
                    url: data_url,
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    },
                    method: "get",
                    data: function(d) {
                        $('#filters-form').serializeArray().forEach(function(item) {
                            d[item.name] = item.value;
                        });
                    },
                    dataSrc: "data",
                },
                columns: [
                    {
                        targets: 0,
                        name: "lead_id",
                        data: "lead_id",
                    },
                    {
                        targets: 1,
                        name: "lead_datetime",
                        data: "lead_datetime",
                        render: function(data, type, row) {
                    const date = new Date(data);
                    return date.toLocaleString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit',
                        hour12: true,
                    });
                },
                    },
                    {
                        targets: 2,
                        name: "source",
                        data: "source",
                    },
                    {
                        targets: 3,
                        name: "lead_catagory",
                        data: "lead_catagory",
                    },
                    {
                        targets: 4,
                        name: "preference",
                        data: "preference",
                    },
                    {
                        targets: 5,
                        name: "locality",
                        data: "locality",
                    },
                ],
                order: [[1, 'desc']],
                rowCallback: function(row, data, index) {
                    if(data.is_ad == 1){
                        row.style.backgroundColor = "#4497bb"
                    }
                }
            });
            $('#filters-form').on('submit', function(e) {
                e.preventDefault();
                dataTable.ajax.reload(null, false);
                document.querySelector('[data-widget="control-sidebar"]').click();
            });
        });
    </script>
@endsection
