@extends('admin.layouts.app')
@section('header-css')
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
@endsection
@section('title', $page_heading . ' | Venue CRM')
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
                    <button class="btn text-light btn-sm buttons-print" onclick="handle_role_add()"
                        style="background-color: var(--wb-renosand)"><i class="fa fa-plus"></i> New</button>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="container-fluid">
                <div class="table-responsive">
                    <table id="clientTable" class="table text-sm">
                        <thead>
                            <tr>
                                <th class="text-nowrap">ID</th>
                                <th class="text-nowrap">Role Name</th>
                                <th class="text-nowrap">Permissions</th>
                                <th class="text-nowrap">Created At</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($roles as $role)
                                <tr>
                                    <td>{{ $role->id }}</td>
                                    <td class="text-bold">{{ $role->name }}</td>
                                    <td class="text-nowrap">
                                        <?php
                                        if ($role->permissions) {
                                            foreach ($role->permissions as $key => $list) {
                                                $a = implode(', ', $list);
                                                $permission_list = ucwords($a);
                                                echo "<p class='mb-0'><b>$key: </b>$permission_list</p>";
                                            }
                                        } else {
                                            echo 'Null';
                                        }
                                        ?>
                                    </td>
                                    <td>{{ date('d-M-Y', strtotime($role->created_at)) }}</td>
                                    <td class="d-flex justify-content-around">
                                        <a href="javascript:void(0);" data-id="{{ $role->id }}"
                                            onclick="handle_role_edit({{ $role->id }})" class="text-success"
                                            title="Edit"><i class="fa fa-edit"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
        <div class="modal fade" id="roleManageModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title" id="exampleModalLabel">Add Role</h3>
                        <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i
                                class="fa fa-times"></i></button>
                    </div>
                    <form id="rolesManageForm" action="" method="post">
                        <div class="modal-body text-sm">
                            <?php
                            $lead_permissions_arr = ['add', 'edit', 'view', 'forward', 'rm-msg'];
                            $common_permissions_arr = ['add', 'edit'];
                            ?>
                            <div class="form-group mb-3">
                                <label for="role_name_inp">Role Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="role_name" id="role_name_inp"
                                    placeholder="Enter role name">
                            </div>
                            <div class="row mb-4 border-bottom pb-3">
                                <div class="col-12 d-flex" style="column-gap: 1rem;">
                                    <h4>Leads</h4>
                                    <input onchange="handle_all_leads_permissions(this)"
                                        class="custom_select_all_checkbox_for_role lead_all_permission_checkox"
                                        type="checkbox" style="width: 1.2rem;">
                                </div>
                                @foreach ($lead_permissions_arr as $key => $list)
                                    <div class="custom-control custom-checkbox col-4 col-lg my-1">
                                        <input
                                            class="custom-control-input custom_checkbox_for_role for_leads_checkbox position-static"
                                            name="lead_{{ $list }}" type="checkbox"
                                            id="leadsCheckbox{{ $key }}">
                                        <label class="custom-control-label"
                                            for="leadsCheckbox{{ $key }}">{{ ucfirst($list) }}</label>
                                    </div>
                                @endforeach
                            </div>
                            <div class="row mb-4 border-bottom pb-3">
                                <div class="col-12 d-flex" style="column-gap: 1rem;">
                                    <h4>Task</h4>
                                    <input onchange="handle_all_task_permissions(this)"
                                        class="custom_select_all_checkbox_for_role task_all_permission_checkox"
                                        type="checkbox" style="width: 1.2rem;">
                                </div>
                                @foreach ($common_permissions_arr as $key => $list)
                                    <div class="custom-control custom-checkbox col-4 my-1">
                                        <input
                                            class="custom-control-input custom_checkbox_for_role for_tasks_checkbox position-static"
                                            name="task_{{ $list }}" type="checkbox"
                                            id="taskCheckbox{{ $key }}">
                                        <label class="custom-control-label"
                                            for="taskCheckbox{{ $key }}">{{ ucfirst($list) }}</label>
                                    </div>
                                @endforeach
                            </div>
                            <div class="row mb-4 border-bottom pb-3">
                                <div class="col-12 d-flex" style="column-gap: 1rem;">
                                    <h4>Visit</h4>
                                    <input onchange="handle_all_visit_permissions(this)"
                                        class="custom_select_all_checkbox_for_role visit_all_permission_checkox"
                                        type="checkbox" style="width: 1.2rem;">
                                </div>
                                @foreach ($common_permissions_arr as $key => $list)
                                    <div class="custom-control custom-checkbox col-4 my-1">
                                        <input
                                            class="custom-control-input custom_checkbox_for_role for_visits_checkbox position-static"
                                            name="visit_{{ $list }}" type="checkbox"
                                            id="visitCheckbox{{ $key }}">
                                        <label class="custom-control-label"
                                            for="visitCheckbox{{ $key }}">{{ ucfirst($list) }}</label>
                                    </div>
                                @endforeach
                            </div>
                            <div class="row mb-4 border-bottom pb-3">
                                <div class="col-12 d-flex" style="column-gap: 1rem;">
                                    <h4>Note</h4>
                                    <input onchange="handle_all_note_permissions(this)"
                                        class="custom_select_all_checkbox_for_role note_all_permission_checkox"
                                        type="checkbox" style="width: 1.2rem;">
                                </div>
                                @foreach ($common_permissions_arr as $key => $list)
                                    <div class="custom-control custom-checkbox col-4 my-1">
                                        <input
                                            class="custom-control-input custom_checkbox_for_role for_notes_checkbox position-static"
                                            name="note_{{ $list }}" type="checkbox"
                                            id="noteCheckbox{{ $key }}">
                                        <label class="custom-control-label"
                                            for="noteCheckbox{{ $key }}">{{ ucfirst($list) }}</label>
                                    </div>
                                @endforeach
                            </div>
                            <div class="row mb-4 border-bottom pb-3">
                                <div class="col-12 d-flex" style="column-gap: 1rem;">
                                    <h4>Meeting</h4>
                                    <input onchange="handle_all_meeting_permissions(this)"
                                        class="custom_select_all_checkbox_for_role meeting_all_permission_checkox"
                                        type="checkbox" style="width: 1.2rem;">
                                </div>
                                @foreach ($common_permissions_arr as $key => $list)
                                    <div class="custom-control custom-checkbox col-4 my-1">
                                        <input
                                            class="custom-control-input custom_checkbox_for_role for_meetings_checkbox position-static"
                                            name="meeting_{{ $list }}" type="checkbox"
                                            id="noteCheckbox{{ $key }}">
                                        <label class="custom-control-label"
                                            for="meetingCheckbox{{ $key }}">{{ ucfirst($list) }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-sm btn-secondary"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" id="submit_btn"
                                class="btn btn-sm text-light d-flex justify-content-center align-items-center"
                                style="background-color: var(--wb-dark-red);">Submit
                                <i id="custom_spinner" class="fa fa-spinner fa-spin ml-1"
                                    style="font-size: 13px; display: none;"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('footer-script')
    <script src="//cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>

    <script>
        initialize_datatable();
        $(document).ready(function() {})


        let role_id = 0;
        const modal = new bootstrap.Modal("#roleManageModal");

        function handle_role_add() {
            role_id = 0;
            role_name_inp.value = "";
            document.querySelectorAll('.custom_checkbox_for_role').forEach((elem) => {
                elem.checked = false;
            })
            document.querySelectorAll('.custom_select_all_checkbox_for_role').forEach((elem) => {
                elem.checked = false;
            })
            role_name_inp.disabled = false;
            role_name_inp.removeAttribute('title');
            modal.show();
        }

        function handle_all_note_permissions(elem) {
            const for_notes_checkbox = document.querySelectorAll('.for_notes_checkbox');
            for (let item of for_notes_checkbox) {
                if (elem.checked) {
                    item.checked = true;
                } else {
                    item.checked = false;
                }
            }
        }

        function handle_all_meeting_permissions(elem) {
            const for_meetings_checkbox = document.querySelectorAll('.for_meetings_checkbox');
            for (let item of for_meetings_checkbox) {
                if (elem.checked) {
                    item.checked = true;
                } else {
                    item.checked = false;
                }
            }
        }

        function handle_all_leads_permissions(elem) {
            const for_leads_checkbox = document.querySelectorAll('.for_leads_checkbox');
            for (let item of for_leads_checkbox) {
                if (elem.checked) {
                    item.checked = true;
                } else {
                    item.checked = false;
                }
            }
        }

        function handle_all_visit_permissions(elem) {
            const for_visits_checkbox = document.querySelectorAll('.for_visits_checkbox');
            for (let item of for_visits_checkbox) {
                if (elem.checked) {
                    item.checked = true;
                } else {
                    item.checked = false;
                }
            }
        }

        function handle_all_task_permissions(elem) {
            const for_tasks_checkbox = document.querySelectorAll('.for_tasks_checkbox');
            for (let item of for_tasks_checkbox) {
                if (elem.checked) {
                    item.checked = true;
                } else {
                    item.checked = false;
                }
            }
        }

        rolesManageForm.addEventListener('submit', (e) => {
            e.preventDefault();
            submit_btn.disabled = true;
            custom_spinner.style.display = "block";

            let permission_json = {};
            let lead_permissions = [];
            let task_permissions = [];
            let visit_permissions = [];
            let note_permissions = [];
            let meeting_permissions = [];
            document.querySelectorAll('.custom_checkbox_for_role:checked').forEach((elem) => {
                let name_arr = elem.name.split("_");
                if (name_arr[0] == "lead") {
                    lead_permissions.push(name_arr[1]);
                } else if (name_arr[0] == "task") {
                    task_permissions.push(name_arr[1]);
                } else if (name_arr[0] == "visit") {
                    visit_permissions.push(name_arr[1]);
                } else if (name_arr[0] == "note") {
                    note_permissions.push(name_arr[1]);
                } else if(name_arr[0] == "meeting"){
                    meeting_permissions.push(name_arr[1])
                }
            })

            if (lead_permissions.length > 0) {
                permission_json.lead = lead_permissions;
            }
            if (task_permissions.length > 0) {
                permission_json.task = task_permissions;
            }
            if (visit_permissions.length > 0) {
                permission_json.visit = visit_permissions;
            }
            if (note_permissions.length > 0) {
                permission_json.note = note_permissions;
            }
            if (meeting_permissions.length > 0) {
                permission_json.meeting = meeting_permissions;
            }
            const formBody = JSON.stringify({
                role_name: role_name_inp.value,
                permissions: permission_json,
            });

            common_ajax(
                `{{ route('admin.role.manage.process') }}/${role_id}`,
                'post',
                formBody
            ).then(response => response.json()).then(data => {
                toastr[data.alert_type](data.message);
                setTimeout(() => {
                    submit_btn.disabled = false;
                    custom_spinner.style.display = "none";
                    if (data.success == true) {
                        window.location.reload();
                    }
                }, 3000);
            })
        })

        function handle_role_edit(id) {
            //for resetting input & checkbox values
            role_name_inp.value = "";
            document.querySelectorAll('.custom_checkbox_for_role').forEach((elem) => {
                elem.checked = false;
            })
            document.querySelectorAll('.custom_select_all_checkbox_for_role').forEach((elem) => {
                elem.checked = false;
            })

            common_ajax(
                `{{ route('admin.role.edit') }}/${id}`,
                'get'
            ).then(response => response.json()).then(data => {
                if (data.success == true) {
                    const role = data.role;
                    const permissions = role.permissions;

                    role_id = id;
                    role_name_inp.value = role.name;
                    if (data.role_assigned) {
                        role_name_inp.disabled = true;
                        role_name_inp.title = "The role has been assigned to members, name cannot be change."
                    } else {
                        role_name_inp.disabled = false;
                        role_name_inp.removeAttribute('title');
                    }

                    if (permissions.lead) {
                        permissions.lead.forEach((item) => {
                            document.querySelector(`input[name="lead_${item}"]`).checked = true;
                        })

                        if (permissions.lead.length == 5) {
                            document.querySelector('.lead_all_permission_checkox').checked = true;
                        }
                    }
                    if (permissions.task) {
                        permissions.task.forEach((item) => {
                            document.querySelector(`input[name="task_${item}"]`).checked = true;
                        })

                        if (permissions.task.length == 2) {
                            document.querySelector('.task_all_permission_checkox').checked = true;
                        }
                    }
                    if (permissions.visit) {
                        permissions.visit.forEach((item) => {
                            document.querySelector(`input[name="visit_${item}"]`).checked = true;
                        })

                        if (permissions.visit.length == 2) {
                            document.querySelector('.visit_all_permission_checkox').checked = true;
                        }
                    }
                    if (permissions.note) {
                        permissions.note.forEach((item) => {
                            document.querySelector(`input[name="note_${item}"]`).checked = true;
                        })

                        if (permissions.note.length == 2) {
                            document.querySelector('.note_all_permission_checkox').checked = true;
                        }
                    }
                    if (permissions.meeting) {
                        permissions.meeting.forEach((item) => {
                            document.querySelector(`input[name="meeting_${item}"]`).checked = true;
                        });

                        if (permissions.meeting.length ==
                            2) {
                            document.querySelector('.meeting_all_permission_checkox').checked = true;
                        }
                    }

                    modal.show();
                } else {
                    toastr[data.alert_type](data.message);
                }
            })
        }
    </script>
@endsection
