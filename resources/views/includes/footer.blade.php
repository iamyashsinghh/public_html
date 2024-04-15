<footer class="main-footer text-sm">
    <strong>Copyright &copy; {{date('Y')}} <a href="javascript:void(0);">Wedding Banquets</a>.</strong>
    All rights reserved.
</footer>
<script>
function handle_view_image(image_url, image_change_request_url = null) {
    const existingModal = document.getElementById('viewImageModal');
    if (existingModal) {
        existingModal.remove();
    }
    const defaultImageUrl = "{{ asset('images/default-user.png') }}";
    if(!image_url) {
        image_url = defaultImageUrl;
    }
    const div = document.createElement('div');
    div.classList = "modal fade";
    div.id = "viewImageModal";
    div.setAttribute("tabindex", "-1");
    const modal_elem = `
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Image</h4>
                    <button type="button" class="btn text-secondary" onclick="handle_remove_modal('viewImageModal')" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                </div>
                <div class="modal-body text-center">
                    <img src="${image_url}" class="rounded img-fluid" style="min-width: 20rem; height: 20rem;" />
                </div>
                <div class="modal-footer justify-content-between align-items-end">
                    <form action="${image_change_request_url}" method="post" class="w-50" enctype="multipart/form-data">
                        <div class="form-group">
                            <label>Update Image?</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="customFile" name="profile_image" required>
                                <label class="custom-file-label" for="customFile">Choose file</label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-sm m-1 text-light" style="background-color: var(--wb-dark-red);">Submit</button>
                    </form>
                    <button type="button" class="btn btn-sm btn-secondary" onclick="handle_remove_modal('viewImageModal')" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    `;
    div.innerHTML = modal_elem;
    document.body.appendChild(div);
    const modal = new bootstrap.Modal(div);
    modal.show();
    const fileInput = document.querySelector('#customFile');
    const label = document.querySelector('label[for="customFile"]');
    fileInput.addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            label.textContent = file.name;
            const img = document.querySelector('.modal-body img');
            img.src = URL.createObjectURL(file);
        }
    });
}
function handle_remove_modal(modalId) {
    const modalElement = document.getElementById(modalId);
    if (modalElement) {
        modalElement.remove();
    }
}
</script>

{{-- </script> --}}
