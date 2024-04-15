<div class="modal fade" id="manageBookingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Booking</h4>
                <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
            </div>
            <form id="manageBookingForm" action="" method="post">
                <div class="modal-body text-sm">
                    @csrf
                    <div class="row">
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <label for="predefined_event_for_booking">Predefined Events<span class="text-danger">*</span></label>
                                <select class="form-control" id="predefined_event_for_booking" name="predefined_event" required onchange="fetch_event_details_for_booking(`{{route('team.event.manage_ajax')}}`, this.value)"></select>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <label for="menu_selected_for_booking">Menu Selected <span class="text-danger">*</span></label>
                                <select class="form-control" id="menu_selected_for_booking" name="menu_selected" required></select>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <label for="party_area_for_booking">Party Area <span class="text-danger">*</span></label>
                                <select class="form-control" id="party_area_for_booking" name="party_area" required></select>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <label>Event Name <span class="text-danger">*</span></label>
                                <input id="event_name_for_booking" type="text" class="form-control booking_event_info" placeholder="Enter event name" name="event_name" required>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <label>Event Date <span class="text-danger">*</span></label>
                                <input id="event_date_for_booking" type="date" min="{{date('Y-m-d')}}" class="form-control booking_event_info" name="event_date" required>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <label for="event_slot_for_booking">Event Slot <span class="text-danger">*</span></label>
                                <select id="event_slot_for_booking" class="form-control booking_event_info" name="event_slot" id="" required>
                                    <option value="" disabled selected>Select event slot</option>
                                    <option value="Lunch">Lunch</option>
                                    <option value="Dinner">Dinner</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3 mb-3">
                            <div class="form-group">
                                <label for="food_preference_for_booking">Food Preference <span class="text-danger">*</span></label>
                                <select id="food_preference_for_booking" class="form-control booking_event_info" name="food_Preference" required>
                                    <option value="" disabled selected>Select food preference</option>
                                    <option value="Veg">Veg</option>
                                    <option value="Non-Veg">Non-Veg</option>
                                    <option value="Both">Both</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3 mb-3">
                            <div class="form-group">
                                <label for="number_of_guest_for_booking">Number of Guest (PAX) <span class="text-danger">*</span></label>
                                <input id="number_of_guest_for_booking" type="text" class="form-control booking_event_info" placeholder="Enter number of guest" name="number_of_guest" onblur="calculate_gmv(this)" required>
                                <span class="text-danger ml-1 position-absolute d-none">Invalid value</span>
                            </div>
                        </div>
                        <div class="col-sm-3 mb-3">
                            <div class="form-group">
                                <label for="price_per_plate_for_booking">Price per Plate <span class="text-danger">*</span></label>
                                <input id="price_per_plate_for_booking" type="text" class="form-control booking_event_info" placeholder="Enter the price" name="price_per_plate" onblur="calculate_gmv(this)" required>
                                <span class="text-danger ml-1 position-absolute d-none">Invalid value</span>
                            </div>
                        </div>
                        <div class="col-sm-3 mb-3">
                            <div class="form-group">
                                <label for="total_gmv_for_booking">Total Amount (GMV)</label>
                                <input id="total_gmv_for_booking" type="text" class="form-control" placeholder="Enter the amount" name="total_gmv" readonly>
                                <span class="text-danger ml-1 position-absolute d-none">Invalid integer value</span>
                            </div>
                        </div>
                        <div class="col-sm-12 mb-3">
                            <label>Advance Amount</label>
                            <button type="button" class="btn btn-success btn-xs ml-3" onclick="add_more_advance_amount_field('advance_amount_field_container')"><i class="fa fa-add"></i></button>
                            <div id="advance_amount_field_container" class="row">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer text-sm">
                    <div class="col">
                        <p>
                            <span class="text-danger text-bold">*</span>
                            Fields are required.
                        </p>
                    </div>
                    <button type="button" class="btn btn-sm bg-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm text-light" style="background-color: var(--wb-dark-red);">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>