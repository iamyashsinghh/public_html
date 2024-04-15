@extends('admin.layouts.app')
@section('title', 'Team Member Profile | Venue CRM')
@section('main')
<div class="content-wrapper pb-5 text-sm">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Team Member Profile</h1>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3">
                    <div class="card card-primary card-outline">
                        <div class="card-body box-profile">
                            <div class="text-center">
                                <img style="height: 100px; cursor: pointer;" class="profile-user-img img-fluid img-circle" src="{{asset($member->profile_image)}}" onerror="this.onerror=null; this.src='{{asset('images/default-user.png')}}'" alt="User profile picture" {{$member->profile_image ? "onclick=handle_view_image(`$member->profile_image`)" : ''}}>
                            </div>
                            <h3 class="profile-username text-center">{{$member->name}}</h3>
                            <p class="text-muted text-center">{{$member->get_role->name}}</p>
                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b>Mobile</b> <a href="tel:{{$member->mobile}}" class="float-right">{{$member->mobile}}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Email</b> <a class="float-right" href="mail:{{$member->email}}">{{$member->email}}</a>
                                </li>
                            </ul>
                            <a target="_blank" href="{{route('admin.bypass.login', $member->id)}}" onclick="return confirm('Login confirmation..')" class="btn btn-sm btn-block text-light" style="background-color: var(--wb-renosand);">
                                <b>Login</b>
                                <i class="fa fa-sign-in-alt mx-1"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-9">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="mb-0 float-left">Profile Info</h4>
                            <a href="{{route('admin.team.edit', $member->id)}}" class="float-right text-dark" title="Edit member."><i class="fa fa-edit"></i></a>
                        </div>
                        <div class="card-body">
                            <div class="container-fluid">
                                <ul class="list-group">
                                    <li class="list-group-item border-0 ps-0 pt-0 text-sm">
                                        <span>Venue Name: &nbsp;</span>
                                        <strong class="text-dark">{{$member->venue_name}}</strong>
                                    </li>
                                    <li class="list-group-item border-0 ps-0 pt-0 text-sm">
                                        <span>Manager: &nbsp;</span>
                                        <strong class="text-dark">{{$member->get_manager ?$member->get_manager->name : 'N/A'}}</strong>
                                    </li>
                                    <li class="list-group-item border-0 ps-0 pt-0 text-sm d-flex align-items-center" style="column-gap: 2rem">
                                        <span>Status: &nbsp;</span>
                                        <a href="{{route('admin.team.update.status', [$member->id, $member->status == 1 ? 0 : 1])}}" style="font-size: 22px;"><i class="fa {{$member->status == 1 ? 'fa-toggle-on text-success' : 'fa-toggle-off text-danger'}}"></i></a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h4 class="mb-0 float-left">Party Area</h4>
                            <a href="javascript:void(0);" class="float-right text-dark" title="Add party area." onclick="handle_manage_party_area()"><i class="fa fa-plus"></i></a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                     <thead>
                                         <tr>
                                             <th>S.No.</th>
                                             <th>Name</th>
                                             <th class="text-nowrap">Created At</th>
                                             <th class="text-center">Action</th>
                                         </tr>
                                     </thead>
                                     <tbody>
                                        @if (sizeof($member->get_party_areas) > 0)
                                            @foreach ($member->get_party_areas as $key => $list)
                                            <tr>
                                                <td>{{$key+1}}</td>
                                                <td>{{$list->name}}</td>
                                                <td class="text-nowrap">{{date('d-m-Y h:i a', strtotime($list->created_at))}}</td>
                                                <td class="text-center">
                                                    <a href="javascript:void(0);" class="text-success mx-2" onclick="handle_manage_party_area('{{$list->name}}', '{{$list->id}}')"><i class="fa fa-edit"></i></a>
                                                    <a href="{{route('admin.partyArea.delete', $list->id)}}" onclick="return confirm('Are you sure want to delete.')" class="text-danger mx-2"><i class="fa fa-trash-alt"></i></a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td class="text-center" colspan="4">No data available in table</td>
                                            </tr>    
                                        @endif
                                     </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h4 class="mb-0 float-left">Food Preference</h4>
                            <a href="javascript:void(0);" class="float-right text-dark" title="Add food preference." onclick="handle_manage_food_preference()"><i class="fa fa-plus"></i></a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                     <thead>
                                         <tr>
                                             <th>S.No.</th>
                                             <th>Name</th>
                                             <th class="text-nowrap">Created At</th>
                                             <th class="text-center">Action</th>
                                         </tr>
                                     </thead>
                                     <tbody>
                                        @if(sizeof($member->get_food_preferences) > 0)
                                            @foreach ($member->get_food_preferences as $key => $list)
                                            <tr>
                                                <td>{{$key+1}}</td>
                                                <td>{{$list->name}}</td>
                                                <td class="text-nowrap">{{date('d-m-Y h:i a', strtotime($list->created_at))}}</td>
                                                <td class="text-center">
                                                    <a href="javascript:void(0);" class="text-success mx-2" onclick="handle_manage_party_area('{{$list->name}}', '{{$list->id}}')"><i class="fa fa-edit"></i></a>
                                                    <a href="{{route('admin.foodPreference.delete', $list->id)}}" onclick="return confirm('Are you sure want to delete.')" class="text-danger mx-2"><i class="fa fa-trash-alt"></i></a>
                                                </td>
                                            </tr>
                                            @endforeach
                                         @else
                                            <tr>
                                                <td class="text-center" colspan="4">No data available in table</td>
                                            </tr>    
                                     @endif
                                     </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="modal fade" id="managePartyAreaModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title mb-0">Add Party Area</h4>
                    <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                </div>
                <form id="manage_party_area_form" action="" method="post">
                    @csrf
                    <input type="hidden" name="member_id" value="{{$member->id}}">
                    <div id="party_area_input_container" class="modal-body"></div>
                    <div class="modal-footer text-sm">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-sm text-light" style="background-color: var(--wb-dark-red);">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="manageFoodPreferenceModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title mb-0">Add Food Preference</h4>
                    <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                </div>
                <form id="manage_food_preference_form" action="" method="post">
                    @csrf
                    <input type="hidden" name="member_id" value="{{$member->id}}">
                    <div id="food_preference_input_container" class="modal-body">
                        
                    </div>
                    <div class="modal-footer text-sm">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-sm text-light" style="background-color: var(--wb-dark-red);">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('footer-script')
