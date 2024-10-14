<style>
    .card {
        border: none;
        box-shadow: none;
    }

    .card-send {
        display: flex;
        justify-content: end;
    }

    .whatsapp-card-send {
        background-color: #DCF8C6;
        border-radius: 12px;
        padding: 10px 15px;
        margin-bottom: 10px;
        max-width: 70%;
    }

    .whatsapp-card {
        background-color: #e4e0e0;
        border-radius: 12px;
        padding: 10px 15px;
        margin-bottom: 10px;
        max-width: 70%;
    }

    .whatsapp-message {
        font-size: 16px;
        line-height: 1.5;
    }

    .whatsapp-timestamp {
        text-align: right;
        font-size: 12px;
        color: #777;
    }

    .fa-check {
        color: blue;
    }

    .whatsapp-date {
        padding: 5px;
        background: #e7e7e7;
        border-radius: 10px;
        font-weight: 500;
        margin-bottom: 10px;
    }

    .modal-content {
        max-height: 630px;
        overflow-x: auto;
    }

    .modal-header {
        position: sticky;
        top: 0;
        z-index: 99;
        background: #fff;
        padding: 10px 1rem;
    }
</style>

<div class="modal fade" id="wa_msg" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="form_title_modal"></h4>
                <button type="button" class="btn text-secondary" id="close-whatsapp-chatmodal" data-bs-dismiss="modal"
                    aria-label="Close"><i class="fa fa-times"></i></button>
            </div>
            <div id="loadingMessages" style="display: none; text-align: center;">
                <img src="https://i.postimg.cc/NF6hL0rM/giphy.webp" alt="Loading..." />
            </div>
            <div class="modal-body whatsapp_msg text-sm"></div>
            <form action="" method="post" id="wa_msg_form">
                <div class="modal-body text-sm">
                    <div class="form-group d-none">
                        <label for="">Put image</label>
                        <input type="text" name="whatsapp_img" class="form-control" id="wha_img_input">
                    </div>
                    <div class="form-group">
                        <label for="">Message <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="what_msg_send" placeholder="Enter Message" name="msg" required></textarea>
                        <input type="number" class="form-control" id="phone_inp_id" name="phone_number_id"
                            style="display: none">
                    </div>
                </div>
                <div class="modal-footer text-sm">
                    <div class="mx-5">
                        <a href="javascript:void(0);" class="btn btn-sm m-1 text-light" style="background-color: var(--wb-dark-red)" id="sendMessageBtnGreetMsg">Rm Greet Msg</a>
                        <a href="javascript:void(0);" class="btn btn-sm m-1 text-light" style="background-color: var(--wb-dark-red)" id="sendMessageBtnHi">Hi</a>
                    </div>
                    <a href="javascript:void(0);" class="btn btn-sm btn-secondary m-1" data-bs-dismiss="modal">Close</a>
                    <a href="javascript:void(0);" class="btn btn-sm text-light m-1" style="background-color: var(--wb-dark-red);" id="sendMessageBtn">Submit</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
