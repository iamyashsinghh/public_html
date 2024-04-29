<nav class="main-header navbar navbar-expand navbar-dark navbar-light" style="background: var(--wb-renosand)">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a href="javascript:void(0);" class="nav-link" data-widget="pushmenu" id="sidebar_collapsible_elem" data-collapse="1" onclick="handle_sidebar_collapse(this)"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="javascript:void(0);" class="nav-link" data-bs-toggle="modal" data-bs-target="#createLeadModal">Create New Lead</a>
        </li>
    </ul>

    <ul class="navbar-nav ml-auto">
        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" title="Logout" onclick="return confirm('Are you sure want to logout?')" href="{{route('logout')}}">
                <i class="fas fa-power-off"></i>
            </a>
        </li>
        @yield('navbar-right-links')
    </ul>
</nav>


<div class="modal fade" id="createLeadModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Create New Lead</h4>
                <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
            </div>
            <form action="{{route('pvendor.lead.add.process')}}" method="post">
                <div class="modal-body text-sm">
                    @csrf
                    <div class="row">
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <label for="nv_lead_name_inp">Name</label>
                                <input type="text" class="form-control" id="nv_lead_name_inp" placeholder="Enter name" name="name">
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <label for="nv_lead_email_inp">Email</label>
                                <input type="email" class="form-control" id="nv_lead_email_inp" placeholder="Enter email" name="email" onblur="validate_email(this)">
                                <span class="text-danger ml-1 position-absolute d-none">Invalid email</span>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <label for="nv_lead_mobile_inp">Mobile No. <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nv_lead_mobile_inp" placeholder="Enter mobile no." name="mobile_number" onblur="validate_mobile_number(this, `{{route('nonvenue.lead.phoneNumber.validate')}}/${this.value}`)" required>
                                <span class="text-danger ml-1 position-absolute d-none">Invalid phone number</span>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-">
                            <div class="form-group">
                                <label for="nv_lead_alt_mobile_inp">Alternate Mobile No.</label>
                                <input type="text" class="form-control" id="nv_lead_alt_mobile_inp" placeholder="Enter alternate mobile no." name="alternate_mobile_number">
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <label for="nv_lead_event_name_inp">Event Name</label>
                                <input type="text" class="form-control" id="nv_lead_event_name_inp" placeholder="Enter event name" name="event_name">
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <label for="nv_lead_event_date_inp">Event Date</label>
                                <input type="date" min="{{date('Y-m-d')}}" class="form-control" id="nv_lead_event_date_inp" name="event_date">
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <label for="nv_lead_event_slot_select">Event Slot</label>
                                <select class="form-control" id="nv_lead_event_slot_select" name="event_slot">
                                    <option value="" selected disabled>Select event slot</option>
                                    <option value="Morning">Morning</option>
                                    <option value="Evening">Evening</option>
                                    <option value="Full Day">Full Day</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <label for="nv_lead_venue_name">Venue Name</label>
                                <input type="text" class="form-control" id="nv_lead_venue_name" placeholder="Enter venue name" name="venue_name">
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <label for="nv_lead_pax_inp">Number of Guest</label>
                                <input type="text" class="form-control" id="nv_lead_pax_inp" placeholder="Enter number of guest" name="number_of_guest">
                            </div>
                        </div>
                        <div class="col-sm-12 mb-3">
                            <div class="form-group">
                                <label for="nv_lead_address_textarea">Address</label>
                                <textarea type="text" class="form-control" id="nv_lead_address_textarea" placeholder="Enter address" name="address"></textarea>
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
                    <a class="btn btn-sm bg-secondary m-1" data-bs-dismiss="modal">Cancel</a>
                    <button type="submit" class="btn btn-sm text-light m-1" style="background-color: var(--wb-dark-red);">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

