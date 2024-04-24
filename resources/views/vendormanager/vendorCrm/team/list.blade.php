@extends('vendormanager.layouts.app')
@section('header-css')
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
@endsection
@section('title', $page_heading." | Vendor Manager CRM")
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
                            <th class="text-nowrap">Profile Image</th>
                            <th class="text-nowrap">Name</th>
                            <th class="text-nowrap">Business Name</th>
                            <th class="text-nowrap">Mobile</th>
                            <th class="text-nowrap">Status</th>
                            <th class="text-nowrap">Created At</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </section>
    <div class="modal fade" id="imageViewModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Profile Image</h4>
                    <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                </div>
                <div class="modal-body text-center">
                    <img id="view_profile_img_elem" class="rounded" style="width: 25rem; height: 20rem;">
                </div>
                <div class="modal-footer text-sm">
                    <form action="" method="post">
                        <div class="form-group">
                            <label>Update Image</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="customFile" name="profile_image">
                                <label class="custom-file-label" for="customFile">Choose file</label>
                            </div>
                            <span class="ml-1 text-xs text-muted" style="left: 8px; bottom: -8px;">File must be an image and less than 200KB.</span>
                        </div>
                    </form>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('footer-script')
<script src="//cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script src="{{asset('plugins/moment/moment.min.js')}}"></script>
<script>
    $(document).ready(function() {
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
                url: "{{route('vendormanager.team.list.ajax')}}",
                headers: {
                    'X-CSRF-TOKEN': "{{csrf_token()}}",
                },
                method: "get",
                dataSrc: "data",
            },

            columns: [{
                    targets: 0,
                    name: "id",
                    data: "id",
                },
                {
                    targets: 1,
                    name: "profile_image",
                    data: "profile_image",
                },
                {
                    targets: 2,
                    name: "name",
                    data: "name",
                },
                {
                    targets: 3,
                    name: "business_name",
                    data: "business_name",
                },
                {
                    targets: 4,
                    name: "mobile",
                    data: "mobile",
                },
                {
                    targets: 5,
                    name: "status",
                    data: "status",
                },
                {
                    targets: 6,
                    name: "created_at",
                    data: "created_at",
                },
                {
                    targets: 7,
                    name: "action",
                    data: "id",
                    orderable: false,
                    searchable: false,
                },
            ],
            order: [[0, 'desc']],
            rowCallback: function(row, data, index) {
                const td_elements = row.querySelectorAll('td');
                td_elements[1].classList.add('py-1');

                if (data.profile_image) {
                    profile_view_attr = `onclick="handle_view_profile_image('${data.profile_image}')"`;
                } else {
                    profile_view_attr = "";
                }
                td_elements[1].innerHTML = `<a ${profile_view_attr} href="javascript:void(0);"><img class="img-thumbnail" src="${data.profile_image}" style="width: 50px;" onerror="this.onerror=null; this.src='{{asset('images/default-user.png')}}'"></a>`;

                td_elements[3].innerHTML = data.business_name ? data.business_name : 'N/A';

                if(data.status == 1){
                    status_elem = `<span class="badge badge-success">Active</span>`;
                }else{
                    status_elem = `<span class="badge badge-danger">In-Active</span>`;
                }
                td_elements[5].innerHTML = status_elem;

                const action_btns = `<td class="">
                    <a href="{{route('vendormanager.bypass.login')}}/${data.id}" target="_blank" class="text-dark mx-2" title="Login" onclick="return confirm('Are you sure want to login?')"><i class="fa fa-right-from-bracket" style="font-size: 15px; color: var(--wb-renosand);"></i></a>
                </td>`

                td_elements[6].innerHTML = moment(data.created_at).format("DD-MMM-YYYY hh:mm a");
                td_elements[7].innerHTML = action_btns;
            }
        });
    });

    function handle_view_profile_image(img_url) {
        const modal = new bootstrap.Modal("#imageViewModal");
        view_profile_img_elem.src = img_url;
        modal.show();
    }
</script>
@endsection
