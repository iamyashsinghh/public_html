<style>
    #messageContent {
        white-space: pre-wrap;
        padding: 10px;
        color: black;
        background-color: #f1f1f1;
        border-radius: 10px;
        margin-top: 10px;
    }

    .btn-msg-type {
        margin-right: 5px;
    }

    .btn-msg-type.active {
        background-color: var(--wb-dark-red);
        color: white;
    }
</style>

<div class="modal fade" id="aimessageModal" tabindex="-1" aria-labelledby="aimessageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="aimessageModalLabel">Wedding Banquets AI</h5>
                <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="generateMessageForm">
                    <div class="mb-3 d-none">
                        <label for="msg_type" class="form-label">Msg Type</label>
                        <div id="msgTypeButtons">
                            <button type="button" class="btn btn-secondary btn-msg-type" data-value="normal">Normal</button>
                            <button type="button" class="btn btn-secondary btn-msg-type" data-value="rm_msg">RM Msg</button>
                            <button type="button" class="btn btn-secondary btn-msg-type" data-value="nvrm_msg">NVRM Msg</button>
                        </div>
                        <input type="hidden" id="msg_type" name="msg_type" value="normal">
                    </div>
                    <div class="mb-3">
                        <label for="aiprompt" class="form-label">Prompt</label>
                        <textarea class="form-control" id="aiprompt" rows="3" placeholder="Enter Prompt..."></textarea>
                    </div>
                </form>

                <div id="generatedMessageContainer" class="mt-3" style="display: none;">
                    <h5>Generated Message:</h5>
                    <div id="messageContent" class="alert alert-secondary"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="sendMessageBtnAI" onclick="generateMessage()"
                    style="background: var(--wb-dark-red); border: 1px solid var(--wb-dark-red);">Send</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const msgTypeButtons = document.querySelectorAll('.btn-msg-type');
        const msgTypeInput = document.getElementById('msg_type');

        // Add click event to each button
        msgTypeButtons.forEach(button => {
            button.addEventListener('click', function () {
                // Remove active class from all buttons
                msgTypeButtons.forEach(btn => btn.classList.remove('active'));

                // Add active class to the clicked button
                button.classList.add('active');

                // Set the hidden input value based on the button clicked
                msgTypeInput.value = button.getAttribute('data-value');
            });
        });
    });

    function generateMessage() {
        const aiprompt = document.getElementById('aiprompt').value;
        const msgType = document.getElementById('msg_type').value;

        document.getElementById('messageContent').innerText = '';

        fetch(`{{ route('ai.chatgptrompt') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({
                    aiprompt: aiprompt,
                    msg_type: msgType
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
