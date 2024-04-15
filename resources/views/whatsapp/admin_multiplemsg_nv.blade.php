<div class="modal fade" id="wa_msg_multiple" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="form_title">Send Message to all slected</h4>
                <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i
                        class="fa fa-times"></i></button>
            </div>
            <form action="" method="post" id="wa_msg_multiple_form">
                @csrf
                <div class="modal-body text-sm">
                    <div class="form-groupp">
                        <label for="">Select Campaign Name</label>
                        <select class="form-control" id="campain_name" name="campain_name">
                            <option value="" selected disabled>Select Campaign</option>
                        @foreach($whatsapp_campaigns as $whatsapp_campaign)
                        <option value="{{ $whatsapp_campaign->id }}">{{ $whatsapp_campaign->name }}--{{$whatsapp_campaign->team_name}}--{{ $whatsapp_campaign->is_rm_name == 1 ? 'Rm Name In Template' : 'Lead Name In Template' }}</option>
                        @endforeach
                        </select>
                    </div>
                        <input type="text" class="form-control" id="phone_inp_id_m" name="phone_number_id_m"
                            style="display: none">
                </div>
                <div class="modal-footer text-sm">
                    <a href="javascript:void(0);" class="btn btn-sm bg-secondary m-1" data-bs-dismiss="modal">Close</a>
                    <a href="javascript:void(0);" class="btn btn-sm text-light m-1"
                        style="background-color: var(--wb-dark-red);" id="sendMultiMessageBtn">Submit</a>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="//cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
<script>
    $("#sendMultiMessageBtn").click(function() {
        var recipientElement = document.getElementById("phone_inp_id_m");
        var camp_name = document.getElementById("campain_name");

        var data = {
            recipient: recipientElement.value,
            camp_name: camp_name.value,
            lead_id_type: '2',
        };
        $.ajax({
            url: '{{ route('whatsapp_chat.create_task_by_id') }}',
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    recipientElement.value = "";
                    camp_name.value = "";
                    var manageWhatsappChatModal = bootstrap.Modal.getInstance(document
                        .getElementById('wa_msg_multiple'));
                    manageWhatsappChatModal.hide();
                    toastr.success(response.message)
                } else {
                    toastr.error(response.message)
                }
            },
            error: function(xhr, status, error) {}
        });
    });
</script>
