<div class="modal fade" id="forwardLeadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header align-items-center">
                <h4 class="modal-title">Forward Lead's to NVRM's</h4>
                {{-- <input class="form-check-input position-static" id="select_all_rms" onclick="handle_select_all(this, '.checkbox_for_rm')" style="height: 1.5rem; width: 1.5rem;" type="checkbox"> --}}
                <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
            </div>
            <form action="{{route('admin.nvlead.forward')}}" method="post">
                <div class="modal-body text-sm">
                    @csrf
                    <input type="hidden" name="forward_leads_id" value="{{isset($lead) ? $lead->id : ''}}">
                    <div class="row">
                        {{-- @foreach ($nvrm_members as $list)
                        <div class="col-sm-4 mb-3">
                            <div class="form-check d-flex align-items-center">
                                <input class="form-check-input checkbox_for_rm" id="forward_rms_id_checkbox{{$list->id}}" type="checkbox" name="forward_rms_id[]" value="{{$list->id}}">
                                <label class="form-check-label" for="forward_rms_id_checkbox{{$list->id}}">{{$list->name}}</label>
                            </div>
                        </div>
                        @endforeach --}}
                        @foreach ($nvrm_members as $rm)
    <div class="custom-control custom-radio my-1 mx-2">
        <input class="custom-control-input" type="radio" name="forward_rms_id[]" id="team_member_{{ $rm->id }}" value="{{ $rm->id }}">
        <label for="team_member_{{ $rm->id }}" class="custom-control-label">{{ $rm->name }}</label>
    </div>
@endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{route('admin.lead.list')}}" class="btn btn-sm bg-secondary m-1" data-bs-dismiss="modal">Cancel</a>
                    <button type="submit" onclick="btn_preloader(this)" class="btn btn-sm text-light m-1" style="background-color: var(--wb-dark-red);">Forward</button>
                </div>
            </form>
        </div>
    </div>
</div>