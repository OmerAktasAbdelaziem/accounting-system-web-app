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
        width: 430px;
        height: 700px;
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
        background: linear-gradient(135deg, #ffffff, #f4f6f8);
        border-bottom: 1px solid #e9ecef;
        color: #111;
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
        color: #111;
    }

    .floating-chat-subtitle {
        font-size: 11px;
        color: #6c757d;
        margin-top: 2px;
    }

    .floating-chat-header-actions {
        display: flex;
        gap: 6px;
    }

    .floating-chat-header-actions button {
        border: none;
        background: #eef1f4;
        color: #444;
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
        max-height: 280px;
        overflow-y: auto;
        background: #fff;
        padding-bottom: 4px;
    }

    .floating-chat-search-wrap {
        padding: 10px 12px 8px;
        border-bottom: 1px solid #f0f0f0;
        background: #fff;
    }

    .floating-chat-search {
        width: 100%;
        border: 1px solid #dfe3e8;
        border-radius: 999px;
        padding: 9px 13px;
        font-size: 13px;
        background: #f8f9fb;
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
        background: #e7f3ff;
    }

    .floating-chat-contact-left {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
    }

    .floating-chat-avatar {
        position: relative;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 800;
        color: #fff;
        flex: 0 0 auto;
        background: linear-gradient(135deg, #ff8c00, #ff6a00);
    }

    .floating-chat-avatar.employee { background: linear-gradient(135deg, #1f7aec, #39a0ff); }
    .floating-chat-avatar.support { background: linear-gradient(135deg, #2dbf6c, #18a058); }
    .floating-chat-avatar.merchant { background: linear-gradient(135deg, #ff8c00, #ff6a00); }

    .floating-chat-avatar::after {
        content: '';
        position: absolute;
        right: 1px;
        bottom: 1px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        border: 2px solid #fff;
        background: #adb5bd;
    }

    .floating-chat-avatar.is-online::after {
        background: #2ecc71;
    }

    .floating-chat-contact-main {
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .floating-chat-contact-name {
        font-weight: 600;
        font-size: 13px;
        color: #222;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .floating-chat-contact-meta {
        font-size: 11px;
        color: #666;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 220px;
    }

    .floating-chat-status-line {
        font-size: 11px;
        color: #6c757d;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .floating-chat-status-line .typing {
        color: #1f7aec;
        font-weight: 700;
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
        background: linear-gradient(180deg, #f8fafc, #f3f4f6);
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
        border-radius: 18px;
        padding: 8px 10px;
        font-size: 13px;
        line-height: 1.35;
        word-break: break-word;
    }

    .floating-chat-message.me {
        align-self: flex-end;
        background: linear-gradient(135deg, #1f7aec, #4f8dff);
        color: #fff;
        border-bottom-right-radius: 6px;
    }

    .floating-chat-message.them {
        align-self: flex-start;
        background: #ffffff;
        color: #222;
        border: 1px solid #e9e9e9;
        border-bottom-left-radius: 6px;
        box-shadow: 0 1px 1px rgba(0, 0, 0, 0.04);
    }

    .floating-chat-message-time {
        display: block;
        margin-top: 4px;
        font-size: 10px;
        opacity: 0.7;
    }

    .floating-chat-message-status {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        margin-top: 4px;
        font-size: 10px;
        opacity: 0.95;
        justify-content: flex-end;
    }

    .floating-chat-message-status i {
        font-size: 10px;
        line-height: 1;
    }

    .floating-chat-message.me .floating-chat-message-status {
        color: rgba(255, 255, 255, 0.9);
    }

    .floating-chat-message.them .floating-chat-message-status {
        color: #6c757d;
    }

    .floating-chat-typing-indicator {
        display: flex;
        gap: 4px;
        align-items: center;
        color: #6c757d;
        font-size: 12px;
        padding: 4px 2px 0;
    }

    .floating-chat-typing-dots {
        display: inline-flex;
        gap: 4px;
    }

    .floating-chat-typing-dots span {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #1f7aec;
        animation: floating-chat-bounce 1.2s infinite ease-in-out;
    }

    .floating-chat-typing-dots span:nth-child(2) { animation-delay: 0.15s; }
    .floating-chat-typing-dots span:nth-child(3) { animation-delay: 0.3s; }

    @keyframes floating-chat-bounce {
        0%, 80%, 100% { transform: translateY(0); opacity: 0.5; }
        40% { transform: translateY(-4px); opacity: 1; }
    }

    .floating-chat-input-wrap {
        border-top: 1px solid #ececec;
        background: #fff;
        padding: 12px;
        display: flex;
        gap: 8px;
    }

    .floating-chat-input {
        border: 1px solid #d9d9d9;
        border-radius: 999px;
        flex: 1;
        min-width: 0;
        padding: 10px 14px;
        font-size: 13px;
        background: #f8f9fb;
    }

    .floating-chat-send {
        border: none;
        border-radius: 999px;
        background: linear-gradient(135deg, #1f7aec, #4f8dff);
        color: #fff;
        padding: 0 16px;
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

        // Switch to contacts list (used when swiping back from a conversation)
        function goToContactsView() {
            selectedContact = null;
            conversationTitleEl.textContent = 'Live Chat';
            conversationSubtitleEl.textContent = 'Choose a contact to start';
            messagesEl.innerHTML = '<div class="floating-chat-empty">Choose a contact to start chatting.</div>';
            renderContacts();
        }

        // Simple swipe-to-back support for mobile: swipe right inside messages to return to contacts
        (function setupSwipeBack() {
            let touchStartX = 0;
            let touchStartY = 0;

            messagesEl.addEventListener('touchstart', (e) => {
                const t = e.touches && e.touches[0];
                if (!t) return;
                touchStartX = t.clientX;
                touchStartY = t.clientY;
            }, { passive: true });

            messagesEl.addEventListener('touchend', (e) => {
                const t = e.changedTouches && e.changedTouches[0];
                if (!t) return;
                const dx = t.clientX - touchStartX;
                const dy = t.clientY - touchStartY;
                // horizontal swipe, to the right, not much vertical movement
                if (dx > 60 && Math.abs(dy) < 40) {
                    // if a conversation is open, go back to contacts
                    if (selectedContact) {
                        goToContactsView();
                    }
                }
            }, { passive: true });
        })();

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
    data-typing-url="{{ route('chat.typing') }}"
    data-current-user-id="{{ (int) auth()->id() }}">
    <div class="floating-chat-window" id="floatingChatWindow">
        <div class="floating-chat-header">
            <div>
                <div class="floating-chat-title" id="floatingChatConversationTitle">Live Chat</div>
                <div class="floating-chat-subtitle" id="floatingChatConversationSubtitle">Choose a contact to start</div>
            </div>
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
            <div class="floating-chat-search-wrap">
                <input type="text" class="floating-chat-search" id="floatingChatSearch" placeholder="Search chats..." autocomplete="off">
            </div>
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
        const typingUrl = root.dataset.typingUrl;
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
        const searchEl = document.getElementById('floatingChatSearch');
        const notificationsEl = document.getElementById('floatingChatNotifications');
        const enableNotificationsBtn = document.getElementById('floatingChatEnableNotifications');
        const conversationTitleEl = document.getElementById('floatingChatConversationTitle');
        const conversationSubtitleEl = document.getElementById('floatingChatConversationSubtitle');

        let sections = [];
        let selectedContact = null;
        let searchTerm = '';
        let isOpen = false;
        let contactsTimer = null;
        let messagesTimer = null;
        let typingTimer = null;
        let typingStateTimer = null;
        const notifiedMessageIds = new Set();

        function formatTime(iso) {
            if (!iso) return '';
            const d = new Date(iso);
            if (Number.isNaN(d.getTime())) return '';
            return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        }

        function formatLastSeen(iso) {
            if (!iso) return 'Offline';
            const d = new Date(iso);
            if (Number.isNaN(d.getTime())) return 'Offline';
            const diffMs = Date.now() - d.getTime();
            const diffMin = Math.floor(diffMs / 60000);
            if (diffMin < 1) return 'Active now';
            if (diffMin < 60) return `Active ${diffMin}m ago`;
            const diffHour = Math.floor(diffMin / 60);
            if (diffHour < 24) return `Active ${diffHour}h ago`;
            return `Last seen ${d.toLocaleDateString()}`;
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
                let message = 'Request failed';
                let payload = null;
                try {
                    payload = await response.json();
                } catch (err) {
                    // ignore JSON parse errors
                }

                if (payload) {
                    if (typeof payload === 'string') {
                        message = payload;
                    } else {
                        message = payload.message || payload.error || message;
                    }
                } else {
                    message = response.statusText || message;
                }

                throw new Error(`${response.status} ${message}`.trim());
            }

            return response.json();
        }

        // Render a single, flat users list (no section splits).
        function renderContacts() {
            const normalizedSearch = searchTerm.trim().toLowerCase();
            const allItems = sections.flatMap((s) => s.items || []);

            const filtered = allItems.filter((contact) => {
                if (!normalizedSearch) return true;
                return [contact.name, contact.meta, contact.user_type]
                    .filter(Boolean)
                    .some((value) => String(value).toLowerCase().includes(normalizedSearch));
            });

            if (!filtered.length) {
                contactsEl.innerHTML = '<div class="p-3 text-muted" style="font-size:12px;">No contacts available.</div>';
                return;
            }

            contactsEl.innerHTML = filtered.map((contact) => {
                const isActive = selectedContact && selectedContact.kind === contact.kind && String(selectedContact.id) === String(contact.id);
                const avatarText = String(contact.name || 'U').trim().slice(0, 2).toUpperCase();
                const avatarClass = contact.kind === 'support' ? 'support' : (contact.kind === 'user' && contact.meta === 'Merchant' ? 'merchant' : 'employee');
                const statusText = contact.is_typing ? 'Typing...' : (contact.is_online ? 'Online now' : formatLastSeen(contact.last_seen_at));
                return `
                    <button type="button" class="floating-chat-contact ${isActive ? 'is-active' : ''}" data-contact-kind="${contact.kind}" data-contact-id="${contact.id}">
                        <div class="floating-chat-contact-left">
                            <div class="floating-chat-avatar ${avatarClass} ${contact.is_online ? 'is-online' : ''}">${escapeHtml(avatarText)}</div>
                            <div class="floating-chat-contact-main">
                                <div class="floating-chat-contact-name">${escapeHtml(contact.name || 'User')}</div>
                                <div class="floating-chat-contact-meta">${escapeHtml(contact.last_message?.message ? contact.last_message.message : (contact.meta || contact.user_type || ''))}</div>
                                <div class="floating-chat-status-line">
                                    ${contact.is_typing ? '<span class="typing">Typing...</span>' : `<span>${escapeHtml(statusText)}</span>`}
                                </div>
                            </div>
                        </div>
                        ${contact.unread_count > 0 ? `<span class="floating-chat-contact-badge">${contact.unread_count > 99 ? '99+' : contact.unread_count}</span>` : ''}
                    </button>
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

        function renderMessageTicks(message, isMe) {
            if (!isMe) return '';

            if (message.read_at) {
                return '<span class="floating-chat-message-status"><i class="bi bi-check2-all"></i> Read</span>';
            }

            if (message.delivered_at) {
                return '<span class="floating-chat-message-status"><i class="bi bi-check2-all"></i> Delivered</span>';
            }

            return '<span class="floating-chat-message-status"><i class="bi bi-check2"></i> Sent</span>';
        }

        function conversationToken(contact) {
            if (!contact) return '';
            if (contact.kind === 'support') return 'support';
            if (contact.kind === 'employee') return `employee:${contact.id}`;
            return String(contact.id);
        }

        async function sendTypingState(isTyping) {
            if (!selectedContact) return;

            try {
                await fetchJson(typingUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        contact_id: conversationToken(selectedContact),
                        typing: !!isTyping,
                    }),
                });
            } catch (error) {
                // Ignore typing errors.
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

                if (selectedContact) {
                    const latest = sections.flatMap((section) => section.items || []).find((item) => String(item.id) === String(selectedContact.id) && item.kind === selectedContact.kind);
                    if (latest) {
                        selectedContact = latest;
                    }
                }

                if (selectedContact) {
                    const latest = sections
                        .flatMap((section) => section.items || [])
                        .find((item) => String(item.id) === String(selectedContact.id) && item.kind === selectedContact.kind);

                    if (latest) {
                        selectedContact = latest;
                    }
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

            const url = messagesUrlTemplate.replace('__CONTACT__', encodeURIComponent(conversationToken(selectedContact)));
            conversationTitleEl.textContent = selectedContact.kind === 'support' ? 'Support System' : (selectedContact.name || 'Live Chat');
            conversationSubtitleEl.textContent = selectedContact.kind === 'support'
                ? (selectedContact.is_typing ? 'Support is typing...' : (selectedContact.is_online ? 'Support is online' : formatLastSeen(selectedContact.last_seen_at)))
                : (selectedContact.is_typing ? 'Typing...' : (selectedContact.is_online ? 'Online now' : formatLastSeen(selectedContact.last_seen_at)));

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
                                ${renderMessageTicks(message, me)}
                            </div>
                        `;
                    }).join('');
                }

                if (selectedContact?.is_typing) {
                    messagesEl.insertAdjacentHTML('beforeend', `
                        <div class="floating-chat-typing-indicator">
                            <span>${escapeHtml(selectedContact.kind === 'support' ? 'Support is typing' : (selectedContact.name || 'Typing'))}</span>
                            <span class="floating-chat-typing-dots"><span></span><span></span><span></span></span>
                        </div>
                    `);
                }

                messagesEl.scrollTop = messagesEl.scrollHeight;
                await fetchJson(markReadUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ contact_id: conversationToken(selectedContact) }),
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
                        recipient_type: selectedContact.kind === 'support' ? 'support' : (selectedContact.kind === 'employee' ? 'employee' : 'user'),
                        message,
                    }),
                });
                await Promise.all([loadMessages(), loadContacts()]);
                await sendTypingState(false);
            } catch (error) {
                showInAppNotification('Message not sent', error?.message || 'Please try again.');
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

            const matchedContact = sections
                .flatMap((section) => section.items || [])
                .find((item) => String(item.id) === String(button.dataset.contactId) && item.kind === button.dataset.contactKind);

            selectedContact = matchedContact ? {
                ...matchedContact,
                id: button.dataset.contactId,
                kind: button.dataset.contactKind,
            } : {
                id: button.dataset.contactId,
                kind: button.dataset.contactKind,
                name: button.querySelector('.floating-chat-contact-name')?.textContent || 'Chat',
            };

            renderContacts();

            // If employee is not linked to a chat user, show an informative placeholder instead of attempting to load messages
            if (selectedContact && selectedContact.kind === 'employee' && !selectedContact.recipient_user_id) {
                conversationTitleEl.textContent = selectedContact.name || 'Employee';
                conversationSubtitleEl.textContent = 'Employee has no chat account';
                messagesEl.innerHTML = `<div class="floating-chat-empty">This employee does not have a chat account.</div>`;
                await loadContacts();
                return;
            }

            await Promise.all([loadMessages(), loadContacts()]);
        });

        searchEl.addEventListener('input', () => {
            searchTerm = searchEl.value || '';
            renderContacts();
        });

        inputEl.addEventListener('input', () => {
            if (typingTimer) {
                clearTimeout(typingTimer);
            }

            if (typingStateTimer) {
                clearTimeout(typingStateTimer);
            }

            sendTypingState(true);
            typingTimer = window.setTimeout(() => sendTypingState(false), 2200);
        });

        inputEl.addEventListener('blur', () => {
            sendTypingState(false);
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
