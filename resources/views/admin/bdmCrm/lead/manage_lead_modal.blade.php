<div class="modal fade" id="manageBdmLeadModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Create Lead</h4>
                <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
            </div>
            <form action="{{route('admin.bdm.lead.add.process')}}" method="post">
                <div class="modal-body text-sm">
                    @csrf
                    <div class="row">
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <label for="business_cat_inp">Business Category</label>
                                <select name="business_cat"  class="form-control" id="business_cat_inp" >
                                    <option value="" disabled selected>Select Category</option>
                                    @foreach ($vendor_categories as $list)
                                        <option value="{{ $list->id }}">{{ $list->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <label for="business_name_inp">Business Name</label>
                                <input type="text" class="form-control" id="business_name_inp" placeholder="Enter Business Name." name="business_name" value="">
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <label for="name_inp">Vendor Name</label>
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
                                <input type="text" class="form-control" id="mobile_inp" placeholder="Enter mobile no." name="mobile" onblur="validate_mobile_number(this, `{{route('bdm.lead.phoneNumber.validate')}}/${this.value}`)" min="10" max="10" required>
                                <span class="text-danger ml-1 position-absolute d-none">Invalid phone number</span>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-">
                            <div class="form-group">
                                <label for="alt_mobile_inp">Alternate Mobile No.</label>
                                <input type="text" class="form-control" id="alt_mobile_inp" placeholder="Enter alternate mobile no." name="alternate_mobile">
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <label for="city_inp">City</label>
                                <input type="text" class="form-control" id="city_inp" placeholder="Enter City." name="city">
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3 d-none">
                            <div class="form-group">
                                <label for="lead_source_select">Lead Source</label>
                                <select class="form-control" id="lead_source_select" name="source" required>
                                    <option value="WB|Team">WB|Team</option>
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
                    </div>
                </div>
                <div class="modal-footer text-sm">
                    <div class="col">
                        <p>
                            <span class="text-danger">*</span>
                            Fields are required.
                        </p>
                    </div>
                    <a href="{{route('bdm.lead.list')}}" class="btn bg-secondary m-1" data-bs-dismiss="modal">Cancel</a>
                    <button type="submit" class="btn text-light m-1" style="background-color: var(--wb-dark-red);">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function handle_create_bdm_lead() {
            const manageBdmLeadModal = new bootstrap.Modal(document.getElementById('manageBdmLeadModal'));
            manageBdmLeadModal.show();
        }
</script>
