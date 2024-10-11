<style>
    #messageContent {
    white-space: pre-wrap;
    padding: 10px;
    color: black;
    background-color: #f1f1f1;
    border-radius: 10px;
    margin-top: 10px;
}
</style>
<div class="modal fade" id="aimessageModal" tabindex="-1" aria-labelledby="aimessageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="aimessageModalLabel">Wedding Banquets AI</h5>
          <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
        </div>
        <div class="modal-body">
          <form id="generateMessageForm">
            <div class="mb-3">
              <label for="aiprompt" class="form-label">Prompt</label>
              <textarea class="form-control" id="aiprompt" rows="3" placeholder="Enter vendor details..."></textarea>
            </div>
          </form>

          <div id="generatedMessageContainer" class="mt-3" style="display: none;">
            <h5>Generated Message:</h5>
            <div id="messageContent" class="alert alert-secondary"></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="sendMessageBtn" onclick="generateMessage()" style="background: var(--wb-dark-red); border: 1px solid var(--wb-dark-red);">Send</button>
        </div>
      </div>
    </div>
  </div>

<script>
    function generateMessage() {
    const aiprompt = document.getElementById('aiprompt').value;

    document.getElementById('messageContent').innerText = '';

    fetch(`{{route('ai.googleStudioPrompt')}}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: JSON.stringify({
            aiprompt: aiprompt
        }),
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('generatedMessageContainer').style.display = 'block';
        document.getElementById('messageContent').innerText = data.message;
    })
    .catch(error => {
        console.error('Error generating message:', error);
        document.getElementById('messageContent').innerText = 'Error generating message. Please try again.';
        document.getElementById('generatedMessageContainer').style.display = 'block';
    });
}
</script>
