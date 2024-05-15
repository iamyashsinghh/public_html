@extends('admin.layouts.app')
@section('header-css')
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
@endsection
@section('title', 'Activity Logs')
@section('main')
    <div class="content-wrapper pb-5">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">All Activity Logs</h1>
                    </div>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="container-fluid">
                <div class="table-responsive">
                    <table id="serverTable" class="table text-sm">
                        <thead class="sticky_head bg-light" style="position: sticky; top: 0;">
                            <tr>
                                <th class="text-nowrap">ID</th>
                                <th class="text-nowrap">Description</th>
                                <th class="text-nowrap">Event</th>
                                <th class="text-nowrap">Modal Type</th>
                                <th class="text-nowrap">Subject Id</th>
                                <th class="text-nowrap">Properties</th>
                                <th class="text-nowrap">Created At</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </section>
    </div>
    <div class="modal fade" id="seeActivityPropertyModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="seeActivityHeading">Add Campaign</h4>
                    <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i
                            class="fa fa-times"></i></button>
                </div>
                <div class="modal-body">
                </div>
            </div>
        </div>
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
                    url: "{{ route('admin.activity.logs_ajax') }}",
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
                        name: "description",
                        data: "description"
                    },
                    {
                        name: "event",
                        data: "event"
                    },
                    {
                        name: "subject_type",
                        data: "subject_type"
                    },
                    {
                        name: "subject_id",
                        data: "subject_id",
                        render: function(data, type, row) {
                                return `<span class="badge badge-success p-2">${data}</span>`;
                        }
                    },
                    {
                        name: "id",
                        data: "id"
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
                rowCallback: function(row, data, index) {
                const td_elements = row.querySelectorAll('td');
                td_elements[5].innerHTML = `<button class="btn btn-sm text-light" onclick="see_activity(${data.id})"
                        style="background-color: var(--wb-renosand);">See Properties</button>`;
                }

            });
        });

        function see_activity(id = 0) {
    const baseUrl = "{{ route('admin.activity.logs_ajax_property', ['id' => 'TEMP_ID']) }}";
    const urlWithId = baseUrl.replace('TEMP_ID', id);

    const seeActivityPropertyModal = document.getElementById('seeActivityPropertyModal');
    const modal = new bootstrap.Modal(seeActivityPropertyModal);
    fetch(urlWithId)
        .then(response => response.json())
        .then(data => {
            const modalBody = seeActivityPropertyModal.querySelector('.modal-body');
            const prettyPrintedJson = JSON.stringify(data, null, 4); // Indent with 4 spaces for readability
            const formattedJson = `<pre>${prettyPrintedJson}</pre>`; // Use <pre> to preserve spaces
            modalBody.innerHTML = formattedJson;
        })
        .catch(error => {
            console.error('Error:', error);
        });
    modal.show();
}

    </script>
@endsection
