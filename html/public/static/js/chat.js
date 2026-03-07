document.addEventListener('DOMContentLoaded', function () {
    let STORAGE_KEY = 'chat_username';
    let overlay = document.getElementById('name-overlay');
    let nameInput = document.getElementById('name-input');
    let nameSubmitBtn = document.getElementById('name-submit');
    let badge = document.getElementById('username-badge');
    let form = document.getElementById('chat-form');
    let messagesContainer = document.getElementById('messages');
    let textInput = document.getElementById('text');
    let sendBtn = document.getElementById('send-btn');
    let currentUser = localStorage.getItem(STORAGE_KEY) || '';

    function initChat() {
        if (!currentUser) {
            overlay.classList.remove('hidden');
            nameInput.focus();
        } else {
            overlay.classList.add('hidden');
            badge.textContent = currentUser;
            markExistingMessages();
        }
    }

    function setUsername(name) {
        currentUser = name.trim();
        localStorage.setItem(STORAGE_KEY, currentUser);
        badge.textContent = currentUser;
        overlay.classList.add('hidden');
        markExistingMessages();
        textInput.focus();
    }

    nameSubmitBtn.addEventListener('click', function () {
        let name = nameInput.value.trim();
        if (name) setUsername(name);
    });

    nameInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            let name = nameInput.value.trim();
            if (name) setUsername(name);
        }
    });

    badge.addEventListener('click', function () {
        showRenameOverlay();
    });

    function showRenameOverlay() {
        let existing = document.getElementById('rename-overlay');
        if (existing) existing.remove();

        let renameOverlay = document.createElement('div');
        renameOverlay.id = 'rename-overlay';
        renameOverlay.className = 'name-overlay';
        renameOverlay.innerHTML =
            '<div class="name-card">' +
            '<h2>✏️ Сменить имя</h2>' +
            '<p>Введите новое имя для чата</p>' +
            '<input type="text" id="rename-input" placeholder="Новое имя..." maxlength="30" value="' + escapeHtml(currentUser) + '">' +
            '<div class="rename-buttons">' +
            '<button type="button" id="rename-cancel" class="btn-cancel">Отмена</button>' +
            '<button type="button" id="rename-submit">Сохранить</button>' +
            '</div>' +
            '</div>';
        document.body.appendChild(renameOverlay);

        let renameInput = document.getElementById('rename-input');
        renameInput.focus();
        renameInput.select();

        document.getElementById('rename-submit').addEventListener('click', function () {
            let name = renameInput.value.trim();
            if (name) {
                setUsername(name);
                renameOverlay.remove();
            }
        });

        document.getElementById('rename-cancel').addEventListener('click', function () {
            renameOverlay.remove();
        });

        renameInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                let name = renameInput.value.trim();
                if (name) {
                    setUsername(name);
                    renameOverlay.remove();
                }
            }
            if (e.key === 'Escape') {
                renameOverlay.remove();
            }
        });

        renameOverlay.addEventListener('click', function (e) {
            if (e.target === renameOverlay) renameOverlay.remove();
        });
    }

    function markExistingMessages() {
        let messages = messagesContainer.querySelectorAll('.message[data-author]');
        messages.forEach(function (el) {
            let author = el.getAttribute('data-author');
            el.classList.remove('me', 'other');
            el.classList.add(author === currentUser ? 'me' : 'other');
        });
    }

    textInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            form.dispatchEvent(new Event('submit', {cancelable: true}));
        }
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        let text = textInput.value.trim();
        if (!text || !currentUser) return;

        sendBtn.disabled = true;
        sendBtn.textContent = '...';

        fetch('/', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'Accept': 'application/json',
            },
            body: new URLSearchParams({author: currentUser, text: text, created_at: null}),
        })
            .then(function (response) {
                if (!response.ok) throw new Error('Ошибка отправки');
            })
            .then(function () {
                textInput.value = '';
                textInput.focus();
            })
            .catch(function (err) {
                alert(err.message || 'Не удалось отправить сообщение');
            })
            .finally(function () {
                sendBtn.disabled = false;
                sendBtn.textContent = 'Отправить';
            });
    });

    function appendMessage(msg) {
        let empty = document.getElementById('empty-state');
        if (empty) empty.remove();

        let isMe = msg.author === currentUser;
        let div = document.createElement('div');
        div.className = 'message ' + (isMe ? 'me' : 'other');
        div.setAttribute('data-author', msg.author);
        div.innerHTML =
            '<div class="message-author">' + escapeHtml(msg.author) + '</div>' +
            '<div class="message-text">' + escapeHtml(msg.text).replace(/\n/g, '<br>') + '</div>' +
            '<div class="message-time">' + escapeHtml(msg.created_at ? new Date(msg.created_at * 1000).toISOString().slice(0, 19).replace('T', ' ') : '') + '</div>';

        messagesContainer.appendChild(div);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    function escapeHtml(str) {
        let d = document.createElement('div');
        d.appendChild(document.createTextNode(str));
        return d.innerHTML;
    }

    function connectSse() {
        let source = new EventSource('/sse');

        source.onmessage = function (event) {
            try {
                let msg = JSON.parse(event.data);
                appendMessage(msg);
            } catch (e) {
                // ignore non-JSON messages
            }
        };

        source.onerror = function () {
            source.close();
            setTimeout(connectSse, 3000);
        };
    }

    connectSse();

    messagesContainer.scrollTop = messagesContainer.scrollHeight;
    initChat();
});
