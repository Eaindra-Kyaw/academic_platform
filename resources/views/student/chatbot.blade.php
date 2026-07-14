@extends('layouts.app')

@section('title', 'Academic Assistant')
@section('role', 'Student')
@section('page-title', 'Academic Assistant')
@section('welcome-text', 'Your AI-powered academic companion')

@section('sidebar')
    @include('layouts.partials.student-sidebar')
@endsection

@section('content')
    <style>
        :root {
            --primary: #0A2463;
            --primary-dark: #061840;
            --primary-light: #1E3A8A;
            --secondary: #C5A020;
            --accent: #D4A017;
            --bg-main: #EEF2F7;
            --white: #FFFFFF;
            --text-gray: #64748b;
            --text-dark: #1e293b;
            --shadow: 0 4px 20px rgba(10, 36, 99, 0.08);
            --shadow-hover: 0 8px 30px rgba(10, 36, 99, 0.15);
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #3b82f6;
            --radius: 12px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .chat-wrapper {
            max-width: 820px;
            margin: 0 auto;
        }

        .features-bar {
            background: var(--white);
            border-radius: var(--radius);
            border: 1px solid rgba(10, 36, 99, 0.06);
            padding: 10px 14px;
            margin-bottom: 12px;
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            justify-content: center;
            box-shadow: var(--shadow);
        }

        .features-bar .feature-btn {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 12px;
            border-radius: 18px;
            border: 1px solid rgba(10, 36, 99, 0.1);
            background: var(--bg-main);
            font-size: 11px;
            color: var(--text-dark);
            cursor: pointer;
            transition: var(--transition);
            font-weight: 500;
            white-space: nowrap;
        }

        .features-bar .feature-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: rgba(10, 36, 99, 0.05);
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }

        .features-bar .feature-btn .icon {
            font-size: 13px;
        }

        .features-bar .feature-btn .badge-new {
            font-size: 7px;
            background: var(--primary);
            color: var(--white);
            padding: 1px 5px;
            border-radius: 8px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-left: 2px;
        }

        .chat-box {
            background: var(--white);
            border-radius: var(--radius);
            border: 1px solid rgba(10, 36, 99, 0.06);
            overflow: hidden;
            height: 440px;
            display: flex;
            flex-direction: column;
            box-shadow: var(--shadow);
        }

        .chat-header {
            padding: 10px 16px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
        }

        .chat-header .left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .chat-header .left .avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.15);
            color: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            border: 2px solid rgba(255, 255, 255, 0.15);
        }

        .chat-header .left .info h6 {
            margin: 0;
            font-size: 14px;
            font-weight: 700;
            color: var(--white);
        }

        .chat-header .left .info small {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.7);
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .chat-header .left .info small .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--success);
            display: inline-block;
            animation: pulseDot 2s infinite;
        }

        @keyframes pulseDot {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.4;
            }
        }

        .chat-header .right {
            display: flex;
            gap: 4px;
        }

        .chat-header .right .btn-header {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.12);
            background: rgba(255, 255, 255, 0.06);
            color: rgba(255, 255, 255, 0.6);
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
        }

        .chat-header .right .btn-header:hover {
            background: rgba(255, 255, 255, 0.15);
            color: var(--white);
        }

        .chat-header .right .btn-header.clear:hover {
            background: rgba(239, 68, 68, 0.25);
            color: #fca5a5;
        }

        .chat-messages {
            flex: 1;
            padding: 12px 16px;
            overflow-y: auto;
            background: var(--bg-main);
            min-height: 0;
        }

        .chat-messages::-webkit-scrollbar {
            width: 3px;
        }

        .chat-messages::-webkit-scrollbar-thumb {
            background: var(--text-gray);
            border-radius: 4px;
        }

        .welcome {
            text-align: center;
            padding: 4px 10px;
        }

        .welcome .icon-wrap {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, rgba(10, 36, 99, 0.08), rgba(10, 36, 99, 0.05));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 6px;
            font-size: 22px;
            border: 2px solid rgba(10, 36, 99, 0.1);
        }

        .welcome h5 {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0 0 2px;
        }

        .welcome p {
            color: var(--text-gray);
            font-size: 12px;
            margin: 0 0 4px;
        }

        .message {
            display: flex;
            gap: 8px;
            margin-bottom: 10px;
            animation: slideUp 0.3s ease;
        }

        .message.bot {
            justify-content: flex-start;
        }

        .message.user {
            justify-content: flex-end;
        }

        .message .msg-avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            flex-shrink: 0;
        }

        .message.bot .msg-avatar {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: var(--white);
        }

        .message.user .msg-avatar {
            background: #e5e7eb;
            color: var(--text-dark);
        }

        .message .bubble {
            max-width: 78%;
            padding: 8px 14px;
            border-radius: 12px;
            font-size: 13px;
            line-height: 1.5;
            position: relative;
            word-wrap: break-word;
        }

        .message.bot .bubble {
            background: var(--white);
            color: var(--text-dark);
            border-bottom-left-radius: 3px;
            box-shadow: 0 1px 8px rgba(0, 0, 0, 0.04);
        }

        .message.bot .bubble::before {
            content: '';
            position: absolute;
            left: -7px;
            top: 10px;
            border: 7px solid transparent;
            border-right-color: var(--white);
        }

        .message.user .bubble {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: var(--white);
            border-bottom-right-radius: 3px;
        }

        .message.user .bubble::after {
            content: '';
            position: absolute;
            right: -7px;
            top: 10px;
            border: 7px solid transparent;
            border-left-color: var(--primary);
        }

        .message .bubble .time {
            font-size: 9px;
            opacity: 0.4;
            margin-top: 3px;
            display: block;
        }

        .message.user .bubble .time {
            text-align: right;
            color: rgba(255, 255, 255, 0.6);
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .typing {
            display: none;
            padding: 2px 0 6px 14px;
            gap: 8px;
            align-items: center;
        }

        .typing.show {
            display: flex;
        }

        .typing .dots {
            display: flex;
            gap: 3px;
            align-items: center;
        }

        .typing .dots span {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #9ca3af;
            animation: typingBounce 1.4s infinite;
        }

        .typing .dots span:nth-child(2) {
            animation-delay: 0.2s;
        }

        .typing .dots span:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes typingBounce {

            0%,
            60%,
            100% {
                transform: translateY(0);
                opacity: 0.3;
            }

            30% {
                transform: translateY(-5px);
                opacity: 1;
            }
        }

        .chat-input {
            padding: 10px 16px;
            border-top: 1px solid rgba(10, 36, 99, 0.06);
            display: flex;
            gap: 8px;
            background: var(--white);
            flex-shrink: 0;
        }

        .chat-input .input-wrap {
            flex: 1;
            position: relative;
        }

        .chat-input .input-wrap .input-icon {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 13px;
        }

        .chat-input .input-wrap input {
            width: 100%;
            padding: 8px 10px 8px 34px;
            border: 2px solid rgba(10, 36, 99, 0.12);
            border-radius: 10px;
            font-size: 13px;
            outline: none;
            transition: var(--transition);
            background: var(--bg-main);
            color: var(--text-dark);
        }

        .chat-input .input-wrap input:focus {
            border-color: var(--primary);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(10, 36, 99, 0.05);
        }

        .chat-input .input-wrap input::placeholder {
            color: #9ca3af;
        }

        .chat-input .btn-send {
            padding: 8px 16px;
            border-radius: 10px;
            border: none;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: var(--white);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 13px;
        }

        .chat-input .btn-send:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 16px rgba(10, 36, 99, 0.2);
        }

        .chat-input .btn-send:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        @media (max-width: 768px) {
            .features-bar {
                padding: 8px 10px;
                gap: 4px;
            }

            .features-bar .feature-btn {
                font-size: 10px;
                padding: 3px 8px;
            }

            .features-bar .feature-btn .badge-new {
                display: none;
            }

            .chat-box {
                height: 400px;
            }

            .chat-header .left .avatar {
                width: 28px;
                height: 28px;
                font-size: 12px;
            }

            .chat-header .left .info h6 {
                font-size: 13px;
            }

            .chat-messages {
                padding: 10px 12px;
            }

            .message .bubble {
                max-width: 88%;
                font-size: 12px;
                padding: 6px 10px;
            }

            .welcome .icon-wrap {
                width: 36px;
                height: 36px;
                font-size: 18px;
            }

            .welcome h5 {
                font-size: 14px;
            }

            .chat-input {
                padding: 8px 12px;
            }

            .chat-input .btn-send {
                padding: 6px 12px;
                font-size: 12px;
            }
        }

        @media (max-width: 480px) {
            .features-bar {
                padding: 6px 8px;
                gap: 3px;
            }

            .features-bar .feature-btn {
                font-size: 9px;
                padding: 2px 6px;
            }

            .chat-box {
                height: 360px;
                border-radius: var(--radius);
            }

            .chat-messages {
                padding: 8px 10px;
            }

            .message .msg-avatar {
                width: 24px;
                height: 24px;
                font-size: 10px;
            }

            .message .bubble {
                font-size: 11px;
                padding: 5px 8px;
            }

            .chat-input .input-wrap input {
                padding: 6px 8px 6px 30px;
                font-size: 12px;
            }

            .chat-input .input-wrap .input-icon {
                left: 8px;
                font-size: 11px;
            }

            .chat-input .btn-send {
                padding: 6px 10px;
                font-size: 11px;
            }
        }
    </style>

    <div class="chat-wrapper">
        <div class="features-bar">
            <button class="feature-btn" onclick="quickAsk('What is my attendance?')">
                <span class="icon">📊</span> Attendance
            </button>
            <button class="feature-btn" onclick="quickAsk('Am I eligible for exam?')">
                <span class="icon">✅</span> Eligibility
            </button>
            <button class="feature-btn" onclick="quickAsk('What is my risk level?')">
                <span class="icon">⚠️</span> Risk
            </button>
            <button class="feature-btn" onclick="quickAsk('What should I do?')">
                <span class="icon">💡</span> Advice
            </button>
            <button class="feature-btn" onclick="quickAsk('What is my health score?')">
                <span class="icon">💚</span> Score
            </button>
            <button class="feature-btn" onclick="quickAsk('What courses am I enrolled in?')">
                <span class="icon">📚</span> Courses
            </button>
            <button class="feature-btn" onclick="quickAsk('What is my class rank?')">
                <span class="icon">🏆</span> Rank
            </button>
            <button class="feature-btn" onclick="quickAsk('Show my attendance trend')">
                <span class="icon">📈</span> Trend
            </button>
            <button class="feature-btn" onclick="quickAsk('Give me study tips')">
                <span class="icon">💡</span> Study Tips
            </button>
            <button class="feature-btn" onclick="quickAsk('Who is my lecturer?')">
                <span class="icon">👨‍🏫</span> Lecturer
            </button>
            <button class="feature-btn" onclick="quickAsk('How is my semester?')">
                <span class="icon">📚</span> Semester
            </button>
            <button class="feature-btn" onclick="quickAsk('Help')">
                <span class="icon">❓</span> Help
            </button>
        </div>

        <div class="chat-box">
            <div class="chat-header">
                <div class="left">
                    <div class="avatar"><i class="bi bi-robot"></i></div>
                    <div class="info">
                        <h6>Academic Assistant</h6>
                        <small><span class="dot"></span> Online</small>
                    </div>
                </div>
                <div class="right">
                    <button class="btn-header clear" onclick="clearChat()" title="Clear Chat">
                        <i class="bi bi-trash3"></i>
                    </button>
                    <button class="btn-header" onclick="window.location.reload()" title="Refresh">
                        <i class="bi bi-arrow-repeat"></i>
                    </button>
                </div>
            </div>

            <div class="chat-messages" id="chatMessages">
                <div class="welcome" id="welcomeMessage">
                    <div class="icon-wrap">🤖</div>
                    <h5>Hello! I'm your Academic Assistant</h5>
                    <p>Click a button below or type your question</p>
                </div>
            </div>

            <div class="typing" id="typingIndicator">
                <div class="dots">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
                <span style="font-size:12px; color:var(--text-gray);">Thinking...</span>
            </div>

            <div class="chat-input">
                <div class="input-wrap">
                    <span class="input-icon"><i class="bi bi-chat"></i></span>
                    <input type="text" id="chatInput" placeholder="Ask me anything..."
                        onkeypress="if(event.key==='Enter') sendMessage()">
                </div>
                <button class="btn-send" id="sendBtn" onclick="sendMessage()">
                    <i class="bi bi-send"></i> Send
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let messageCount = 0;

            function sendMessage() {
                const input = document.getElementById('chatInput');
                const message = input.value.trim();
                if (!message) return;

                document.getElementById('welcomeMessage').style.display = 'none';
                addMessage('user', message);
                input.value = '';
                input.focus();

                document.getElementById('sendBtn').disabled = true;
                document.getElementById('typingIndicator').classList.add('show');

                fetch('{{ route('student.chatbot.ask') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            query: message
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('typingIndicator').classList.remove('show');
                        document.getElementById('sendBtn').disabled = false;
                        if (data.success) {
                            addMessage('bot', data.response);
                        } else {
                            addMessage('bot', '⚠️ Sorry, I encountered an error. Please try again.');
                        }
                    })
                    .catch(() => {
                        document.getElementById('typingIndicator').classList.remove('show');
                        document.getElementById('sendBtn').disabled = false;
                        addMessage('bot', '⚠️ Something went wrong. Please try again.');
                    });
            }

            function addMessage(type, content) {
                const container = document.getElementById('chatMessages');
                const div = document.createElement('div');
                div.className = `message ${type}`;

                const avatar = document.createElement('div');
                avatar.className = 'msg-avatar';
                avatar.innerHTML = type === 'bot' ? '<i class="bi bi-robot"></i>' : '<i class="bi bi-person"></i>';

                const bubble = document.createElement('div');
                bubble.className = 'bubble';
                const now = new Date();
                const timeStr = String(now.getHours()).padStart(2, '0') + ':' +
                    String(now.getMinutes()).padStart(2, '0');
                bubble.innerHTML = content + `<span class="time">${timeStr}</span>`;

                div.appendChild(avatar);
                div.appendChild(bubble);
                container.appendChild(div);
                messageCount++;
                container.scrollTop = container.scrollHeight;
            }

            function quickAsk(question) {
                document.getElementById('chatInput').value = question;
                sendMessage();
            }

            function clearChat() {
                if (messageCount === 0) return;
                if (confirm('Clear all messages?')) {
                    const container = document.getElementById('chatMessages');
                    container.innerHTML = `
                        <div class="welcome" id="welcomeMessage">
                            <div class="icon-wrap">🤖</div>
                            <h5>Hello! I'm your Academic Assistant</h5>
                            <p>Click a button below or type your question</p>
                        </div>
                    `;
                    messageCount = 0;
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('chatInput').focus();
            });
        </script>
    @endpush
@endsection
