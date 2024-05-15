@extends('admin.layouts.app')
@section('header-css')
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
@endsection
@section('title', $page_heading." | Non Venue CRM")
@section('main')
<div class="content-wrapper pb-5">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{$page_heading}}</h1>
                </div>
            </div>
            <div class="button-group my-4">
                <button class="btn text-light btn-sm buttons-print" onclick="handle_manage_vendor()" style="background-color: var(--wb-renosand)"><i class="fa fa-plus"></i> New</button>
                <a href="{{route('admin.vendor.list')}}"><button class="btn text-light btn-sm buttons-print" style="background-color: var(--wb-renosand)"><i class="fa-solid fa-arrow-right-from-bracket" style="transform :rotate(180deg)"></i> Back</button></a>
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
                            <th class="text-nowrap">Profile Image</th>
                            <th class="">Name</th>
                            <th class="text-nowrap">Mobile</th>
                            <th class="text-nowrap">Email</th>
                            <th class="text-nowrap">Business Name</th>
                            <th class="text-nowrap">Category</th>
                            <th class="text-nowrap">Status</th>
                            <th class="text-nowrap">Created At</th>
                            <th class="text-center text-nowrap">Action</th>
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
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="manageVendorModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="manageVendorModalHeading">Add Vendor</h4>
                    <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                </div>
                <form id="manage_vendor_form" method="post" enctype="multipart/form-data">
                    <div class="modal-body text-sm">
                        @csrf
                        <div class="row">
                            <div class="col-sm-4 mb-3">
                                <div class="form-group">
                                    <label for="vendor_name_inp">Vendor Name</label>
                                    <input type="text" class="form-control" id="vendor_name_inp" placeholder="Enter vendor name" name="vendor_name" required>
                                </div>
                            </div>
                            <div class="col-sm-4 mb-3">
                                <div class="form-group">
                                    <label for="vendor_mobile_inp">Mobile No. <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="vendor_mobile_inp" placeholder="Enter mobile no." name="mobile_number" required>
                                </div>
                            </div>
                            <div class="col-sm-4 mb-3">
                                <div class="form-group">
                                    <label for="vendor_email_inp">Email</label>
                                    <input type="email" class="form-control" id="vendor_email_inp" placeholder="Enter email" name="email">
                                </div>
                            </div>
                            <div class="col-sm-4 mb-3">
                                <div class="form-group">
                                    <label>Profile Image</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="vendor_profile_inp" name="profile_image">
                                        <label class="custom-file-label" for="vendor_profile_inp">Choose file</label>
                                    </div>
                                    <span class="position-absolute ml-1 text-xs text-muted" style="left: 8px; bottom: -8px;">File must be an image and less than 200KB.</span>
                                </div>
                            </div>
                            <div class="col-sm-4 mb-3">
                                <div class="form-group">
                                    <label for="business_name_inp">Business Name</label>
                                    <input type="text" class="form-control" id="business_name_inp" placeholder="Enter business name" name="business_name">
                                </div>
                            </div>
                            <div class="col-sm-4 mb-3">
                                <div class="form-group">
                                    <label for="category_select">Category <span class="text-danger">*</span></label>
                                    <select class="form-control" id="category_select" name="category" required>
                                        <option value="" selected disabled>Select vendor category</option>
                                        @foreach ($vendor_categories as $list)
                                            <option value="{{$list->id}}">{{$list->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer text-sm">
                        <div class="col">
                            <p>
                                <span class="text-danger">*</span>
                                Fields are required.
                            </p>
                        </div>
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm text-light m-1" style="background-color: var(--wb-dark-red);">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('footer-script')
<script src="//cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
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
            paging: false,
            order: [[9, 'desc']],
            serverSide: true,
            loading: true,
            ajax: {
                url: "{{route('admin.vendor.list.ajax')}}",
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
                    name: "mobile",
                    data: "mobile",
                },
                {
                    targets: 4,
                    name: "email",
                    data: "email",
                },
                {
                    targets: 5,
                    name: "business_name",
                    data: "business_name",
                },
                {
                    targets: 6,
                    name: "category_name",
                    data: "category_name",
                },
                {
                    targets: 7,
                    name: "status",
                    data: "status",
                },
                {
                    targets: 8,
                    name: "created_at",
                    data: "created_at",
                },
                {
                    targets:10,
                    name: "id",
                    data: "id",
                    orderable: false,
                    searchable: false,
                },
            ],

            rowCallback: function(row, data, index) {
                // console.log(data.display_order)
                row.setAttribute('id', data.id);
                const td_elements = row.querySelectorAll('td');
                td_elements[1].classList.add('py-1');
                // if (data.profile_image) {
                //     profile_view_attr = `onclick="handle_view_image('${data.profile_image}', '{{route('admin.vendor.updateProfileImage')}}/${data.id}')"`;
                // } else {
                //     profile_view_attr = "";
                // }
                td_elements[1].innerHTML = `<a onclick="handle_view_image('${data.profile_image}', '{{route('admin.vendor.updateProfileImage')}}/${data.id}')" href="javascript:void(0);">
                    <img class="img-thumbnail" src="${data.profile_image}" style="width: 50px;" onerror="this.onerror=null; this.src='{{asset('images/default-user.png')}}'">
                </a>`;
                td_elements[4].innerHTML = data.email ? data.email : 'N/A';
                td_elements[5].innerHTML = data.business_name ? data.business_name : 'N/A';
                status_action_elem = `<a href="{{route('admin.vendor.update.status')}}/${data.id}/${data.status == 1 ? 0 : 1}" style="font-size: 22px;"><i class="fa ${data.status == 1 ? 'fa-toggle-on text-success' : 'fa-toggle-off text-danger'} "></i></a>`;
                td_elements[6].classList.add('text-nowrap');
                td_elements[7].innerHTML = status_action_elem;
                td_elements[8].innerHTML = moment(data.created_at).format("DD-MMM-YYYY");
                const action_btns = `<td class="d-flex justify-content-around">
                    <a href="javascript:void(0);" class="text-success mx-2" title="Edit"><i class="fa fa-edit" style="font-size: 15px;" onclick="handle_manage_vendor(${data.id})"></i></a>
                    <a href="{{route('admin.vendor.delete')}}/${data.id}" onclick="return confirm('Are you sure want to delete?')" class="text-danger mx-2" title="Delete"><i class="fa fa-trash-alt" style="font-size: 15px;"></i></a>
                    <div class="dropdown d-inline-block mx-2">
                        <a href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-caret-down text-dark"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" target="_blank" onclick="return confirm('Login confirmation..')" href="{{route('admin.vendor.bypass.login')}}/${data.id}">Login</a></li>
                        </ul>
                    </div>
                </td>`
                td_elements[9].classList.add('text-nowrap');
                td_elements[9].innerHTML = action_btns;
            }
        });


        $("#serverTable tbody").sortable({
        delay: 150,
        stop: function() {
            var selectedData = new Array();
            $("#serverTable tbody>tr").each(function() {
                selectedData.push($(this).attr("id"));
            });
            updateOrder(selectedData);
        }
    });

    function updateOrder(aData) {
        $.ajax({
            url: "{{route('admin.vendor.vendorlistupdate')}}",
            type: 'POST',
            data: {
                allData: aData
            },
            success: function() {
                toastr.success("New alignement done !")
            }
        });
    }

        //  $("#serverTable tbody").sortable({
        //         axis: "y",
        //         cursor: "move",
        //         update: function (event, ui) {
        //             var newOrder = $(this).sortable('toArray').toString();
        //             // Make an AJAX request to save the updated order in the database
        //             $.ajax({
        //                 url: "{{route('admin.vendor.vendorlistupdate')}}",
        //                 type: 'POST',
        //                 data: {
        //                     allData: newOrder,
        //                     _token: "{{ csrf_token() }}",
        //                 },
        //                 success: function (response) {
        //                     // Handle success response
        //                     console.log('Order saved successfully');
        //                 },
        //                 error: function (error) {
        //                     // Handle error
        //                     console.error('Error saving order:', error);
        //                 }
        //             });
        //         }
        //     }).disableSelection();


    });

    function handle_manage_vendor(vendor_id = 0){
        const manageVendorModal = document.getElementById('manageVendorModal');
        const modal = new bootstrap.Modal(manageVendorModal);
        const submit_url = `{{route('admin.vendor.manage.process')}}/${vendor_id}`;
        manage_vendor_form.action = submit_url;
        if (vendor_id > 0) {
            fetch(`{{route('admin.vendor.edit')}}/${vendor_id}`).then(response => response.json()).then(data => {
                if(data.success === true){
                    vendor_name_inp.value = data.vendor.name;
                    vendor_mobile_inp.value = data.vendor.mobile;
                    vendor_email_inp.value = data.vendor.email;
                    business_name_inp.value = data.vendor.business_name;
                    category_select.querySelector(`option[value="${data.vendor.category_id}"]`).selected = true;
                    manageVendorModalHeading.innerText = "Edit Vendor";
                    modal.show();
                }else{
                    toastr[data.alert_type](data.message);
                }
            })
        }else{
            const inps = manageLeadModal.querySelectorAll("input");
            manageVendorModalHeading.innerText = "Add Vendor";
            for (let inp of inps) {
                inp.value = null;
            }
            modal.show();
        }
    }
</script>
@endsection