<script>
    function add_more_party_area(){
        const div = document.createElement('div');
        div.classList.add('form-group');
    
        const elem = `<div class="d-flex align-items-center justify-content-between">
            <input type="text" style="width: 95%" class="form-control" placeholder="Enter name" name="party_area_name[]" required>
            <a href="javascript:void(0);" onclick="remove_party_area(this)" class="text-danger"><i class="fa fa-times"></i></a>
        </div>`;
        div.innerHTML = elem;
        party_area_input_container.append(div);
    }

    function remove_party_area(elem){
        elem.parentElement.parentElement.remove();
    }

    function handle_manage_party_area(area_name = '', id = 0){
        const managePartyAreaModal = document.getElementById("managePartyAreaModal");
        const modal = new bootstrap.Modal("#managePartyAreaModal");
        const modal_heading = managePartyAreaModal.querySelector('.modal-title');
        manage_party_area_form.action = `{{route('admin.partyArea.manage.process')}}/${id}`;
        if(id > 0){
            elem = `<div class="form-group">
                <label for="party_area_name_inp">Party Area Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="party_area_name_inp" placeholder="Enter name" name="party_area_name" value="${area_name}" required>
            </div>`;
            party_area_input_container.innerHTML = elem;
            modal_heading.innerText = "Edit Party Area";
        }else{
            elem = ` <div class="form-group">
                <label for="party_area_name_inp">Party Area Name <span class="text-danger">*</span></label>
                <div class="d-flex align-items-center justify-content-between">
                    <input type="text" style="width: 95%" class="form-control" id="party_area_name_inp" placeholder="Enter name" name="party_area_name[]" required>
                    <a href="javascript:void(0);" onclick="add_more_party_area()" class="text-success"><i class="fa fa-plus"></i></a>
                </div>
            </div>`;
            party_area_input_container.innerHTML = elem;
            modal_heading.innerText = "Add Party Area";
        }
        modal.show();
    }

    function handle_manage_food_preference(name = '', id = 0){
        const manageFoodPreferenceModal = document.getElementById("manageFoodPreferenceModal");
        const modal = new bootstrap.Modal("#manageFoodPreferenceModal");
        const modal_heading = manageFoodPreferenceModal.querySelector('.modal-title');
        manage_food_preference_form.action = `{{route('admin.foodPreference.manage.process')}}/${id}`;
        if(id > 0){
            elem = `<div class="form-group">
                <label for="food_preference_name_inp">Food Preference Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="food_preference_name_inp" placeholder="Enter name" name="food_preference_name" required>
            </div>`;
            food_preference_input_container.innerHTML = elem;
            modal_heading.innerText = "Edit Food Preference";
        }else{
            elem = `<div class="form-group">
                <label for="food_preference_name_inp">Food Preference Name <span class="text-danger">*</span></label>
                <div class="d-flex align-items-center justify-content-between">
                    <input type="text" style="width: 95%" class="form-control" id="food_preference_name_inp" placeholder="Enter name" name="food_preference_name[]" required>
                    <a href="javascript:void(0);" onclick="add_more_food_preference()" class="text-success"><i class="fa fa-plus"></i></a>
                </div>
            </div>`;
            food_preference_input_container.innerHTML = elem;
            modal_heading.innerText = "Add Food Preference";
        }
        modal.show();
    }

    function add_more_food_preference(){
        const div = document.createElement('div');
        div.classList.add('form-group');
    
        const elem = `<div class="d-flex align-items-center justify-content-between">
            <input type="text" style="width: 95%" class="form-control" placeholder="Enter name" name="food_preference_name[]" required>
            <a href="javascript:void(0);" onclick="remove_food_preference(this)" class="text-danger"><i class="fa fa-times"></i></a>
        </div>`;
        div.innerHTML = elem;
        food_preference_input_container.append(div);
    }

    function remove_food_preference(elem){
        elem.parentElement.parentElement.remove();
    }

</script>
@endsection