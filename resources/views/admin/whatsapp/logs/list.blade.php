@extends('admin.layouts.app')
@section('header-css')
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
@endsection
@section('title', 'Whatsapp Bulk Msg Logs')
@section('main')
    <div class="content-wrapper pb-5">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Whatsapp Bulk Msg Logs</h1>
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
                                <th class="text-nowrap">Mobile</th>
                                <th class="text-nowrap">campaign_name</th>
                                <th class="text-nowrap">status</th>
                                <th class="text-nowrap">Created At</th>
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
    <script>
        $(document).ready(function() {
            $('#serverTable').DataTable({
                pageLength: 10,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('whatsapp_chat.logs_ajax') }}",
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    },
                    method: "get",
                    dataSrc: "data",
                },
                columns: [{
                        name: "id",
                        data: "id"
                    },
                    {
                        name: "number",
                        data: "number"
                    },
                    {
                        name: "campaign_name",
                        data: "campaign_name"
                    },
                    {
                        name: "status",
                        data: "status",
                        render: function(data, type, row) {
                            if (data === '0') {
                                return `<span class="badge badge-success">Sent</span>`;
                            } else {
                                return '<span class="badge badge-danger">Not Sent</span>';
                            }
                        }
                    },
                    {
                        name: "created_at",
                        data: "created_at",
                        render: function(data, type, row) {
                            return moment(data).format('YYYY-MM-DD HH:mm:ss');
                        }
                    },
                ],
                order: [
                    [0, 'desc']
                ],
            });
        });
    </script>
@endsection
