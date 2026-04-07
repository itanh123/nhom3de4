@extends('layouts.app')

@section('title', 'Chat AI')

@push('styles')
<style>
    .chat-container {
        max-width: 800px;
        margin: 0 auto;
        height: calc(100vh - 200px);
        display: flex;
        flex-direction: column;
    }
    .chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 1rem;
        background: #f8fafc;
        border-radius: 1rem;
        margin-bottom: 1rem;
    }
    .message {
        max-width: 75%;
        margin-bottom: 1rem;
        animation: fadeIn 0.3s ease;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .message.user {
        margin-left: auto;
    }
    .message.ai {
        margin-right: auto;
    }
    .message-content {
        padding: 0.75rem 1rem;
        border-radius: 1rem;
        line-height: 1.6;
        white-space: pre-wrap;
        word-wrap: break-word;
    }
    .message.user .message-content {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        color: white;
        border-bottom-right-radius: 0.25rem;
    }
    .message.ai .message-content {
        background: white;
        color: #1e293b;
        border: 1px solid #e2e8f0;
        border-bottom-left-radius: 0.25rem;
    }
    .message-time {
        font-size: 0.7rem;
        color: #94a3b8;
        margin-top: 0.25rem;
        padding: 0 0.5rem;
    }
    .message.user .message-time {
        text-align: right;
    }
    .chat-input-area {
        display: flex;
        gap: 0.75rem;
        padding: 1rem;
        background: white;
        border-radius: 1rem;
        box-shadow: 0 -4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    .chat-input {
        flex: 1;
        padding: 0.875rem 1.25rem;
        border: 2px solid #e2e8f0;
        border-radius: 0.75rem;
        font-size: 1rem;
        transition: border-color 0.2s;
    }
    .chat-input:focus {
        outline: none;
        border-color: #4f46e5;
    }
    .chat-send-btn {
        padding: 0.875rem 1.5rem;
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        color: white;
        border: none;
        border-radius: 0.75rem;
        font-weight: 600;
        cursor: pointer;
        transition: transform 0.2s, opacity 0.2s;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .chat-send-btn:hover:not(:disabled) {
        transform: scale(1.02);
    }
    .chat-send-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    .typing-indicator {
        display: flex;
        gap: 4px;
        padding: 0.75rem 1rem;
    }
    .typing-indicator span {
        width: 8px;
        height: 8px;
        background: #94a3b8;
        border-radius: 50%;
        animation: typing 1.4s infinite;
    }
    .typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
    .typing-indicator span:nth-child(3) { animation-delay: 0.4s; }
    @keyframes typing {
        0%, 60%, 100% { transform: translateY(0); }
        30% { transform: translateY(-8px); }
    }
    .empty-state {
        text-align: center;
        padding: 3rem;
        color: #64748b;
    }
    .empty-state svg {
        width: 64px;
        height: 64px;
        margin-bottom: 1rem;
        opacity: 0.5;
    }
    .session-list {
        max-height: 300px;
        overflow-y: auto;
    }
    .session-item {
        padding: 0.75rem;
        border-radius: 0.5rem;
        cursor: pointer;
        transition: background 0.2s;
    }
    .session-item:hover {
        background: #f1f5f9;
    }
    .session-item.active {
        background: #e0e7ff;
    }
</style>
@endpush

@section('content')
<div class="chat-container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2><i class="bi bi-robot me-2"></i>Chat AI</h2>
        <div class="btn-group">
            <button class="btn btn-outline-secondary btn-sm" onclick="newChat()">
                <i class="bi bi-plus-circle me-1"></i>Cuộc trò chuyện mới
            </button>
            <button class="btn btn-outline-danger btn-sm" onclick="clearChat()">
                <i class="bi bi-trash me-1"></i>Xóa
            </button>
        </div>
    </div>

    <!-- Session List (Collapsible) -->
    <div class="card mb-3" id="sessionPanel" style="display: none;">
        <div class="card-header d-flex justify-content-between align-items-center py-2">
            <small class="fw-bold">Lịch sử cuộc trò chuyện</small>
            <button type="button" class="btn btn-sm p-0" onclick="toggleSessions()">
                <i class="bi bi-chevron-up" id="sessionToggleIcon"></i>
            </button>
        </div>
        <div class="card-body p-0 session-list" id="sessionList">
            <!-- Sessions will be loaded here -->
        </div>
    </div>

    <div class="chat-messages" id="chatMessages">
        <div class="empty-state" id="emptyState">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
            <p>Bắt đầu cuộc trò chuyện với AI</p>
            <small>Hỏi bất cứ điều gì về chủ đề học tập</small>
        </div>
    </div>

    <div class="chat-input-area">
        <input type="text" class="chat-input" id="messageInput" placeholder="Nhập tin nhắn..." autocomplete="off">
        <button class="chat-send-btn" id="sendBtn" onclick="sendMessage()">
            <span>Gửi</span>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/>
            </svg>
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const chatMessages = document.getElementById('chatMessages');
    const messageInput = document.getElementById('messageInput');
    const sendBtn = document.getElementById('sendBtn');
    const emptyState = document.getElementById('emptyState');
    const sessionPanel = document.getElementById('sessionPanel');
    const sessionList = document.getElementById('sessionList');

    let currentSessionId = null;
    let isLoading = false;

    messageInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    function toggleSessions() {
        const icon = document.getElementById('sessionToggleIcon');
        if (sessionPanel.style.display === 'none') {
            sessionPanel.style.display = 'block';
            icon.className = 'bi bi-chevron-up';
        } else {
            sessionPanel.style.display = 'none';
            icon.className = 'bi bi-chevron-down';
        }
    }

    function newChat() {
        currentSessionId = null;
        chatMessages.innerHTML = '';
        chatMessages.appendChild(createEmptyState());
        loadSessions();
    }

    function createEmptyState() {
        const div = document.createElement('div');
        div.className = 'empty-state';
        div.id = 'emptyState';
        div.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
            <p>Bắt đầu cuộc trò chuyện với AI</p>
            <small>Hỏi bất cứ điều gì về chủ đề học tập</small>
        `;
        return div;
    }

    function loadSessions() {
        fetch('{{ route("chat.sessions") }}')
            .then(res => res.json())
            .then(data => {
                if (data.success && data.sessions.length > 0) {
                    sessionPanel.style.display = 'block';
                    sessionList.innerHTML = data.sessions.map(s => `
                        <div class="session-item ${s.id == currentSessionId ? 'active' : ''}" onclick="loadSession(${s.id})">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="fw-bold">${escapeHtml(s.title || 'Cuộc trò chuyện')}</small>
                                    <br><small class="text-muted">${s.last_message_at || 'Chưa có tin nhắn'}</small>
                                </div>
                                ${s.is_starred ? '<i class="bi bi-star-fill text-warning"></i>' : ''}
                            </div>
                        </div>
                    `).join('');
                }
            })
            .catch(err => console.error('Load sessions error:', err));
    }

    function loadSession(sessionId) {
        currentSessionId = sessionId;
        chatMessages.innerHTML = '<div class="text-center py-5"><span class="spinner-border"></span></div>';

        fetch(`/chat/history/${sessionId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    chatMessages.innerHTML = '';
                    if (data.messages.length === 0) {
                        chatMessages.appendChild(createEmptyState());
                    } else {
                        data.messages.forEach(msg => {
                            const type = msg.sender_type === 'user' ? 'user' : 'ai';
                            addMessageToUI(msg.content, type, msg.created_at);
                        });
                    }
                    loadSessions();
                }
            })
            .catch(err => {
                chatMessages.innerHTML = '';
                chatMessages.appendChild(createEmptyState());
                console.error('Load session error:', err);
            });
    }

    function clearChat() {
        if (confirm('Xóa cuộc trò chuyện hiện tại?')) {
            if (currentSessionId) {
                fetch(`/chat/session/${currentSessionId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                }).then(() => {
                    newChat();
                    loadSessions();
                });
            } else {
                newChat();
            }
        }
    }

    function addMessage(content, type, time = null) {
        const empty = document.getElementById('emptyState');
        if (empty) empty.remove();

        const t = time || new Date().toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${type}`;
        messageDiv.innerHTML = `
            <div class="message-content">${escapeHtml(content)}</div>
            <div class="message-time">${t}</div>
        `;

        chatMessages.appendChild(messageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function addMessageToUI(content, type, createdAt) {
        const empty = document.getElementById('emptyState');
        if (empty) empty.remove();

        const time = new Date(createdAt).toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${type}`;
        messageDiv.innerHTML = `
            <div class="message-content">${escapeHtml(content)}</div>
            <div class="message-time">${time}</div>
        `;

        chatMessages.appendChild(messageDiv);
    }

    function showTyping() {
        const typingDiv = document.createElement('div');
        typingDiv.className = 'message ai';
        typingDiv.id = 'typingIndicator';
        typingDiv.innerHTML = `
            <div class="typing-indicator">
                <span></span><span></span><span></span>
            </div>
        `;
        chatMessages.appendChild(typingDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function hideTyping() {
        const typing = document.getElementById('typingIndicator');
        if (typing) typing.remove();
    }

    async function sendMessage() {
        const message = messageInput.value.trim();

        if (!message) {
            messageInput.focus();
            return;
        }

        if (isLoading) return;

        isLoading = true;
        sendBtn.disabled = true;
        messageInput.value = '';

        addMessage(message, 'user');
        showTyping();

        try {
            const body = { message };
            if (currentSessionId) {
                body.session_id = currentSessionId;
            }

            const response = await fetch('{{ route("chat.send") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(body)
            });

            const data = await response.json();
            hideTyping();

            if (data.success) {
                if (data.session_id && !currentSessionId) {
                    currentSessionId = data.session_id;
                    loadSessions();
                }
                addMessage(data.reply, 'ai');
            } else {
                addMessage('❌ ' + data.error, 'ai');
            }
        } catch (error) {
            hideTyping();
            addMessage('❌ Đã xảy ra lỗi: ' + error.message, 'ai');
            console.error('Chat error:', error);
        } finally {
            isLoading = false;
            sendBtn.disabled = false;
            messageInput.focus();
        }
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Load sessions on page load
    loadSessions();
</script>
@endpush