<script>
    let lastMessageTimestamp = '';
    let currentPage = 1;
    let isFetchingMessages = false;
    let hasMoreMessages = true;
    let messageFetchInterval;
    let newMessageFetchInterval;
    let displayedMessages = [];
    let messageSendElement = document.getElementById("what_msg_send");

    document.getElementById('close-whatsapp-chatmodal').addEventListener('click', resetChat);

    document.getElementById("sendMessageBtnHi").addEventListener('click', function() {
        handleMessageSend('hi', this);
    });

    document.getElementById("sendMessageBtnGreetMsg").addEventListener('click', function() {
        handleMessageSend('greet', this);
    });

    document.getElementById("sendMessageBtn").addEventListener('click', function() {
        handleMessageSend('message', this);
    });

    function resetChat() {
        currentPage = 1;
        document.querySelector('.whatsapp_msg').innerHTML = '';
        hasMoreMessages = true;
        isFetchingMessages = false;
        clearInterval(messageFetchInterval);
        clearInterval(newMessageFetchInterval);
        displayedMessages = [];
        lastMessageTimestamp = '';
    }

    function handleMessageSend(type, button) {
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
        button.disabled = true;

        const recipient = document.getElementById("phone_inp_id").value;
        const data = {
            recipient: recipient,
            message: type === 'message' ? document.getElementById('what_msg_send').value : type,
            greetmsg: type === 'greet' ? 'Hello! I am your assistant.' : null
        };

        const xhr = new XMLHttpRequest();
        xhr.open('POST', '{{ route('whatsapp_chat.send') }}', true);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                button.innerHTML = originalText;
                button.disabled = false;
                messageSendElement.value = '';
            } else if (xhr.readyState === 4) {
                button.innerHTML = originalText;
                button.disabled = false;
            }
        };
        xhr.send(JSON.stringify(data));
    }

    function wamsg(num) {
        resetChat();
        const id = num;
        document.getElementById("phone_inp_id").value = num;

        function fetchMessages(page) {
            if (isFetchingMessages || !hasMoreMessages) return;
            document.getElementById('loadingMessages').style.display = 'block';
            isFetchingMessages = true;

            const xhr = new XMLHttpRequest();
            xhr.open('GET', `{{ route('whatsapp_chat.get', '') }}/${id}?page=${page}`, true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    document.getElementById('loadingMessages').style.display = 'none';

                    if (response.data.length > 0) {
                        const groupedMessages = groupMessagesByDate(response.data);
                        updateHTML(groupedMessages);

                        if (currentPage === 1) {
                            lastMessageTimestamp = response.data[0].time;
                        }

                        if (currentPage >= response.last_page) {
                            hasMoreMessages = false;
                        } else {
                            currentPage++;
                        }
                    }

                    if (currentPage > response.last_page) {
                        hasMoreMessages = false;
                        clearInterval(messageFetchInterval);
                    }

                    isFetchingMessages = false;
                } else if (xhr.readyState === 4) {
                    isFetchingMessages = false;
                }
            };
            xhr.send();
        }

        fetchMessages(currentPage);

        messageFetchInterval = setInterval(function () {
            fetchMessages(currentPage);
        }, 4000);

        function fetchNewMessages() {
            if (lastMessageTimestamp !== '') {
                const xhr = new XMLHttpRequest();
                xhr.open('GET', `{{ route('whatsapp_chat.get_new', '') }}/${id}?lastTimestamp=${lastMessageTimestamp}`, true);
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        const response = JSON.parse(xhr.responseText);
                        if (response.length > 0) {
                            response.forEach(message => {
                                const messageHTML = buildMessageHTML(message);
                                document.querySelector('.whatsapp_msg').insertAdjacentHTML('beforeend', messageHTML);
                            });
                            lastMessageTimestamp = response[response.length - 1].time;
                        }
                    }
                };
                xhr.send();
            }
        }

        newMessageFetchInterval = setInterval(fetchNewMessages, 5000);
    }

    function groupMessagesByDate(messages) {
        const groupedMessages = {};
        messages.forEach(message => {
            const date = formatTimestamp(message.timestamp);
            if (!groupedMessages[date]) {
                groupedMessages[date] = [];
            }
            groupedMessages[date].push(message);
        });
        return groupedMessages;
    }

    function formatTimestamp(timestamp) {
        const messageDate = new Date(timestamp * 1000).toDateString();
        const today = new Date().toDateString();
        if (messageDate === today) {
            return 'Today';
        } else {
            return messageDate;
        }
    }

    function updateHTML(groupedMessages) {
        const whatsappMsgElement = document.querySelector('.whatsapp_msg');
        Object.keys(groupedMessages).forEach(date => {
            if (!displayedMessages.includes(date)) {
                displayedMessages.push(date);
                whatsappMsgElement.insertAdjacentHTML('beforeend', `<div class="whatsapp-date">${date}</div>`);
            }

            groupedMessages[date].forEach(message => {
                const messageHTML = buildMessageHTML(message);
                whatsappMsgElement.insertAdjacentHTML('beforeend', messageHTML);
            });
        });
    }

    function buildMessageHTML(message) {
        const messageAlignment = message.isSentByCurrentUser ? 'card-send' : '';
        const messageType = message.isSentByCurrentUser ? 'whatsapp-card-send' : 'whatsapp-card';
        const timestamp = moment(message.timestamp * 1000).format('LT');
        return `
            <div class="card ${messageAlignment}">
                <div class="${messageType}">
                    <p class="whatsapp-message">${message.message}</p>
                    <p class="whatsapp-timestamp">${timestamp} <i class="fa fa-check"></i></p>
                </div>
            </div>
        `;
    }
</script>
