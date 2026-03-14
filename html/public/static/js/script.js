class ApiClient {
    constructor(historyUrl, storeUrl) {
        this.historyUrl = historyUrl;
        this.storeUrl = storeUrl;
    }

    loadHistory() {
        return fetch(this.historyUrl, {
            method: 'GET',
            headers: {'Accept': 'application/json'},
        }).then(function (response) {
            if (!response.ok) throw new Error('Ошибка загрузки истории');
            return response.json();
        });
    }

    sendMessage(author, text) {
        return fetch(this.storeUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'Accept': 'application/json',
            },
            body: new URLSearchParams({author: author, text: text, created_at: null}),
        }).then(function (response) {
            if (!response.ok) throw new Error('Ошибка отправки');
        });
    }
}

class SseClient {
    constructor(url, onMessage) {
        this.url = url;
        this.onMessage = onMessage;
        this._connect();
    }

    _connect() {
        let source = new EventSource(this.url);

        source.onmessage = (event) => {
            try {
                this.onMessage(JSON.parse(event.data));
            } catch (e) {
                // ignore non-JSON messages
            }
        };

        source.onerror = () => {
            source.close();
            setTimeout(() => this._connect(), 3000);
        };
    }
}

class UserManager {
    constructor(storageKey) {
        this.storageKey = storageKey;
        this.currentUser = localStorage.getItem(storageKey) || '';
    }

    save(name) {
        this.currentUser = name.trim();
        localStorage.setItem(this.storageKey, this.currentUser);
    }

    isSet() {
        return Boolean(this.currentUser);
    }
}

class ChatApp {
    constructor() {
        this.STORAGE_KEY = 'chat_username';
        this.HISTORY_URL = '/api/history';
        this.STORE_URL = '/api/store';
        this.SSE_URL = '/sse';

        this.overlay = document.getElementById('name-overlay');
        this.nameInput = document.getElementById('name-input');
        this.nameSubmitBtn = document.getElementById('name-submit');
        this.badge = document.getElementById('username-badge');
        this.form = document.getElementById('chat-form');
        this.messagesContainer = document.getElementById('messages');
        this.textInput = document.getElementById('text');
        this.sendBtn = document.getElementById('send-btn');

        this.userManager = new UserManager(this.STORAGE_KEY);
        this.api = new ApiClient(this.HISTORY_URL, this.STORE_URL);

        this._bindEvents();
        this._init();
    }

    _init() {
        if (!this.userManager.isSet()) {
            this.overlay.classList.remove('hidden');
            this.nameInput.focus();
        } else {
            this.overlay.classList.add('hidden');
            this.badge.textContent = this.userManager.currentUser;
            this._markExistingMessages();
        }

        this.api.loadHistory()
            .then((payload) => {
                let history = Array.isArray(payload) ? payload : [];
                this.messagesContainer.innerHTML = '';

                if (history.length === 0) {
                    this._renderEmptyState();
                    return;
                }

                history.forEach((message) => this._appendMessage(this._normalizeMessage(message)));
            })
            .catch((err) => console.error(err))
            .finally(() => {
                new SseClient(this.SSE_URL, (raw) => this._appendMessage(this._normalizeMessage(raw)));
                this.messagesContainer.scrollTop = this.messagesContainer.scrollHeight;
                this._markExistingMessages();
            });
    }

