@extends('admin.layouts.app')

@section('header-css')
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
@endsection

@section('title', 'Whatsapp CRM')

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
                                <th class="text-nowrap">Template Name</th>
                                <th class="text-nowrap">Img</th>
                                <th class="text-nowrap">Message</th>
                                <th class="text-nowrap">Status</th>
                                <th class="text-nowrap">Created At</th>
                                <th class="text-nowrap">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </section>
        <!-- Modal -->
        <div class="modal fade" id="myModalTemp" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Modal Title</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Your modal content goes here.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Save changes</button>
                    </div>
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
                    url: "{{ route('whatsapp_chat.ajax_templates') }}",
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
                        name: "temp_name",
                        data: "temp_name"
                    },
                    {
                        name: "img",
                        data: "img",
                        render: function(data, type, row) {
                            return data ?
                                `<img src="${data}" alt="image" style="width: 50px; height: auto;">` :
                                'No image';
                        }
                    },
                    {
                        name: "msg",
                        data: "msg"
                    },
                    {
                        name: "status",
                        data: "status",
                        render: function(data, type, row) {
                            var statusHtml = `<span class="badge badge-success">${data}</span>`;
                            return statusHtml;
                        }
                    },
                    {
                        name: "created_at",
                        data: "created_at",
                        render: function(data, type, row) {
                            return moment(data).format('YYYY-MM-DD HH:mm:ss');
                        }
                    },
                    {
                        name: "action",
                        data: "temp_name",
                        render: function(data, type, row) {
                            var actionHtml =
                                `<a href='javascript:void(0);' onclick="handle_image('${data}')" class="btn btn-sm btn-primary">Edit</a>`;
                            return actionHtml;
                        }
                    },
                ],
                order: [
                    [0, 'desc']
                ],
            });
        });

        function openModal() {
            var myModal = new bootstrap.Modal(document.getElementById('myModal'), {});
            myModal.show();
        }

        function handle_image(tempName) {
            var image_data = '';
            var baseUrl = "{{ rtrim(route('get.whatsapp.temp.img', ''), '/') }}";
            var fullUrl = `${baseUrl}/${encodeURIComponent(tempName)}`;
            fetch(fullUrl)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        return response.json();
                    } else {
                        return response.text();
                    }
                })
                .then(data => {
                    image_data = data;
                    handle_set_image(data, tempName)
                    console.log(data);
                })
                .catch(error => {
                    console.error('Error fetching the image:', error);
                });
        }
function handle_set_image(image_url, tempName) {
    const existingModal = document.getElementById('viewImageModal');
    if (existingModal) {
        existingModal.remove();
    }
    var image_change_request_url = "{{ route('set.whatsapp.temp.img') }}";
    const defaultImageUrl = "{{ asset('images/default-user.png') }}";
    if(!image_url) {
        image_url = defaultImageUrl;
    }
    const div = document.createElement('div');
    div.classList = "modal fade";
    div.id = "viewImageModal";
    div.setAttribute("tabindex", "-1");
    const modal_elem = `
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Image</h4>
                    <button type="button" class="btn text-secondary" onclick="handle_remove_modal('viewImageModal')" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                </div>
                <div class="modal-body text-center">
                    <img src="${image_url}" class="rounded img-fluid" style="min-width: 20rem; height: 20rem;" />
                </div>
                <div class="modal-footer justify-content-between align-items-end">
                    <form action="${image_change_request_url}" method="post" class="w-50" enctype="multipart/form-data">
                        <div class="form-group">
                            <label>Update Image?</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="customFile" name="temp_image" required>
                                <label class="custom-file-label" for="customFile">Choose file</label>
                            </div>
                            <input type="text" id="customTemp_name" class="d-none" name="temp_name" value="${tempName}" required>
                        </div>
                        <button type="submit" class="btn btn-sm m-1 text-light" style="background-color: var(--wb-dark-red);">Submit</button>
                    </form>
                    <button type="button" class="btn btn-sm btn-secondary" onclick="handle_remove_modal('viewImageModal')" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    `;
    div.innerHTML = modal_elem;
    document.body.appendChild(div);
    const modal = new bootstrap.Modal(div);
    modal.show();
    const fileInput = document.querySelector('#customFile');
    const label = document.querySelector('label[for="customFile"]');
    fileInput.addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            label.textContent = file.name;
            const img = document.querySelector('.modal-body img');
            img.src = URL.createObjectURL(file);
        }
    });
}
    </script>
@endsection
