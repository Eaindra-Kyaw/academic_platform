@extends('layouts.app')

@section('title', 'Uni Bot | Intelligent Assistant')
@section('role', 'Student')
@section('page-title', 'Uni Bot')
@section('welcome-text', 'Your Academic Companion')

@section('sidebar')
    @include('layouts.partials.student-sidebar')
@endsection

@section('content')
    <style>
        :root {
            --primary: #0A2463;
            --primary-dark: #061840;
            --primary-light: #1E3A8A;
            --accent: #F9A825;
            --bg-main: #EEF2F7;
            --white: #FFFFFF;
            --text-gray: #64748b;
            --text-dark: #1e293b;
            --shadow: 0 4px 20px rgba(10, 36, 99, 0.08);
            --shadow-hover: 0 8px 30px rgba(10, 36, 99, 0.15);
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --radius: 12px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .bot-wrapper {
            max-width: 900px;
            margin: 0 auto;
            padding: 0 15px;
        }

        /* ================================
                       QUICK ACTIONS (PILLS)
                       ================================ */
        .quick-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 20px;
            justify-content: center;
        }

        .quick-actions .pill {
            background: var(--white);
            padding: 8px 16px;
            border-radius: 30px;
            border: 1px solid rgba(10, 36, 99, 0.06);
            box-shadow: var(--shadow);
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-dark);
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .quick-actions .pill:hover {
            transform: translateY(-2px);
            border-color: var(--accent);
            box-shadow: var(--shadow-hover);
            background: #f8fafc;
        }

        .quick-actions .pill i {
            color: var(--primary);
            font-size: 0.85rem;
        }

        /* ================================
                       CHAT INTERFACE
                       ================================ */
        .chat-container {
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            border: 1px solid rgba(10, 36, 99, 0.04);
            height: 550px;
            display: flex;
            flex-direction: column;
        }

        .chat-header {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
        }

        .chat-header .bot-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .chat-header .bot-info .avatar {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: var(--white);
            border: 2px solid rgba(255, 255, 255, 0.1);
        }

        .chat-header .bot-info .details h5 {
            margin: 0;
            color: var(--white);
            font-size: 16px;
            font-weight: 700;
        }

        .chat-header .bot-info .details small {
            color: rgba(255, 255, 255, 0.7);
            font-size: 11px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .chat-header .bot-info .details small .online-dot {
            width: 6px;
            height: 6px;
            background: var(--success);
            border-radius: 50%;
            display: inline-block;
            animation: pulse-dot 2s infinite;
        }

        @keyframes pulse-dot {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.3;
            }
        }

        .chat-header .actions {
            display: flex;
            gap: 6px;
        }

        .chat-header .actions .icon-btn {
            background: rgba(255, 255, 255, 0.1);
            border: none;
            border-radius: 8px;
            color: rgba(255, 255, 255, 0.8);
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
            font-size: 14px;
        }

        .chat-header .actions .icon-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            color: var(--white);
        }

        /* ------------------ MESSAGES ------------------ */
        .chat-messages {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            background: #fafbfc;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .chat-messages::-webkit-scrollbar {
            width: 4px;
        }

        .chat-messages::-webkit-scrollbar-thumb {
            background: var(--text-gray);
            border-radius: 10px;
        }

        .msg-row {
            display: flex;
            gap: 10px;
            animation: fadeInUp 0.3s ease-out forwards;
            opacity: 0;
        }

        .msg-row.bot {
            justify-content: flex-start;
        }

        .msg-row.user {
            justify-content: flex-end;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .msg-row .bubble {
            max-width: 75%;
            padding: 12px 16px;
            border-radius: 14px;
            font-size: 0.9rem;
            line-height: 1.6;
            position: relative;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
        }

        .msg-row.user .bubble {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: var(--white);
            border-bottom-right-radius: 4px;
        }

        .msg-row.bot .bubble {
            background: var(--white);
            color: var(--text-dark);
            border: 1px solid rgba(10, 36, 99, 0.04);
            border-bottom-left-radius: 4px;
        }

        .msg-row .bubble .timestamp {
            font-size: 0.6rem;
            opacity: 0.5;
            margin-top: 6px;
            display: block;
        }

        .msg-row.user .bubble .timestamp {
            text-align: right;
            color: rgba(255, 255, 255, 0.7);
        }

        .msg-row .bot-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--primary);
            color: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
            margin-top: 4px;
        }

        .msg-row.user .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #e5e7eb;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
            margin-top: 4px;
        }

        /* ------------------ TYPING ------------------ */
        .typing-indicator {
            display: none;
            padding: 10px 0 0 10px;
            gap: 8px;
            align-items: center;
        }

        .typing-indicator.active {
            display: flex;
        }

        .typing-indicator .dots {
            display: flex;
            gap: 4px;
        }

        .typing-indicator .dots span {
            width: 6px;
            height: 6px;
            background: #a0aec0;
            border-radius: 50%;
            display: inline-block;
            animation: typingBounce 1.4s infinite ease-in-out both;
        }

        .typing-indicator .dots span:nth-child(1) {
            animation-delay: -0.32s;
        }

        .typing-indicator .dots span:nth-child(2) {
            animation-delay: -0.16s;
        }

        @keyframes typingBounce {

            0%,
            80%,
            100% {
                transform: scale(0);
            }

            40% {
                transform: scale(1);
            }
        }

        .typing-indicator span {
            font-size: 0.75rem;
            color: var(--text-gray);
        }

        /* ------------------ INPUT AREA ------------------ */
        .chat-input-area {
            padding: 12px 20px;
            border-top: 1px solid #e2e8f0;
            background: var(--white);
            display: flex;
            gap: 10px;
            align-items: center;
            flex-shrink: 0;
        }

        .chat-input-area .input-wrapper {
            flex: 1;
            position: relative;
            display: flex;
            align-items: center;
        }

        .chat-input-area .input-wrapper .input-icon {
            position: absolute;
            left: 14px;
            color: #9ca3af;
            font-size: 1rem;
            pointer-events: none;
        }

        .chat-input-area .input-wrapper input {
            width: 100%;
            padding: 12px 14px 12px 44px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 0.9rem;
            outline: none;
            transition: var(--transition);
            background: #f8fafc;
            font-family: 'Inter', sans-serif;
            color: var(--text-dark);
        }

        .chat-input-area .input-wrapper input:focus {
            border-color: var(--primary);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(10, 36, 99, 0.06);
        }

        .chat-input-area .input-wrapper input::placeholder {
            color: #9ca3af;
        }

        .chat-input-area .btn-send {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: var(--white);
            border: none;
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
            white-space: nowrap;
        }

        .chat-input-area .btn-send:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(10, 36, 99, 0.25);
        }

        .chat-input-area .btn-send:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        /* ------------------ CUSTOM CONFIRM MODAL ------------------ */
        .custom-confirm-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(4px);
        }

        .custom-confirm-overlay.show {
            display: flex;
        }

        .custom-confirm-box {
            background: var(--white);
            border-radius: var(--radius);
            padding: 2rem 2rem 1.5rem;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.3s ease;
            text-align: center;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px) scale(0.95);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .custom-confirm-box .icon-wrap {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: #fee2e2;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.75rem;
            font-size: 2rem;
            color: var(--danger);
        }

        .custom-confirm-box h4 {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0 0 0.3rem;
        }

        .custom-confirm-box p {
            font-size: 0.85rem;
            color: var(--text-gray);
            margin: 0 0 1.5rem;
            line-height: 1.5;
        }

        .custom-confirm-box .btn-group {
            display: flex;
            gap: 0.75rem;
            justify-content: center;
        }

        .custom-confirm-box .btn-cancel {
            padding: 0.5rem 1.5rem;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 500;
            border: 1px solid #e2e8f0;
            background: var(--white);
            color: var(--text-dark);
            cursor: pointer;
            transition: var(--transition);
        }

        .custom-confirm-box .btn-cancel:hover {
            background: #f1f5f9;
        }

        .custom-confirm-box .btn-confirm {
            padding: 0.5rem 1.5rem;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 500;
            border: none;
            background: var(--danger);
            color: var(--white);
            cursor: pointer;
            transition: var(--transition);
        }

        .custom-confirm-box .btn-confirm:hover {
            background: #dc2626;
            transform: translateY(-1px);
        }

        /* ------------------ RESPONSIVE ------------------ */
        @media (max-width: 768px) {
            .quick-actions {
                justify-content: flex-start;
                gap: 6px;
            }

            .quick-actions .pill {
                font-size: 0.65rem;
                padding: 6px 12px;
            }

            .chat-container {
                height: 450px;
            }

            .msg-row .bubble {
                max-width: 90%;
                font-size: 0.8rem;
                padding: 10px 12px;
            }

            .chat-input-area {
                padding: 10px 12px;
                gap: 8px;
                flex-wrap: wrap;
            }

            .chat-input-area .input-wrapper input {
                padding: 10px 12px 10px 38px;
                font-size: 0.8rem;
            }

            .chat-input-area .btn-send {
                padding: 10px 16px;
                font-size: 0.8rem;
            }
        }

        @media (max-width: 480px) {
            .chat-container {
                height: 400px;
                border-radius: 10px;
            }

            .chat-header {
                padding: 12px 15px;
            }

            .chat-header .bot-info .avatar {
                width: 34px;
                height: 34px;
                font-size: 16px;
            }

            .chat-header .bot-info .details h5 {
                font-size: 14px;
            }

            .chat-messages {
                padding: 12px;
            }

            .msg-row .bot-avatar,
            .msg-row .user-avatar {
                width: 28px;
                height: 28px;
                font-size: 12px;
            }

            .msg-row .bubble {
                font-size: 0.75rem;
                padding: 8px 10px;
                max-width: 95%;
            }

            .chat-input-area .input-wrapper input {
                font-size: 0.75rem;
                padding: 8px 10px 8px 34px;
            }

            .chat-input-area .btn-send {
                padding: 8px 14px;
                font-size: 0.75rem;
            }

            .chat-input-area .input-wrapper .input-icon {
                font-size: 0.85rem;
                left: 10px;
            }

            .custom-confirm-box {
                padding: 1.5rem 1.25rem;
            }
        }
    </style>

    <div class="bot-wrapper">
        {{-- Quick Actions --}}
        <div class="quick-actions">
            <button class="pill" onclick="quickAsk('What is my attendance?')">
                <i class="bi bi-bar-chart-fill"></i> Attendance
            </button>
            <button class="pill" onclick="quickAsk('Am I eligible for exam?')">
                <i class="bi bi-check-circle-fill"></i> Eligibility
            </button>
            <button class="pill" onclick="quickAsk('What is my risk level?')">
                <i class="bi bi-shield-exclamation"></i> Risk
            </button>
            <button class="pill" onclick="quickAsk('What should I do?')">
                <i class="bi bi-lightbulb-fill"></i> Advice
            </button>
            <button class="pill" onclick="quickAsk('Show my timetable')">
                <i class="bi bi-calendar3-fill"></i> Timetable
            </button>
            <button class="pill" onclick="quickAsk('Who is my lecturer?')">
                <i class="bi bi-person-badge-fill"></i> Teacher
            </button>
        </div>

        {{-- Chat Container --}}
        <div class="chat-container">
            <div class="chat-header">
                <div class="bot-info">
                    <div class="avatar"><i class="bi bi-robot"></i></div>
                    <div class="details">
                        <h5>Uni Bot</h5>
                        <small><span class="online-dot"></span> Online</small>
                    </div>
                </div>
                <div class="actions">
                    <button class="icon-btn" onclick="showClearConfirm()" title="Clear Chat">
                        <i class="bi bi-eraser"></i>
                    </button>
                </div>
            </div>

            <div class="chat-messages" id="chatMessages">
                <div class="msg-row bot">
                    <div class="bot-avatar"><i class="bi bi-robot"></i></div>
                    <div class="bubble">
                        <i class="bi bi-hand-wave" style="font-size:1.2rem;"></i> Hello! I am your <strong>MTU Academic
                            Assistant</strong>.<br>
                        I have direct access to your academic data. Ask me anything about your attendance, eligibility,
                        health score, or timetable.
                        <span class="timestamp">Just now</span>
                    </div>
                </div>
            </div>

            <div class="typing-indicator" id="typingIndicator">
                <div class="dots"><span></span><span></span><span></span></div>
                <span>Uni Bot is thinking...</span>
            </div>

            <div class="chat-input-area">
                <div class="input-wrapper">
                    <i class="bi bi-chat-dots input-icon"></i>
                    <input type="text" id="chatInput" placeholder="Type your question here..."
                        onkeydown="if(event.key === 'Enter') sendMessage()">
                </div>
                <button class="btn-send" onclick="sendMessage()" id="sendBtn">
                    <i class="bi bi-send"></i> Send
                </button>
            </div>
        </div>
    </div>

    {{-- Custom Confirm Modal --}}
    <div class="custom-confirm-overlay" id="clearConfirmModal">
        <div class="custom-confirm-box">
            <div class="icon-wrap"><i class="bi bi-trash3-fill"></i></div>
            <h4>Clear All Messages?</h4>
            <p>This action will permanently delete all conversation history. Are you sure you want to continue?</p>
            <div class="btn-group">
                <button class="btn-cancel" onclick="hideClearConfirm()">Cancel</button>
                <button class="btn-confirm" onclick="confirmClearChat()">Clear All</button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const csrfToken = "{{ csrf_token() }}";
            const chatMessages = document.getElementById('chatMessages');
            const chatInput = document.getElementById('chatInput');
            const typingIndicator = document.getElementById('typingIndicator');
            const sendBtn = document.getElementById('sendBtn');
            let isProcessing = false;

            function addMessage(type, text, timestamp = null) {
                const row = document.createElement('div');
                row.className = `msg-row ${type}`;

                if (type === 'bot') {
                    row.innerHTML = `
                        <div class="bot-avatar"><i class="bi bi-robot"></i></div>
                        <div class="bubble">${text}<span class="timestamp">${timestamp || new Date().toLocaleTimeString()}</span></div>
                    `;
                } else {
                    row.innerHTML = `
                        <div class="bubble">${text}<span class="timestamp">${timestamp || new Date().toLocaleTimeString()}</span></div>
                        <div class="user-avatar"><i class="bi bi-person-fill"></i></div>
                    `;
                }
                chatMessages.appendChild(row);
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }

            function sendMessage() {
                const message = chatInput.value.trim();
                if (!message || isProcessing) return;

                addMessage('user', message);
                chatInput.value = '';
                isProcessing = true;
                sendBtn.disabled = true;
                typingIndicator.classList.add('active');

                fetch("{{ route('student.chatbot.ask') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            query: message
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        typingIndicator.classList.remove('active');
                        isProcessing = false;
                        sendBtn.disabled = false;
                        if (data.success) {
                            addMessage('bot', data.response);
                        } else {
                            addMessage('bot',
                                '<i class="bi bi-exclamation-triangle-fill" style="color:var(--danger);"></i> Sorry, I encountered an internal error. Please try again.'
                                );
                        }
                    })
                    .catch(err => {
                        typingIndicator.classList.remove('active');
                        isProcessing = false;
                        sendBtn.disabled = false;
                        addMessage('bot',
                            '<i class="bi bi-wifi-off" style="color:var(--danger);"></i> Connection error. Please check your internet and try again.'
                            );
                        console.error(err);
                    });
            }

            function quickAsk(text) {
                chatInput.value = text;
                sendMessage();
            }

            // Custom Confirm Modal
            function showClearConfirm() {
                document.getElementById('clearConfirmModal').classList.add('show');
            }

            function hideClearConfirm() {
                document.getElementById('clearConfirmModal').classList.remove('show');
            }

            function confirmClearChat() {
                chatMessages.innerHTML = `
                    <div class="msg-row bot">
                        <div class="bot-avatar"><i class="bi bi-robot"></i></div>
                        <div class="bubble"><i class="bi bi-check-circle-fill" style="color:var(--success);"></i> Chat cleared. How can I help you today?<span class="timestamp">${new Date().toLocaleTimeString()}</span></div>
                    </div>
                `;
                hideClearConfirm();
            }

            // Close modal on overlay click
            document.getElementById('clearConfirmModal').addEventListener('click', function(e) {
                if (e.target === this) hideClearConfirm();
            });

            // Close modal on Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') hideClearConfirm();
            });

            document.addEventListener('DOMContentLoaded', () => chatInput.focus());
        </script>
    @endpush
@endsection
