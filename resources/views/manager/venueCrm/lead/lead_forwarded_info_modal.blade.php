<div class="modal fade" id="leadForwardedMemberInfo" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header text-sm">
                <h4 class="modal-title">Forward Information</h4>
                <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
            </div>
            <div class="modal-body">
                <p id="last_forwarded_info_paragraph" class="text-sm mb-2"></p>
                <div class="table-responsive">
                    <table id="clientTable" class="table text-sm">
                        <thead>
                            <tr>
                                <th class="text-nowrap">S.No.</th>
                                <th class="text-nowrap">Name</th>
                                <th class="text-nowrap">Venue Name</th>
                                <th class="text-nowrap">Read Status</th>
                                <th class="text-nowrap">Forwarded At</th>
                            </tr>
                        </thead>
                        <tbody id="forward_info_table_body">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>