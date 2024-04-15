@extends('admin.layouts.app')
@section('header-css')
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
@endsection
@section('title', $page_heading." | Venue CRM")
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
                <a href="{{route('admin.team.new')}}" class="btn btn-sm text-light buttons-print" style="background-color: var(--wb-renosand)"><i class="fa fa-plus"></i> New</a>
            </div>
            <div class="button-group vendor-categories my-4">
                @foreach ($roles as $role)
                    <button class="btn btn-secondary btn-sm filter-btn"
                        data-role-name="{{ $role->id }}">{{ $role->name }}</button>
                @endforeach
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
                            <th class="text-nowrap">Mobile</th>
                            <th class="text-nowrap">Venue</th>
                            <th class="text-nowrap">Role</th>
                            <th class="text-nowrap">Status</th>
                            <th class="text-nowrap">Created At</th>
                            <th class="text-center">Action</th>
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
<script src="{{asset('plugins/moment/moment.min.js')}}"></script>
    @include('whatsapp.chat');
<script>
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
            const wa_status_url = `{{ route('whatsapp_chat.status') }}`;
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
                url: "{{route('admin.team.list.ajax')}}",
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
                    name: "venue_name",
                    data: "venue_name",
                },
                {
                    targets: 5,
                    name: "role_name",
                    data: "role_name",
                },
                {
                    targets: 6,
                    name: "status",
                    data: "status",
                },
                {
                    targets: 7,
                    name: "created_at",
                    data: "created_at",
                },
                {
                    targets: 8,
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

                // if (data.profile_image) {
                //     profile_view_attr = `onclick="handle_view_image('${data.profile_image}')"`;
                // } else {
                //     profile_view_attr = "";
                // }
                td_elements[1].innerHTML = `<a onclick="handle_view_image('${data.profile_image}', '{{route('admin.team.updateProfileImage')}}/${data.id}')" href="javascript:void(0);"><img class="img-thumbnail" src="${data.profile_image}" style="width: 50px;" onerror="this.onerror=null; this.src='{{asset('images/default-user.png')}}'"></a>`;
                // if (data.is_whatsapp_msg === 1) {
                //         td_elements[5].innerHTML =
                //             `<div class="d-flex"><div>${data.mobile} </div> &nbsp;&nbsp;&nbsp;<i class="fa-brands fa-square-whatsapp" onclick="handle_whatsapp_msg(${data.mobile})" id="what_id-${data.mobile}" style="font-size: 25px; color: green;"></i></div>`;
                //     } else {
                        td_elements[3].innerHTML =
                            `<div class="d-flex"><div>${data.mobile} </div>&nbsp;&nbsp;&nbsp;<i class="fab fa-whatsapp" onclick="handle_whatsapp_msg(${data.mobile})" style="font-size: 25px; color: green;"></i></div>`;
                    // }

                td_elements[4].innerHTML = data.venue_name ? data.venue_name : 'N/A';



                status_action_elem = `<a href="{{route('admin.team.update.status')}}/${data.id}/${data.status == 1 ? 0 : 1}" style="font-size: 22px;"><i class="fa ${data.status == 1 ? 'fa-toggle-on text-success' : 'fa-toggle-off text-danger'} "></i></a>`;

                td_elements[6].innerHTML = status_action_elem;

                const action_btns = `<td class="d-flex justify-content-around">
                    <a href="{{route('admin.team.view')}}/${data.id}" class="text-dark mx-2" title="View"><i class="fa fa-eye" style="font-size: 15px;"></i></a>
                    <a href="{{route('admin.team.edit')}}/${data.id}" class="text-success mx-2" title="Edit"><i class="fa fa-edit" style="font-size: 15px;"></i></a>
                    <a href="{{route('admin.team.delete')}}/${data.id}" onclick="return confirm('Are you sure want to delete?')" class="text-danger mx-2" title="Delete"><i class="fa fa-trash-alt" style="font-size: 15px;"></i></a>
                    <div class="dropdown d-inline-block mx-2">
                        <a href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-caret-down text-dark"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" target="_blank" onclick="return confirm('Login confirmation..')" href="{{route('admin.bypass.login')}}/${data.id}">Login</a></li>
                        </ul>
                    </div>
                </td>`

                td_elements[7].innerHTML = moment(data.created_at).format("DD-MMM-YYYY");
                td_elements[8].innerHTML = action_btns;
            }
        });
    });

    function handle_change_status(elem, member_id) {
        const i = elem.firstElementChild;
        const status = elem.getAttribute('data-status');
        if (status == 1) {
            i.classList = "fa fa-toggle-on text-success";
            elem.setAttribute("data-status", 0);
        } else {
            i.classList = "fa fa-toggle-off text-danger";
            elem.setAttribute("data-status", 1);
        }

        const formBody = {
            "member_id": member_id,
            status: status
        };

        common_ajax(
            `{{route('admin.team.update.status')}}`,
            "post",
            JSON.stringify(formBody)
        ).then(response => response.text()).then(data => {
            console.log(data);
        })
    }
</script>
@endsection
