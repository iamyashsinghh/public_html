<div class="modal fade" id="forwardLeadModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header modal-xl">
                <h4 class="modal-title">Forward Lead's to VM's</h4>
                <input class="form-check-input position-static" id="select_all_vms" onclick="handle_select_all(this, '.checkbox_for_vm')" style="height: 1.5rem; width: 1.5rem;" type="checkbox">
                <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
            </div>
            <form action="{{route('manager.lead.forward')}}" method="post">
                <div class="modal-body text-sm">
                    @csrf
                    <input type="hidden" name="forward_leads_id" value="{{isset($lead) ? $lead->lead_id : ''}}">
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
                </div>
                <div class="modal-body text-sm">
                    <div class="row">
                        <label>Reason For Forwading <span class="text-danger">*</span></label>
                       <textarea name="forword_lead_reason" class="form-control" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{route('manager.lead.list')}}" class="btn btn-sm bg-secondary m-1" data-bs-dismiss="modal">Cancel</a>
                    <button type="submit" class="btn btn-sm text-light m-1" style="background-color: var(--wb-dark-red);">Forward</button>
                </div>
            </form>
        </div>
    </div>
</div>