    _bindEvents() {
        this.nameSubmitBtn.addEventListener('click', () => {
            let name = this.nameInput.value.trim();
            if (name) this._setUsername(name);
        });

        this.nameInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                let name = this.nameInput.value.trim();
                if (name) this._setUsername(name);
            }
        });

        this.badge.addEventListener('click', () => this._showRenameOverlay());

        this.textInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.form.dispatchEvent(new Event('submit', {cancelable: true}));
            }
        });

        this.form.addEventListener('submit', (e) => {
            e.preventDefault();

            let text = this.textInput.value.trim();
            if (!text || !this.userManager.isSet()) return;

            this.sendBtn.disabled = true;
            this.sendBtn.textContent = '...';

            this.api.sendMessage(this.userManager.currentUser, text)
                .then(() => {
                    this.textInput.value = '';
                    this.textInput.focus();
                })
                .catch((err) => alert(err.message || 'Не удалось отправить сообщение'))
                .finally(() => {
                    this.sendBtn.disabled = false;
                    this.sendBtn.textContent = 'Отправить';
                });
        });
    }

    _setUsername(name) {
        this.userManager.save(name);
        this.badge.textContent = this.userManager.currentUser;
        this.overlay.classList.add('hidden');
        this._markExistingMessages();
        this.textInput.focus();
    }

    _showRenameOverlay() {
        let existing = document.getElementById('rename-overlay');
        if (existing) existing.remove();

        let renameOverlay = document.createElement('div');
        renameOverlay.id = 'rename-overlay';
        renameOverlay.className = 'name-overlay';
        renameOverlay.innerHTML =
            '<div class="name-card">' +
            '<h2>✏️ Сменить имя</h2>' +
            '<p>Введите новое имя для чата</p>' +
            '<input type="text" id="rename-input" placeholder="Новое имя..." maxlength="30" value="' + this._escapeHtml(this.userManager.currentUser) + '">' +
            '<div class="rename-buttons">' +
            '<button type="button" id="rename-cancel" class="btn-cancel">Отмена</button>' +
            '<button type="button" id="rename-submit">Сохранить</button>' +
            '</div>' +
            '</div>';
        document.body.appendChild(renameOverlay);

        let renameInput = document.getElementById('rename-input');
        renameInput.focus();
        renameInput.select();

        const applyRename = () => {
            let name = renameInput.value.trim();
            if (name) {
                this._setUsername(name);
                renameOverlay.remove();
            }
        };

        document.getElementById('rename-submit').addEventListener('click', applyRename);
        document.getElementById('rename-cancel').addEventListener('click', () => renameOverlay.remove());

        renameInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                applyRename();
            }
            if (e.key === 'Escape') renameOverlay.remove();
        });

        renameOverlay.addEventListener('click', (e) => {
            if (e.target === renameOverlay) renameOverlay.remove();
        });
    }

    _markExistingMessages() {
        this.messagesContainer.querySelectorAll('.message[data-author]').forEach((el) => {
            let author = el.getAttribute('data-author');
            el.classList.remove('me', 'other');
            el.classList.add(author === this.userManager.currentUser ? 'me' : 'other');
        });
    }

    _appendMessage(msg) {
        let empty = document.getElementById('empty-state');
        if (empty) empty.remove();

        let isMe = msg.author === this.userManager.currentUser;
        let div = document.createElement('div');
        div.className = 'message ' + (isMe ? 'me' : 'other');
        div.setAttribute('data-author', msg.author);
        div.innerHTML =
            '<div class="message-author">' + this._escapeHtml(msg.author) + '</div>' +
            '<div class="message-text">' + this._escapeHtml(msg.text).replace(/\n/g, '<br>') + '</div>' +
            '<div class="message-time">' + this._escapeHtml(this._formatCreatedAt(msg.created_at)) + '</div>';

        this.messagesContainer.appendChild(div);
        this.messagesContainer.scrollTop = this.messagesContainer.scrollHeight;
    }

    _normalizeMessage(raw) {
        let createdAt = Number(raw.created_at ?? raw.createdAt ?? 0);
        return {
            author: String(raw.author ?? ''),
            text: String(raw.text ?? ''),
            created_at: Number.isFinite(createdAt) ? createdAt : 0,
        };
    }

    _formatCreatedAt(createdAt) {
        if (!createdAt) return '';
        return new Date(createdAt * 1000).toISOString().slice(0, 19).replace('T', ' ');
    }

    _renderEmptyState() {
        if (document.getElementById('empty-state')) return;

        let empty = document.createElement('div');
        empty.className = 'empty-state';
        empty.id = 'empty-state';
        empty.textContent = 'Сообщений пока нет. Напишите первое!';

        this.messagesContainer.appendChild(empty);
    }

    _escapeHtml(str) {
        let d = document.createElement('div');
        d.appendChild(document.createTextNode(str));
        return d.innerHTML;
    }
}

document.addEventListener('DOMContentLoaded', () => new ChatApp());
