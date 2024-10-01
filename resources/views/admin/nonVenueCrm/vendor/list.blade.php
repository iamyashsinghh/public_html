@extends('admin.layouts.app')
@section('header-css')
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
@endsection
@php

@endphp
@section('title', $page_heading . ' | Non Venue CRM')
@section('main')
    <div class="content-wrapper pb-5">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">{{ $page_heading }}</h1>
                    </div>
                </div>
                <div class="button-group my-4">
                    <button class="btn text-light btn-sm buttons-print" onclick="handle_manage_vendor()"
                        style="background-color: var(--wb-renosand)"><i class="fa fa-plus"></i> New</button>
                </div>
                <div class="button-group vendor-categories my-4">
                    @foreach ($vendor_categories as $category)
                        <button class="btn btn-secondary btn-sm filter-btn mt-2"
                            data-category-name="{{ $category->id }}">{{ $category->name }}</button>
                    @endforeach
                </div>
            </div>
        </section>
        <section class="content">
            <div class="container-fluid">
                <div class="table-responsive">
                    <table id="serverTable" class="table text-sm">
                        <thead class="sticky_head bg-light" style="position: sticky; top: 0;">
                            <tr>
                                <th class="text-nowrap">Subscription Type</th>
                                <th class="text-nowrap">Profile Image</th>
                                <th class="text-nowrap">&nbsp;&nbsp;ID&nbsp;&nbsp;</th>
                                <th class="">Name</th>
                                <th class="text-nowrap">Mobile</th>
                                <th class="text-nowrap">Email</th>
                                <th class="text-nowrap">Business Name</th>
                                <th class="text-nowrap">Total Leads</th>
                                <th class="text-nowrap">Category</th>
                                <th class="text-nowrap">Status</th>
                                <th class="text-nowrap">Is Active</th>
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
                        <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i
                                class="fa fa-times"></i></button>
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
                        <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i
                                class="fa fa-times"></i></button>
                    </div>
                    <form id="manage_vendor_form" method="post" enctype="multipart/form-data">
                        <div class="modal-body text-sm">
                            @csrf
                            <div class="row">
                                <div class="col-sm-4 mb-3">
                                    <div class="form-group">
                                        <label for="business_name_inp">Business Name</label>
                                        <input type="text" class="form-control" id="business_name_inp"
                                            placeholder="Enter business name" name="business_name">
                                    </div>
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <div class="form-group">
                                        <label for="vendor_name_inp">Vendor Name</label>
                                        <input type="text" class="form-control" id="vendor_name_inp"
                                            placeholder="Enter vendor name" name="vendor_name" required>
                                    </div>
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <div class="form-group">
                                        <label for="vendor_mobile_inp">Mobile No. <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="vendor_mobile_inp"
                                            placeholder="Enter mobile no." name="mobile_number" required>
                                    </div>
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <div class="form-group">
                                        <label for="vendor_altmobile_inp">Alt Mobile No. <span
                                                class="text-danger"></span></label>
                                        <input type="text" class="form-control" id="vendor_altmobile_inp"
                                            placeholder="Enter mobile no." name="alt_mobile_number">
                                    </div>
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <div class="form-group">
                                        <label for="vendor_email_inp">Email</label>
                                        <input type="email" class="form-control" id="vendor_email_inp"
                                            placeholder="Enter email" name="email">
                                    </div>
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <div class="form-group">
                                        <label for="category_select">Category <span class="text-danger">*</span></label>
                                        <select class="form-control" id="category_select" name="category" required>
                                            <option value="">Select vendor category</option>
                                            @foreach ($vendor_categories as $list)
                                                <option value="{{ $list->id }}">{{ $list->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <div class="form-group">
                                        <label for="group_name_select" id="group_name_select_lable">Select Group</label>
                                        <select class="form-control" id="group_name_select" name="group_name">
                                            <option value="" selected disabled>Select group</option>
                                            @foreach (range('A', 'Z') as $letter)
                                                <option value="{{ $letter }}">{{ $letter }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <div class="form-group">
                                        <label for="start_date_inp">Start Date</label>
                                        <input type="date" class="form-control" id="start_date_inp" name="start_date"
                                            required>
                                    </div>
                                </div>

                                <div class="col-sm-4 mb-3">
                                    <div class="form-group">
                                        <label for="end_date_inp">End Date</label>
                                        <input type="date" class="form-control" id="end_date_inp" name="end_date"
                                            required readonly>
                                    </div>
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <div class="form-group">
                                        <label for="parent_member_select">Parent Member <i>(Vendor Manager)</i></label>
                                        <select class="form-control" id="parent_member_select" name="parent_id">
                                            <option value="" disabled selected>Select Parent Member</option>
                                            @foreach ($team_members as $list)
                                                <option value="{{ $list->id }}">{{ $list->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <div class="form-group">
                                        <label>Profile Image</label>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="vendor_profile_inp"
                                                name="profile_image">
                                            <label class="custom-file-label" for="vendor_profile_inp">Choose file</label>
                                        </div>
                                        <span class="position-absolute ml-1 text-xs text-muted"
                                            style="left: 8px; bottom: -8px;">File must be an image and less than
                                            200KB.</span>
                                    </div>
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <div class="form-group">
                                        <label for="subscription_select">Type <i>(Subscription)</i></label>
                                        <select class="form-control" id="subscription_select" name="subscription_type">
                                            <option value="" disabled selected>Selected Subscription</option>
                                            <option value="premium">Premium</option>
                                            <option value="gold">Gold</option>
                                            <option value="elite">Elite</option>
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
                            <button type="button" class="btn btn-sm btn-secondary"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-sm text-light m-1"
                                style="background-color: var(--wb-dark-red);">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" id="manageDownloadVendorData" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="manageDownloadVendorDataHeading">Download Vendor Data</h4>
                        <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i
                                class="fa fa-times"></i></button>
                    </div>
                    <form id="manageDownloadVendorDataForm" method="post" enctype="multipart/form-data">
                        <div class="modal-body text-sm">
                            @csrf
                            <input type="text" class="d-none" name="vendor_id" id="download_form_vendor_id">
                            <input type="text" class="d-none" name="vendor_name" id="download_form_vendor_name">
                            <input type="text" class="d-none" name="business_name"
                                id="download_form_vendor_business_name">
                            <div class="row">
                                <div class="col-sm-12 mb-3">
                                    <div class="form-group">
                                        <label for="file_type">File Type<span class="text-danger">*</span></label>
                                        <select name="file_type" class="form-control" id="file_type" required>
                                            <option value="">Select File Type</option>
                                            <option value="exel">Exel</option>
                                            <option value="pdf">Pdf</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6 mb-3">
                                    <div class="form-group">
                                        <label for="fromDateVendorData">From <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="fromDateVendorData"
                                            name="from" required>
                                    </div>
                                </div>
                                <div class="col-sm-6 mb-3">
                                    <div class="form-group">
                                        <label for="toDateVendorData">To <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="toDateVendorData" name="to"
                                            required>
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
                            <button type="button" class="btn btn-sm btn-secondary"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-sm text-light m-1"
                                style="background-color: var(--wb-dark-red);">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('footer-script')
    @include('whatsapp.admin_nv_chat');
    <script src="//cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
    <script>
        var localities = @json($localities);

        function handle_whatsapp_msg(id) {
            const elementToUpdate = document.querySelector(`#what_id-${id}`);
            if (elementToUpdate) {
                elementToUpdate.outerHTML =
                    `<i class="fab fa-whatsapp" onclick="handle_whatsapp_msg(${id})" style="font-size: 25px; color: green;"></i>`;
            }
            const form_title = document.querySelector(`#form_title_modal`);
            form_title.innerHTML = `Whatsapp Messages of ${id}`;
            const manageWhatsappChatModal = new bootstrap.Modal(document.getElementById('wa_msg'));
            wamsg(id);
            manageWhatsappChatModal.show();
            const wa_status_url = `{{ route('whatsapp_chat.status_nv_team') }}`;
            const wa_status_data = {
                mobile: id
            };
        }
        var vendor_cat_id = 0;
        var routeTemplate = "{{ route('admin.vendor.list.ajax', ['vendor_cat_id' => 'PLACEHOLDER']) }}";
        var actualUrl = routeTemplate.replace('PLACEHOLDER', vendor_cat_id);
        $(document).ready(function() {
            var table = $('#serverTable').DataTable({
                pageLength: 10,
                language: {
                    "search": "_INPUT_",
                    "searchPlaceholder": "Type here to search..",
                    processing: `<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>`, // loader
                },
                paging: true,
                serverSide: true,
                loading: true,
                ajax: {
                    url: actualUrl,
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
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
                        name: "subscription_type",
                        data: "subscription_type",
                    },
                    {
                        targets: 2,
                        name: "profile_image",
                        data: "profile_image",
                    },
                    {
                        targets: 3,
                        name: "name",
                        data: "name",
                    },
                    {
                        targets: 4,
                        name: "mobile",
                        data: "mobile",
                    },
                    {
                        targets: 5,
                        name: "email",
                        data: "email",
                    },
                    {
                        targets: 6,
                        name: "business_name",
                        data: "business_name",
                    },
                    {
                        targets: 7,
                        name: "total_leads",
                        data: "total_leads",
                        defaultContent: 'No leads found',
                    },
                    {
                        targets: 8,
                        name: "category_name",
                        data: "category_name",
                    },
                    {
                        targets: 9,
                        name: "status",
                        data: "status",
                    },
                    {
                        targets: 10,
                        name: "is_active",
                        data: "is_active",
                    },
                    {
                        targets: 11,
                        name: "created_at",
                        data: "created_at",
                    },
                    {
                        targets: 12,
                        name: "id",
                        data: "id",
                        orderable: false,
                        searchable: false,
                    },

                ],
                order: [
                    [9, 'desc']
                ],
                rowCallback: function(row, data, index) {
                    row.setAttribute('id', data.id);
                    const td_elements = row.querySelectorAll('td');

                    td_elements[0].classList.add('py-1');
                    td_elements[0].innerHTML = `
                                    <img src="{{asset('/images/packages/${data.subscription_type}.png')}}"  style="height: 50px; width:130px;">
                    `;

                        td_elements[1].classList.add('py-1');
                    td_elements[1].innerHTML = `<a onclick="handle_view_image('${data.profile_image}', '{{ route('admin.vendor.updateProfileImage') }}/${data.id}')" href="javascript:void(0);">
                    <img class="img-thumbnail" src="${data.profile_image}" style="width: 50px;" onerror="this.onerror=null; this.src='{{ asset('images/default-user.png') }}'">
                </a>`;
                td_elements[2].innerHTML = `${data.id}-${data.group_name}`;


                    if (data.is_whatsapp_msg === 1) {
                        td_elements[4].innerHTML =
                            `<div class="d-flex"><div>${data.mobile} </div> &nbsp;&nbsp;&nbsp;<i class="fa-brands fa-square-whatsapp" onclick="handle_whatsapp_msg(${data.mobile})" id="what_id-${data.mobile}" style="font-size: 25px; color: green;"></i></div>`;
                    } else {
                        td_elements[4].innerHTML =
                            `<div class="d-flex"><div>${data.mobile} </div>&nbsp;&nbsp;&nbsp;<i class="fab fa-whatsapp" onclick="handle_whatsapp_msg(${data.mobile})" style="font-size: 25px; color: green;"></i></div>`;
                    }
                    td_elements[5].innerHTML = data.group_name;
                    td_elements[5].innerHTML = data.email ? data.email : 'N/A';
                    td_elements[6].innerHTML = data.business_name ? data.business_name : 'N/A';
                    status_action_elem =
                        `<a href="{{ route('admin.vendor.update.status') }}/${data.id}/${data.status == 1 ? 0 : 1}" style="font-size: 22px;"><i class="fa ${data.status == 1 ? 'fa-toggle-on text-success' : 'fa-toggle-off text-danger'} "></i></a>`;
                    td_elements[8].classList.add('text-nowrap');
                    td_elements[9].innerHTML = status_action_elem;
                    diable_action_elem = `<a href="{{route('admin.vendor.update.is_active')}}/${data.id}/${data.is_active == 1 ? 0 : 1}" style="font-size: 22px;"><i class="fa ${data.is_active == 1 ? 'fa-toggle-on text-success' : 'fa-toggle-off text-danger'} "></i></a>`;

td_elements[10].innerHTML = diable_action_elem;
                    td_elements[11].innerHTML = moment(data.created_at).format("DD-MMM-YYYY");
                    const action_btns = `<td class="d-flex justify-content-around">
                    <a href="{{ route('admin.vendor.view') }}/${data.id}" class="text-dark mx-2" title="View"><i class="fa fa-eye" style="font-size: 15px;"></i></a>
                    <a href="javascript:void(0);" class="text-success mx-2" title="Edit"><i class="fa fa-edit" style="font-size: 15px;" onclick="handle_manage_vendor(${data.id})"></i></a>
                    <a href="javascript:void(0);" class="text-info mx-2" title="Download"><i class="fa fa-download" style="font-size: 15px;" onclick="handle_download_vendor_data(${data.id}, '${data.name}', '${data.business_name}')"></i></a>
                    <a href="{{ route('admin.vendor.delete') }}/${data.id}" onclick="return confirm('Are you sure want to delete?')" class="text-danger mx-2" title="Delete"><i class="fa fa-trash-alt" style="font-size: 15px;"></i></a>
                    <div class="dropdown d-inline-block mx-2">
                        <a href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-caret-down text-dark"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" target="_blank" onclick="return confirm('Login confirmation..')" href="{{ route('admin.vendor.bypass.login') }}/${data.id}">Login</a></li>
                        </ul>
                    </div>
                </td>`
                    td_elements[12].classList.add('text-nowrap');
                    td_elements[12].innerHTML = action_btns;
                }
            });
            $('.filter-btn').on('click', function() {
                var vendor_cat_id = $(this).data('category-name');
                var actualUrl = routeTemplate.replace('PLACEHOLDER', vendor_cat_id);
                table.ajax.url(actualUrl).load();
            });

            $('#start_date_inp').on('change', function() {
                var startDate = $(this).val();
                if (startDate) {
                    var startDateObject = new Date(startDate);
                    startDateObject.setDate(startDateObject.getDate() + 89);
                    var endDate = startDateObject.toISOString().split('T')[0];
                    $('#end_date_inp').val(endDate);
                }
            });
        });

        function handle_manage_vendor(vendor_id = 0) {
            const manageVendorModal = document.getElementById('manageVendorModal');
            const modal = new bootstrap.Modal(manageVendorModal);
            const submit_url = `{{ route('admin.vendor.manage.process') }}/${vendor_id}`;
            manage_vendor_form.action = submit_url;
            if (vendor_id > 0) {
                fetch(`{{ route('admin.vendor.edit') }}/${vendor_id}`).then(response => response.json()).then(data => {
                    if (data.success === true) {
                        vendor_name_inp.value = data.vendor.name;
                        vendor_mobile_inp.value = data.vendor.mobile;
                        vendor_email_inp.value = data.vendor.email;
                        business_name_inp.value = data.vendor.business_name;
                        start_date_inp.value = data.vendor.start_date;
                        end_date_inp.value = data.vendor.end_date;
                        group_name_select.value = data.vendor.group_name;
                        vendor_altmobile_inp.value = data.vendor.alt_mobile_number;
                        category_select.querySelector(`option[value="${data.vendor.category_id}"]`).selected = true;

                        const subscriptionElement = subscription_select.querySelector(`option[value="${data.vendor.subscription_type}"]`);
                        if (subscriptionElement) {
                            subscriptionElement.selected = true;
                        } else {
                            console.error('Option element not found for value:', data.vendor.parent_id);
                        }

                        updateGroupDropdown(data.vendor.category_id, localities, data.vendor.group_name);

                        const optionElement = parent_member_select.querySelector(
                            `option[value="${data.vendor.parent_id}"]`);
                        if (optionElement) {
                            optionElement.selected = true;
                        } else {
                            console.error('Option element not found for value:', data.vendor.parent_id);
                        }

                        manageVendorModalHeading.innerText = "Edit Vendor";
                        modal.show();
                    } else {
                        toastr[data.alert_type](data.message);
                    }
                })
            } else {
                const inps = manageLeadModal.querySelectorAll("input");
                manageVendorModalHeading.innerText = "Add Vendor";
                for (let inp of inps) {
                    inp.value = null;
                }
                modal.show();
            }
        }

        function handle_download_vendor_data(vendor_id, vendor_name, business_name) {
            const manageDownloadVendorData = document.getElementById('manageDownloadVendorData');
            const modal = new bootstrap.Modal(manageDownloadVendorData);
            const submit_url = `{{ route('admin.vendor.download.data') }}`;
            manageDownloadVendorDataForm.action = submit_url;
            manageDownloadVendorDataHeading.innerText = `Download ${vendor_name}'s (${business_name}) Data`;
            download_form_vendor_id.value = vendor_id;
            modal.show();
        }
    </script>
    <script>
        document.getElementById('category_select').addEventListener('change', function() {
            updateGroupDropdown(this.value, localities);
        });

        function updateGroupDropdown(categoryId, localities, selectedGroup = null) {
            var groupSelect = document.getElementById('group_name_select');
            var groupSelectLabel = document.getElementById('group_name_select_lable');
            groupSelect.innerHTML = '<option value="" selected disabled>Select group</option>';
            if (categoryId == '4') {
                groupSelectLabel.innerText = 'Select Locality';
                localities.forEach(function(locality) {
                    var option = new Option(locality.name, locality.name);
                    option.selected = locality.name === selectedGroup;
                    groupSelect.add(option);
                });
            } else {
                for (var i = 65; i <= 90; i++) {
                    var letter = String.fromCharCode(i);
                    var option = new Option(letter, letter);
                    option.selected = letter === selectedGroup;
                    groupSelect.add(option);
                }
                groupSelectLabel.innerText = 'Select Group';
            }
        }
    </script>

@endsection
