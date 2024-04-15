@extends('admin.layouts.app')

@section('header-css')
<link rel="stylesheet" href="//cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
@endsection

@section('title', "Whatsapp Campaign")

@section('main')
<div class="content-wrapper pb-5">
    <section class="content-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Whatsapp Campaign</h1>
                </div>
                <div class="d-flex">
                    <button class="btn btn-sm text-light" onclick="manage_campain()"
                        style="background-color: var(--wb-renosand);">New Campaign</button>
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
                            <th class="text-nowrap">Assign To</th>
                            <th class="text-nowrap">Template Name</th>
                            <th class="text-nowrap">Status</th>
                            <th class="text-nowrap">Created At</th>
                            <th class="text-nowrap">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </section>
    <div class="modal fade" id="manageWhatCampModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="manageWhatCampModalHeading">Add Campaign</h4>
                    <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i
                            class="fa fa-times"></i></button>
                </div>
                <form id="manage_whatsapp_camp_form" method="post" enctype="multipart/form-data">
                    <div class="modal-body text-sm">
                        @csrf
                        <div class="row">
                            <div class="col-sm-4 mb-3">
                                <div class="form-group">
                                    <label for="WhatCamp_name_inp">Campaign Name</label>
                                    <input type="name" class="form-control" id="WhatCamp_name_inp"
                                        placeholder="Enter Campaign Name" name="name">
                                </div>
                            </div>
                            <div class="col-sm-4 mb-3">
                                <div class="form-group">
                                    <label for="template_name">Select Template</label>
                                    <select class="form-control" id="template_name" name="template_name">
                                        <option value="" selected disabled>Select Template</option>
                                        @foreach($templates as $template)
                                            <option value="{{ $template->template_name }}">{{ $template->template_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4 mb-3">
                                <div class="form-group">
                                    <label for="team_member">Select Team Member</label>
                                    <select class="form-control" id="team_member" name="team_member">
                                        <option value="" selected disabled>Select Team Member</option>
                                        @foreach($teamdata as $team)
                                            <option value="{{ $team->id }}">{{ $team->name}}---{{$team->venue_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4 mb-3">
                                <div class="form-group">
                                    <label for="is_rm_name">RM Name / Lead Name</label>
                                    <select class="form-control" id="is_rm_name" name="is_rm_name">
                                        <option value="" selected disabled>Select Team Member</option>
                                        <option value="0">Value Of Lead Name</option>
                                        <option value="1">Value of Rm Name</option>
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
                url: "{{ route('whatsapp_chat.campain_ajax') }}",
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                },
                method: "get",
                dataSrc: "data",
            },
            columns: [
                { name: "id", data: "id" },
                { name: "name", data: "name" },
                { name: "team_name", data: "team_name"},
                { name: "template_name", data: "template_name"},
                { name: "status", data: "status"},
                { name: "created_at", data: "created_at"},
                { name: "action", data: "id", orderable: false, searchable: false},
            ],
            order: [[0, 'desc']],
            rowCallback: function(row, data, index) {
                const td_elements = row.querySelectorAll('td');
                const action_btns =
                        `<a href="javascript:void(0);" class="text-success mx-2" title="Edit"><i class="fa fa-edit" style="font-size: 15px;" onclick="manage_campain(${data.id})"></i></a>
                         <a href="{{ route('admin.campaign.delete') }}/${data.id}" onclick="return confirm('Are you sure want to delete?')" class="text-danger mx-2" title="Delete"><i class="fa fa-trash-alt" style="font-size: 15px;"></i></a>`
                td_elements[0].innerText = data.id;
                td_elements[1].innerText = data.name;
                td_elements[2].innerText = data.team_name;
                td_elements[3].innerText = data.template_name;
                status_action_elem =
                        `<a href="{{ route('admin.campaign.update.status') }}/${data.id}/${data.status == 1 ? 0 : 1}" style="font-size: 22px;"><i class="fa ${data.status == 1 ? 'fa-toggle-on text-success' : 'fa-toggle-off text-danger'} "></i></a>`;
                td_elements[4].innerHTML = status_action_elem;
                td_elements[5].innerText = data.created_at ? moment(data.created_at).format(
                        "DD-MMM-YYYY") : 'N/A';
                td_elements[6].innerHTML = action_btns;
            }
        });
    });
    function manage_campain(campaign_id = 0) {
            const manageWhatCampModal = document.getElementById('manageWhatCampModal');
            const modal = new bootstrap.Modal(manageWhatCampModal);
            const submit_url = `{{ route('admin.campaign.manage.process') }}/${campaign_id}`;
            manage_whatsapp_camp_form.action = submit_url;
            if (campaign_id > 0) {
                fetch(`{{ route('admin.campaign.edit') }}/${campaign_id}`).then(response => response.json()).then(data => {
                    if (data.success === true) {
                        WhatCamp_name_inp.value = data.campaign.name;
                        team_member.querySelector(`option[value="${data.campaign.assign_to}"]`).selected = true;
                        template_name.querySelector(`option[value="${data.campaign.template_name}"]`).selected = true;
                        is_rm_name.querySelector(`option[value="${data.campaign.is_rm_name}"]`).selected = true;
                        manageWhatCampModalHeading.innerText = "Edit Campaign";
                        modal.show();
                    } else {
                        toastr[data.alert_type](data.message);
                    }
                })
            } else {
                const inps = manageLeadModal.querySelectorAll("input");
                manageWhatCampModalHeading.innerText = "Add Vendor";
                for (let inp of inps) {
                    inp.value = null;
                }

            }
            modal.show();
        }
</script>
@endsection
