<div class="modal fade" id="manageLeadModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Create Lead</h4>
                <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
            </div>
            <form id="manage_lead_form" action="{{route('manager.lead.add_process')}}" method="post">
                <div class="modal-body text-sm">
                    @csrf
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                        <label for="name_inp">Forward Lead to Vm <span class="text-danger">*</span></label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        @foreach ($vm_members as $list)
                        <div class="col-sm-4 mb-3">
                            <div class="form-check d-flex align-items-center">
                                <input class="form-check-input checkbox_for_vm" id="forward_vms_id_checkbox{{$list->id}}" type="checkbox" name="forward_vms_id[]" value="{{$list->id}}">
                                <label class="form-check-label" for="forward_vms_id_checkbox{{$list->id}}">{{$list->name}} ({{$list->venue_name}})</label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="row">
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <label for="name_inp">Name</label>
                                <input type="text" class="form-control" id="name_inp" placeholder="Enter name" name="name">
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <label for="email_inp">Email</label>
                                <input type="email" class="form-control" id="email_inp" placeholder="Enter email" name="email">
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <label for="mobile_inp">Mobile No. <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="mobile_inp" placeholder="Enter mobile no." name="mobile_number" required onblur="validate_mobile_number(this, `{{route('venue.lead.phoneNumber.validate')}}/${this.value}`)" min="10" max="10">
                                <span class="text-danger ml-1 position-absolute d-none">Invalid phone number</span>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-">
                            <div class="form-group">
                                <label for="alt_mobile_inp">Alternate Mobile No.</label>
                                <input type="text" class="form-control" id="alt_mobile_inp" placeholder="Enter alternate mobile no." name="alternate_mobile_number">
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <label for="locality_inp">Preferred Locality</label>
                                <input type="text" class="form-control" id="locality_inp" placeholder="Enter preferred locality." name="locality">
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <label for="lead_source_select">Lead Source</label>
                                <select class="form-control" id="lead_source_select" name="lead_source" required>
                                    <option value="VM|Reference" selected>VM|Reference</option>
                                    <option value="WB|Call">WB|Call</option>
                                    <option value="Walk-in">Walk-in</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <label for="lead_status_select">Lead Status</label>
                                <select class="form-control" id="lead_status_select" name="lead_status" required>
                                    <option value="Active">Active</option>
                                    <option value="Hot">Hot</option>
                                    <option value="Super Hot">Super Hot</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <label for="event_name_inp">Event Name</label>
                                <input type="text" class="form-control" id="event_name_inp" placeholder="Enter event name" name="event_name">
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <label for="event_date_inp">Event Date</label>
                                <input type="date" min="{{date('Y-m-d')}}" class="form-control" id="event_date_inp" name="event_date">
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <label for="event_slot_select">Event Slot</label>
                                <select class="form-control" id="event_slot_select" name="event_slot">
                                    <option value="" selected disabled>Select event slot</option>
                                    <option value="Lunch">Lunch</option>
                                    <option value="Dinner">Dinner</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <label for="food_Preference_select">Food Preference</label>
                                <select class="form-control" id="food_Preference_select" name="food_Preference">
                                    <option value="" disabled selected>Select food preference</option>
                                    <option value="Veg">Veg</option>
                                    <option value="Non-Veg">Non-Veg</option>
                                    <option value="Both">Both</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <label for="number_of_guest_inp">Number of Guest</label>
                                <input type="text" class="form-control" id="number_of_guest_inp" placeholder="Enter number of guest" name="number_of_guest">
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <label for="budget_inp">Budget (in INR)</label>
                                <input type="text" class="form-control" id="budget_inp" placeholder="Enter budget" name="budget">
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
                    <a href="{{route('admin.lead.list')}}" class="btn bg-secondary m-1" data-bs-dismiss="modal">Cancel</a>
                    <button type="submit" class="btn text-light m-1" style="background-color: var(--wb-dark-red);">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
