<style>
    .floating-chat-root {
        position: fixed;
        right: 22px;
        bottom: 22px;
        z-index: 3000;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .floating-chat-toggle {
        width: 60px;
        height: 60px;
        border: none;
        border-radius: 50%;
        color: #fff;
        background: linear-gradient(135deg, #ff8c00, #ff6a00);
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.25);
        font-size: 22px;
        position: relative;
    }

    .floating-chat-badge {
        position: absolute;
        top: -4px;
        right: -4px;
        min-width: 22px;
        height: 22px;
        border-radius: 11px;
        font-size: 12px;
        font-weight: 700;
        line-height: 22px;
        text-align: center;
        color: #fff;
        background: #e53935;
        padding: 0 6px;
        display: none;
    }

    .floating-chat-window {
        width: 360px;
        height: 520px;
        border-radius: 14px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 16px 50px rgba(0, 0, 0, 0.28);
        display: none;
        flex-direction: column;
        margin-bottom: 14px;
    }

    .floating-chat-window.is-open {
        display: flex;
    }

    .floating-chat-header {
        background: linear-gradient(135deg, #1a1a1a, #333333);
        color: #fff;
        padding: 12px 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }

    .floating-chat-title {
        font-weight: 700;
        font-size: 14px;
    }

    .floating-chat-header-actions {
        display: flex;
        gap: 6px;
    }

    .floating-chat-header-actions button {
        border: none;
        background: rgba(255, 255, 255, 0.18);
        color: #fff;
        border-radius: 7px;
        width: 30px;
        height: 30px;
    }

    .floating-chat-body {
        display: flex;
        flex-direction: column;
        min-height: 0;
        flex: 1;
    }

    .floating-chat-contacts {
        border-bottom: 1px solid #ececec;
        max-height: 150px;
        overflow-y: auto;
        background: #fafafa;
    }

    .floating-chat-contact {
        width: 100%;
        border: none;
        background: transparent;
        text-align: left;
        padding: 10px 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        cursor: pointer;
    }

    .floating-chat-contact:hover {
        background: #f2f2f2;
    }

    .floating-chat-contact.is-active {
        background: #ffe8cc;
    }

    .floating-chat-contact-name {
        font-weight: 600;
        font-size: 13px;
        color: #222;
    }

    .floating-chat-contact-meta {
        font-size: 11px;
        color: #666;
    }

    .floating-chat-contact-badge {
        min-width: 20px;
        height: 20px;
        border-radius: 10px;
        background: #e53935;
        color: #fff;
        font-size: 11px;
        line-height: 20px;
        text-align: center;
        padding: 0 5px;
        font-weight: 700;
    }

    .floating-chat-messages {
        flex: 1;
        min-height: 0;
        overflow-y: auto;
        background: #f7f7f7;
        padding: 12px;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .floating-chat-empty {
        text-align: center;
        color: #777;
        margin: auto 0;
        font-size: 13px;
    }

    .floating-chat-message {
        max-width: 82%;
        border-radius: 12px;
        padding: 8px 10px;
        font-size: 13px;
        line-height: 1.35;
        word-break: break-word;
    }

    .floating-chat-message.me {
        align-self: flex-end;
        background: #1f7aec;
        color: #fff;
        border-bottom-right-radius: 4px;
    }

    .floating-chat-message.them {
        align-self: flex-start;
        background: #ffffff;
        color: #222;
        border: 1px solid #e9e9e9;
        border-bottom-left-radius: 4px;
    }

    .floating-chat-message-time {
        display: block;
        margin-top: 4px;
        font-size: 10px;
        opacity: 0.7;
    }

    .floating-chat-input-wrap {
        border-top: 1px solid #ececec;
        background: #fff;
        padding: 10px;
        display: flex;
        gap: 8px;
    }

    .floating-chat-input {
        border: 1px solid #d9d9d9;
        border-radius: 9px;
        flex: 1;
        min-width: 0;
        padding: 8px 10px;
        font-size: 13px;
    }

    .floating-chat-send {
        border: none;
        border-radius: 9px;
        background: #1f7aec;
        color: #fff;
        padding: 0 14px;
        font-size: 15px;
    }

    .floating-chat-notification-list {
        position: fixed;
        right: 22px;
        bottom: 96px;
        width: 320px;
        max-height: 320px;
        overflow-y: auto;
        z-index: 2999;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .floating-chat-notification {
        background: #fff;
        border-left: 4px solid #ff8c00;
        border-radius: 10px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
        padding: 10px 12px;
    }

    .floating-chat-notification-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 4px;
    }

    .floating-chat-notification-title {
        font-size: 12px;
        font-weight: 700;
        color: #111;
    }

    .floating-chat-notification-close {
        border: none;
        background: transparent;
        color: #666;
        font-size: 16px;
        line-height: 1;
    }

    .floating-chat-notification-text {
        font-size: 12px;
        color: #333;
    }

    @media (max-width: 576px) {
        .floating-chat-root {
            right: 12px;
            bottom: 12px;
        }

        .floating-chat-window {
            width: calc(100vw - 24px);
            max-width: 360px;
            height: 72vh;
        }

        .floating-chat-notification-list {
            right: 12px;
            width: calc(100vw - 24px);
            max-width: 320px;
        }
    }
</style>

<div class="floating-chat-notification-list" id="floatingChatNotifications"></div>

<div class="floating-chat-root" id="floatingChatRoot"
    data-contacts-url="{{ route('chat.contacts') }}"
    data-messages-url-template="{{ url('/chat/messages/__CONTACT__') }}"
    data-send-url="{{ route('chat.send') }}"
    data-mark-read-url="{{ route('chat.markRead') }}"
    data-current-user-id="{{ (int) auth()->id() }}">
    <div class="floating-chat-window" id="floatingChatWindow">
        <div class="floating-chat-header">
            <div class="floating-chat-title" id="floatingChatConversationTitle">Live Chat</div>
            <div class="floating-chat-header-actions">
                <button type="button" id="floatingChatEnableNotifications" title="Enable desktop notifications">
                    <i class="bi bi-bell"></i>
                </button>
                <button type="button" id="floatingChatClose" title="Close chat">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        </div>
        <div class="floating-chat-body">
            <div class="floating-chat-contacts" id="floatingChatContacts"></div>
            <div class="floating-chat-messages" id="floatingChatMessages">
                <div class="floating-chat-empty">Choose a contact to start chatting.</div>
            </div>
            <form class="floating-chat-input-wrap" id="floatingChatForm">
                <input type="text" class="floating-chat-input" id="floatingChatInput" maxlength="2000" placeholder="Type your message..." autocomplete="off">
                <button type="submit" class="floating-chat-send" id="floatingChatSendBtn">
                    <i class="bi bi-send"></i>
                </button>
            </form>
        </div>
    </div>

    <button type="button" class="floating-chat-toggle" id="floatingChatToggle" aria-label="Open chat">
        <i class="bi bi-chat-dots-fill"></i>
        <span class="floating-chat-badge" id="floatingChatUnreadBadge">0</span>
    </button>
</div>

<script>
    (function () {
        const root = document.getElementById('floatingChatRoot');
        if (!root) return;

        const contactsUrl = root.dataset.contactsUrl;
        const messagesUrlTemplate = root.dataset.messagesUrlTemplate;
        const sendUrl = root.dataset.sendUrl;
        const markReadUrl = root.dataset.markReadUrl;
        const currentUserId = Number(root.dataset.currentUserId || 0);
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        const toggleBtn = document.getElementById('floatingChatToggle');
        const unreadBadge = document.getElementById('floatingChatUnreadBadge');
        const windowEl = document.getElementById('floatingChatWindow');
        const closeBtn = document.getElementById('floatingChatClose');
        const contactsEl = document.getElementById('floatingChatContacts');
        const messagesEl = document.getElementById('floatingChatMessages');
        const formEl = document.getElementById('floatingChatForm');
        const inputEl = document.getElementById('floatingChatInput');
        const notificationsEl = document.getElementById('floatingChatNotifications');
        const enableNotificationsBtn = document.getElementById('floatingChatEnableNotifications');
        const conversationTitleEl = document.getElementById('floatingChatConversationTitle');

        let sections = [];
        let selectedContact = null;
        let isOpen = false;
        let contactsTimer = null;
        let messagesTimer = null;
        const notifiedMessageIds = new Set();

        function formatTime(iso) {
            if (!iso) return '';
            const d = new Date(iso);
            if (Number.isNaN(d.getTime())) return '';
            return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text || '';
            return div.innerHTML;
        }

        function setUnreadBadge(total) {
            const count = Number(total || 0);
            if (count > 0) {
                unreadBadge.style.display = 'inline-block';
                unreadBadge.textContent = count > 99 ? '99+' : String(count);
            } else {
                unreadBadge.style.display = 'none';
            }
        }

        async function fetchJson(url, options = {}) {
            const response = await fetch(url, {
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    ...(options.headers || {}),
                },
                ...options,
            });

            if (!response.ok) {
                throw new Error('Request failed: ' + response.status);
            }

            return response.json();
        }

        function renderContacts() {
            const hasItems = sections.some((section) => Array.isArray(section.items) && section.items.length);
            if (!hasItems) {
                contactsEl.innerHTML = '<div class="p-3 text-muted" style="font-size:12px;">No contacts available.</div>';
                return;
            }

            contactsEl.innerHTML = sections.map((section) => {
                const itemsHtml = (section.items || []).map((contact) => {
                    const isActive = selectedContact && selectedContact.kind === contact.kind && String(selectedContact.id) === String(contact.id);
                    return `
                        <button type="button" class="floating-chat-contact ${isActive ? 'is-active' : ''}" data-contact-kind="${contact.kind}" data-contact-id="${contact.id}">
                            <div>
                                <div class="floating-chat-contact-name">${escapeHtml(contact.name || 'User')}</div>
                                <div class="floating-chat-contact-meta">${escapeHtml(contact.meta || contact.user_type || '')}</div>
                            </div>
                            ${contact.unread_count > 0 ? `<span class="floating-chat-contact-badge">${contact.unread_count > 99 ? '99+' : contact.unread_count}</span>` : ''}
                        </button>
                    `;
                }).join('');

                return `
                    <div class="px-2 pt-2 pb-1" style="font-size:11px; font-weight:700; color:#777; text-transform:uppercase; letter-spacing:0.08em;">${escapeHtml(section.label || '')}</div>
                    ${itemsHtml}
                `;
            }).join('');
        }

        function showInAppNotification(title, text) {
            const item = document.createElement('div');
            item.className = 'floating-chat-notification';
            item.innerHTML = `
                <div class="floating-chat-notification-head">
                    <div class="floating-chat-notification-title">${escapeHtml(title)}</div>
                    <button type="button" class="floating-chat-notification-close" aria-label="Close">&times;</button>
                </div>
                <div class="floating-chat-notification-text">${escapeHtml(text)}</div>
            `;
            const closeBtnEl = item.querySelector('.floating-chat-notification-close');
            closeBtnEl?.addEventListener('click', () => item.remove());
            notificationsEl.prepend(item);
        }

        function showDesktopNotification(title, body) {
            if (!('Notification' in window)) {
                return;
            }

            if (Notification.permission === 'granted') {
                try {
                    new Notification(title, {
                        body,
                        requireInteraction: true,
                    });
                } catch (error) {
                    // Ignore notification errors and keep in-app fallback.
                }
            }
        }

        function notifyNewMessage(contact, message) {
            if (!message || !message.id || message.sender_id === currentUserId) {
                return;
            }

            if (notifiedMessageIds.has(message.id)) {
                return;
            }

            notifiedMessageIds.add(message.id);

            const title = `New message from ${contact.name || 'User'}`;
            const text = (message.message || '').trim() || 'New message';

            showInAppNotification(title, text);
            showDesktopNotification(title, text);
        }

        async function loadContacts() {
            try {
                const data = await fetchJson(contactsUrl);
                sections = Array.isArray(data.sections) ? data.sections : [];
                setUnreadBadge(data.unread_total || 0);

                if (!selectedContact && sections.length && sections[0].items && sections[0].items.length) {
                    selectedContact = sections[0].items[0];
                }

                renderContacts();

                sections.forEach((section) => {
                    (section.items || []).forEach((contact) => {
                        if (Number(contact.unread_count || 0) > 0 && contact.last_message) {
                            notifyNewMessage(contact, contact.last_message);
                        }
                    });
                });
            } catch (error) {
                // Keep widget running even if one poll fails.
            }
        }

        async function loadMessages() {
            if (!selectedContact) {
                messagesEl.innerHTML = '<div class="floating-chat-empty">Choose a contact to start chatting.</div>';
                return;
            }

            const url = messagesUrlTemplate.replace('__CONTACT__', selectedContact.kind === 'support' ? 'support' : String(selectedContact.id));
            conversationTitleEl.textContent = selectedContact.kind === 'support' ? 'Support System' : (selectedContact.name || 'Live Chat');

            try {
                const data = await fetchJson(url);
                const messages = Array.isArray(data.messages) ? data.messages : [];

                if (!messages.length) {
                    messagesEl.innerHTML = '<div class="floating-chat-empty">No messages yet. Say hello.</div>';
                } else {
                    messagesEl.innerHTML = messages.map((message) => {
                        const me = Number(message.sender_id) === currentUserId;
                        return `
                            <div class="floating-chat-message ${me ? 'me' : 'them'}">
                                ${escapeHtml(message.message || '')}
                                <span class="floating-chat-message-time">${formatTime(message.created_at)}</span>
                            </div>
                        `;
                    }).join('');
                }

                messagesEl.scrollTop = messagesEl.scrollHeight;
                await fetchJson(markReadUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ contact_id: selectedContact.kind === 'support' ? 'support' : selectedContact.id }),
                });
            } catch (error) {
                // Ignore polling errors to avoid interrupting chat UI.
            }
        }

        async function sendMessage() {
            const text = (inputEl.value || '').trim();
            if (!text || !selectedContact) {
                return;
            }

            const message = text.slice(0, 2000);
            inputEl.value = '';
            inputEl.focus();

            try {
                await fetchJson(sendUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        recipient_id: selectedContact.kind === 'support' ? null : selectedContact.id,
                        recipient_type: selectedContact.kind === 'support' ? 'support' : 'user',
                        message,
                    }),
                });
                await Promise.all([loadMessages(), loadContacts()]);
            } catch (error) {
                showInAppNotification('Message not sent', 'Please try again.');
                inputEl.value = message;
            }
        }

        function startPolling() {
            stopPolling();
            contactsTimer = window.setInterval(loadContacts, 4000);
            messagesTimer = window.setInterval(() => {
                if (isOpen) {
                    loadMessages();
                }
            }, 2500);
        }

        function stopPolling() {
            if (contactsTimer) {
                clearInterval(contactsTimer);
                contactsTimer = null;
            }
            if (messagesTimer) {
                clearInterval(messagesTimer);
                messagesTimer = null;
            }
        }

        function openChat() {
            isOpen = true;
            windowEl.classList.add('is-open');
            Promise.all([loadContacts(), loadMessages()]).then(() => inputEl.focus()).catch(() => {});
        }

        function closeChat() {
            isOpen = false;
            windowEl.classList.remove('is-open');
        }

        toggleBtn.addEventListener('click', () => {
            if (isOpen) {
                closeChat();
                return;
            }
            openChat();
        });

        closeBtn.addEventListener('click', closeChat);

        enableNotificationsBtn.addEventListener('click', async () => {
            if (!('Notification' in window)) {
                showInAppNotification('Notifications unavailable', 'This browser does not support desktop notifications.');
                return;
            }

            if (Notification.permission === 'granted') {
                showInAppNotification('Notifications enabled', 'Desktop notifications are already enabled.');
                return;
            }

            const permission = await Notification.requestPermission();
            if (permission === 'granted') {
                showInAppNotification('Notifications enabled', 'You will receive desktop alerts for new messages.');
            } else {
                showInAppNotification('Notifications blocked', 'Enable notifications in browser settings to receive desktop alerts.');
            }
        });

        contactsEl.addEventListener('click', async (event) => {
            const button = event.target.closest('.floating-chat-contact[data-contact-id][data-contact-kind]');
            if (!button) {
                return;
            }

            selectedContact = {
                id: button.dataset.contactId,
                kind: button.dataset.contactKind,
                name: button.querySelector('.floating-chat-contact-name')?.textContent || 'Chat',
            };
            renderContacts();
            await Promise.all([loadMessages(), loadContacts()]);
        });

        formEl.addEventListener('submit', (event) => {
            event.preventDefault();
            sendMessage();
        });

        window.addEventListener('beforeunload', stopPolling);

        loadContacts();
        startPolling();
    })();
</script>
