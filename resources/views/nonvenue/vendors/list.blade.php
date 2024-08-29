@extends('nonvenue.layouts.app')
@section('header-css')
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
@endsection
@section('title', $page_heading . ' | Non Venue CRM')
@section('main')
    <div class="content-wrapper pb-5">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">{{ $page_heading }} Vendors</h1>
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
                                <th class="text-nowrap">Subscription Type</th>
                                <th class="text-nowrap">Profile Image</th>
                                <th class="text-nowrap">&nbsp;&nbsp;ID&nbsp;&nbsp;</th>
                                <th class="">Name</th>
                                <th class="text-nowrap">Mobile</th>
                                <th class="text-nowrap">Business Name</th>
                                <th class="text-nowrap">Total Leads</th>
                                <th class="text-nowrap">Category</th>
                                <th class="text-nowrap">Created At</th>
                                <th class="text-center text-nowrap">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </section>
    </div>
    <div class="modal fade" id="vendorsForwodedleadInfo" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="vendor_lead_forword_info_heading">Forward Information</h4>
                    <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i
                            class="fa fa-times"></i></button>
                </div>
                <div class="modal-body text-center">
                    <div class="table-responsive">
                        <table id="clientTable" class="table text-sm">
                            <thead>
                                <tr>
                                    <th class="text-nowrap">Lead Id</th>
                                    <th class="text-nowrap">Name</th>
                                    <th class="text-nowrap">Mobile</th>
                                    <th class="text-nowrap">Event Date</th>
                                    <th class="text-nowrap">Forwarded At</th>
                                </tr>
                            </thead>
                            <tbody id="vendor_leads_info_table_body">
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
@endsection
@section('footer-script')
    @include('whatsapp.nvchat');
    <script src="//cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
    <script>
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
                    url: `{{ route('nonvenue.vendor_ajax.list', $vendor_category_id) }}`,
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
                        name: "business_name",
                        data: "business_name",
                    },
                    {
                        targets: 6,
                        name: "total_leads",
                        data: "total_leads",
                        defaultContent: 'No leads found',
                    },
                    {
                        targets: 7,
                        name: "category_name",
                        data: "category_name",
                    },
                    {
                        targets: 8,
                        name: "created_at",
                        data: "created_at",
                    },
                    {
                        targets: 9,
                        name: "id",
                        data: "id",
                        orderable: false,
                        searchable: false,
                    },
                ],
                rowCallback: function(row, data, index) {
                    row.setAttribute('id', data.id);
                    const td_elements = row.querySelectorAll('td');

                    td_elements[0].classList.add('py-1');
                    td_elements[0].innerHTML = `
                                    <img src="{{asset('/images/packages/${data.subscription_type}.png')}}"  style="height: 50px; width:150px;">
                    `;

                    td_elements[1].classList.add('py-1');
                    td_elements[1].innerHTML =
                        `<img class="img-thumbnail" src="${data.profile_image}" style="width: 50px;" onerror="this.onerror=null; this.src='{{ asset('images/default-user.png') }}'">`;

                        td_elements[2].innerHTML = `${data.id}-${data.group_name}`;


                        if (data.is_whatsapp_msg === 1) {
                            td_elements[3].innerHTML =
                                `<div class="d-flex"><div>${data.mobile} </div> &nbsp;&nbsp;&nbsp;<i class="fa-brands fa-square-whatsapp" onclick="handle_whatsapp_msg(${data.mobile})" id="what_id-${data.mobile}" style="font-size: 25px; color: green;"></i></div>`;
                        } else {
                            td_elements[3].innerHTML =
                                `<div class="d-flex"><div>${data.mobile} </div>&nbsp;&nbsp;&nbsp;<i class="fab fa-whatsapp" onclick="handle_whatsapp_msg(${data.mobile})" style="font-size: 25px; color: green;"></i></div>`;
                        }
                    td_elements[4].innerHTML = data.business_name ? data.business_name : 'N/A';
                    status_action_elem =
                        `<a href="{{ route('admin.vendor.update.status') }}/${data.id}/${data.status == 1 ? 0 : 1}" style="font-size: 22px;"><i class="fa ${data.status == 1 ? 'fa-toggle-on text-success' : 'fa-toggle-off text-danger'} "></i></a>`;
                    td_elements[6].classList.add('text-nowrap');
                    td_elements[7].innerHTML = moment(data.created_at).format("DD-MMM-YYYY");
                    const action_btns =
                            `<button onclick="handle_view_vendor_lead(${data.id}, '${data.name}', '${data.business_name}')" class="btn p-0 px-2 btn-info d-flex align-items-center" title="Forward info" style="column-gap: 5px;"><i class="fa fa-share-alt" style="font-size: 15px;"></i>${data.total_leads}</button>`;
                        td_elements[8].classList.add('text-nowrap');
                    td_elements[8].innerHTML = action_btns;
                }
            });
        });
        function handle_view_vendor_lead(id , vendor_name, vendor_bussiness_name) {
            document.getElementById('vendor_lead_forword_info_heading').innerHTML = `${vendor_name} (${vendor_bussiness_name})`;
            const url = "{{ route('nonvenue.vedor_leads.list', ['vendor_id' => ':vendor_id']) }}".replace(':vendor_id', id);
            fetch(url, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    $('#vendorsForwodedleadInfo').modal('show');
                    displayLeads(data);
                })
                .catch(error => console.error('Error fetching data:', error));
        }
        function displayLeads(data) {
            const tableBody = document.getElementById('vendor_leads_info_table_body');
            tableBody.innerHTML = '';
            data.forEach((lead) => {
                const row = document.createElement('tr');
                row.innerHTML = `
            <td>${lead.lead_id}</td>
            <td>${lead.name}</td>
            <td>${lead.mobile}</td>
            <td>${new Date(lead.event_datetime).toLocaleString()}</td>
            <td>${new Date(lead.created_at).toLocaleString()}</td>`;
                tableBody.appendChild(row);
            });
        }
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
            const wa_status_url = `{{ route('whatsapp_chat.status_nv_team_vendor') }}`;
            const wa_status_data = {
                mobile: id
            };
            fetch(wa_status_url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(wa_status_data),
                })
                .then(response => response.json())
                .then(data => {})
                .catch((error) => {});
        }
    </script>
@endsection
