@extends('nonvenue.layouts.app')
@section('header-css')
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
@endsection
@section('title', 'Vendor Help')
@section('main')
    <div class="content-wrapper pb-5">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">{{$page_heading}}</h1>
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
                                <th class="text-nowrap">ID</th>
                                <th class="text-nowrap">Lead Id</th>
                                <th class="text-nowrap">Name</th>
                                <th class="text-nowrap">Mobile</th>
                                <th class="text-nowrap">Event Date</th>
                                <th class="text-nowrap">Lead Status</th>
                                <th class="text-nowrap">Created At</th>
                                <th class="text-nowrap">Created By</th>
                                <th class="text-nowrap">Status</th>
                                <th class="text-nowrap">Responsed At</th>
                                <th class="text-nowrap">Responsed By</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </section>
    </div>
@endsection
@section('footer-script')
    <script src="//cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
    @php
        $filter = '';
        // $filter_params = '';

        if (isset($filter_params['dashboard_filters'])) {
            $filter = 'dashboard_filters=' . $filter_params['dashboard_filters'];
        }
        $dashfilters = isset($filter_params['dashboard_filters']) ? $filter_params['dashboard_filters'] : null;
    @endphp
    <script>
        $(document).ready(function() {
            $('#serverTable').DataTable({
                pageLength: 10,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('nonvenue.vendor.help.ajax') }}",
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    },
                    method: "get",
                    data: function(d) {
                        d.dashboard_filters = {!! json_encode($dashfilters) !!};
                    },
                    dataSrc: "data",
                },
                columns: [{
                        name: "id",
                        data: "id"
                    },
                    {
                        name: "lead_id",
                        data: "lead_id"
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
                        name: "event_date",
                        data: "event_date",
                                                render: function(data, type, row) {
                            return moment(data).format('YYYY-MM-DD');
                        }

                    },
                    {
                        name: "lead_status",
                        data: "lead_status"
                    },
                    {
                        name: "created_at",
                        data: "created_at",
                        render: function(data, type, row) {
                            return moment(data).format('YYYY-MM-DD hh:mm:ss a');
                        }
                    },
                    {
                        name: "created_by_name",
                        data: "created_by_name"
                    },
                    {
                        name: "status",
                        data: "status",
                        render: function(data, type, row) {
                            if (data == 1) {
                                return '<span class="badge badge-success">Responded</span>';
                            } else if (data == 0) {
                                return '<span class="badge badge-danger">Waiting For Response</span>';
                            } else {
                                return '<span class="badge badge-danger">Invalid</span>';
                            }
                        }
                    },
                    {
                        name: "done_datetime",
                        data: "done_datetime",
                        render: function(data, type, row) {
                            return moment(data).format('YYYY-MM-DD hh:mm:ss a');
                        }
                    },
                    {
                        name: "done_by_name",
                        data: "done_by_name"
                    },
                ],
                order: [
                    [0, 'desc']
                ],
                rowCallback: function(row, data, index) {
                    const td_elements = row.querySelectorAll('td');
                    td_elements[7].innerText = `${data.created_by_name} -- ${data.category_name}`;
                    for (let i = 1; i < 7; i++) {
                        td_elements[i].style.cursor = "pointer";
                        td_elements[i].setAttribute('onclick',
                            `handle_view_lead(${data.lead_id})`);
                    }
                }
            });
        });

        function handle_view_lead(lead_id) {
            window.open(`{{ route('nonvenue.lead.view') }}/${lead_id}#get_nvrm_help_messages_card`);
        }
    </script>
@endsection
