@extends('admin.layouts.app')

@section('header-css')
<link rel="stylesheet" href="//cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
@endsection

@section('title', "Whatsapp CRM")

@section('main')
<div class="content-wrapper pb-5">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Whatsapp CRM</h1>
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
                            <th class="text-nowrap">Campaign Name</th>
                            <th class="text-nowrap">Img</th>
                            <th class="text-nowrap">Message</th>
                            <th class="text-nowrap">Status</th>
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
                url: "{{ route('whatsapp_chat.ajax') }}",
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                },
                method: "get",
                dataSrc: "data",
            },
            columns: [
                { name: "id", data: "id" },
                { name: "campaign_name", data: "campaign_name" },
                { name: "img", data: "img", render: function(data, type, row) {
                    return data ? `<img src="${data}" alt="image" style="width: 50px; height: auto;">` : 'No image';
                } },
                { name: "msg", data: "msg" },
                { name: "status", data: "status" ,
                render: function(data, type, row) {
                    var statusHtml = data === "0" ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-secondary">Complete</span>';
                    return statusHtml;
                }},
                { name: "created_at", data: "created_at", render: function(data, type, row) {
                    return moment(data).format('YYYY-MM-DD HH:mm:ss');
                } },
            ],
            order: [[0, 'desc']],
        });
    });
</script>
@endsection
