<div class="modal fade" id="forwardLeadModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header align-items-center">
                <h4 class="modal-title">Assign Lead's to Bdm's</h4>
                <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
            </div>
            <form action="{{route('admin.bdm.lead.forward')}}" method="post">
                <div class="modal-body text-sm">
                    @csrf
                    <input type="hidden" name="forward_leads_id" value="{{isset($lead) ? $lead->lead_id : ''}}">
                    <div class="row">
                        @foreach ($getBdm as $list)
                        <div class="col-sm-4 mb-3">
                            <div class="form-check d-flex align-items-center">
                                <input class="form-check-input checkbox_for_rm" id="forward_bdm_id_checkbox{{$list->id}}" type="radio" name="forward_bdm_id" value="{{$list->id}}">
                                <label class="form-check-label" for="forward_bdm_id_checkbox{{$list->id}}">{{$list->name}}</label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{route('admin.bdm.lead.list')}}" class="btn btn-sm bg-secondary m-1" data-bs-dismiss="modal">Cancel</a>
                    <button type="submit" class="btn btn-sm text-light m-1" onclick="btn_preloader(this)" style="background-color: var(--wb-dark-red);">Forward
                </div>
            </form>
        </div>
    </div>
</div>
