{{-- resources/views/chat/room.blade.php --}}
@extends('layouts.chat')

@section('title', 'Чат - ' . ($isAuthor ? 'Мои кандидаты' : 'Мои работодатели'))
@section('header', 'Чат - ' . ($isAuthor ? 'Мои кандидаты' : 'Мои работодатели'))

@section('styles')
    <link href="{{ asset('css/chat.css') }}" rel="stylesheet">
@endsection

@section('content')
    <!-- Глобальный чат -->
    <div id="globalChatModal" class="global-chat-modal">
        <!-- Заголовок чата -->
        <div class="chat-header">
            <div class="chat-header-info">
                <div class="chat-title" id="chatTitle">💬 Чат</div>
                <div class="chat-subtitle" id="chatSubtitle">{{ $isAuthor ? 'Мои кандидаты' : 'Мои работодатели' }}</div>
            </div>
            <button class="close-chat" title="Закрыть" onclick="window.close()">&times;</button>
        </div>

        <!-- Основное тело чата -->
        <div class="chat-body">
            <!-- Левая панель - список чатов -->
            <div class="chat-sidebar" id="chatSidebar">
                <!-- Табы - УБИРАЕМ АРХИВ -->
                <div class="chat-tabs" id="chatTabs">
                    @if($isAuthor)
                        <button class="chat-tab active" data-tab="candidates">Кандидаты</button>
                    @else
                        <button class="chat-tab active" data-tab="employers">Работодатели</button>
                    @endif
                </div>

                <!-- Списки чатов - УБИРАЕМ АРХИВ -->
                <div class="chat-lists">
                    @if($isAuthor)
                        <div class="chat-list active" id="candidatesList">
                            <div class="chat-list-empty">Загрузка кандидатов...</div>
                        </div>
                    @else
                        <div class="chat-list active" id="employersList">
                            <div class="chat-list-empty">Загрузка работодателей...</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Правая панель - сообщения -->
            <div class="chat-main" id="chatMain">
                <!-- Кнопка назад -->
                <div class="chat-back-button" onclick="globalChat.showConversationList()">
                    <i>←</i> Назад к списку
                </div>

                <div class="chat-messages-container">
                    <div class="chat-messages" id="chatMessages">
                        <div class="chat-welcome">
                            <div class="welcome-icon">💬</div>
                            <h3>Добро пожаловать в чат</h3>
                            <p>Выберите контакт из списка слева для начала общения</p>
                        </div>
                    </div>
                </div>

                <!-- Блок ввода сообщения -->
                <div class="chat-input-container" id="chatInputContainer">
                    <div class="chat-input">
                        <input type="text" id="chatInput" placeholder="Введите сообщение..." disabled>
                        <button id="sendChatBtn" title="Отправить сообщение" disabled>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        class GlobalChat {
            constructor() {
                this.currentJobId = null;
                this.userEmail = '{{ session("user_email") }}';
                this.userName = '{{ $userName ?? "User" }}';
                this.isAuthor = {{ $isAuthor ? 'true' : 'false' }};
                this.authorToken = '{{ session("author_token") }}';
                this.candidateToken = '{{ session("candidate_token") }}';
                this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                this.unreadCount = 0;
                this.currentConversation = null;
                this.currentChatRoom = null;

                this.init();
            }

            init() {
                console.log('✅ GlobalChat initialized - Room layout');
                console.log('User:', this.userEmail, '(', this.isAuthor ? 'AUTHOR' : 'CANDIDATE', ')');

                this.setupEventListeners();
                this.loadConversations();

                // Всегда начинаем со списка контактов
                this.showConversationList();
            }

            setupEventListeners() {
                const sendChatBtn = document.getElementById('sendChatBtn');
                const chatInput = document.getElementById('chatInput');

                if (sendChatBtn) sendChatBtn.addEventListener('click', () => this.sendMessage());
                if (chatInput) chatInput.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') this.sendMessage();
                });

                // Табы - УБИРАЕМ ПЕРЕКЛЮЧЕНИЕ ТАБОВ
            }

            async loadConversations() {
                try {
                    // Загружаем реальные данные
                    await this.loadRealConversations();
                } catch (error) {
                    console.error('Error loading conversations:', error);
                    // Fallback на демо-данные
                    this.loadDemoConversations();
                }
            }

            async loadRealConversations() {
                try {
                    console.log('Loading real conversations for:', this.userEmail);

                    const response = await fetch('/chat/conversations?user_email=' +
                        encodeURIComponent(this.userEmail), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    console.log('Response status:', response.status);

                    if (response.ok) {
                        const data = await response.json();
                        console.log('API response:', data);

                        if (data.success) {
                            this.renderRealConversations(data.conversations);
                            return;
                        } else {
                            console.error('API returned error:', data.message);
                            throw new Error(data.message || 'API error');
                        }
                    } else {
                        console.error('HTTP error:', response.status, response.statusText);
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }

                } catch (error) {
                    console.error('loadRealConversations error:', error);
                    throw error;
                }
            }

            renderRealConversations(conversations) {
                // Рендерим реальные данные
                console.log('Rendering real conversations:', conversations);

                if (this.isAuthor) {
                    this.renderChatList('candidates', conversations.candidates || []);
                } else {
                    this.renderChatList('employers', conversations.employers || []);
                }
            }

            loadDemoConversations() {
                console.log('Loading demo conversations');

                if (this.isAuthor) {
                    // Демо-данные для автора
                    const candidates = [
                        {
                            id: 'chat_1',
                            chatRoomId: 1,
                            partnerName: 'Иван Петров',
                            jobTitle: 'Senior Developer',
                            lastMessage: 'Здравствуйте! Меня заинтересовала ваша вакансия...',
                            time: '12:30',
                            unread: 2,
                            online: true
                        },
                        {
                            id: 'chat_2',
                            chatRoomId: 2,
                            partnerName: 'Мария Сидорова',
                            jobTitle: 'Frontend Developer',
                            lastMessage: 'Спасибо за ответ! Когда можно пройти собеседование?',
                            time: '11:15',
                            unread: 0,
                            online: false
                        }
                    ];

                    this.renderChatList('candidates', candidates);
                } else {
                    // Демо-данные для кандидата
                    const employers = [
                        {
                            id: 'chat_3',
                            chatRoomId: 3,
                            partnerName: 'Tech Company s.r.o.',
                            jobTitle: 'Senior Developer',
                            lastMessage: 'Мы рассмотрели ваше резюме. Можете ли вы...',
                            time: '09:30',
                            unread: 1,
                            online: true
                        }
                    ];

                    this.renderChatList('employers', employers);
                }
            }

            renderChatList(listId, chats) {
                const listElement = document.getElementById(`${listId}List`);
                if (!listElement) return;

                if (chats.length === 0) {
                    listElement.innerHTML = '<div class="chat-list-empty">Нет активных чатов</div>';
                    return;
                }

                // УБИРАЕМ ЗАГОЛОВОК ТАБА - теперь он вверху
                listElement.innerHTML = '';

                // Добавляем чаты
                chats.forEach(chat => {
                    const chatElement = document.createElement('div');
                    chatElement.className = `chat-item ${chat.unread > 0 ? 'has-unread' : ''}`;
                    chatElement.innerHTML = `
                    <div class="chat-item-header">
                        <div>
                            <div class="chat-item-name">${chat.partnerName}</div>
                            <div class="chat-item-job">${chat.jobTitle}</div>
                        </div>
                        <div class="chat-item-meta">
                            <div class="chat-item-time">${chat.time}</div>
                            <div class="chat-item-status ${chat.online ? 'online' : 'offline'}">
                                ${chat.online ? '● Онлайн' : '○ Офлайн'}
                            </div>
                        </div>
                    </div>
                    <div class="chat-item-preview">${chat.lastMessage}</div>
                    ${chat.unread > 0 ? `<div class="chat-item-unread">${chat.unread}</div>` : ''}
                `;

                    chatElement.addEventListener('click', () => {
                        this.showConversation(chat.chatRoomId, chat.partnerName, chat.jobTitle);
                    });

                    listElement.appendChild(chatElement);
                });
            }

            // Показать список конверзаций
            showConversationList() {
                const chatBody = document.querySelector('.chat-body');
                const chatTitle = document.getElementById('chatTitle');
                const chatSubtitle = document.getElementById('chatSubtitle');

                if (chatBody) {
                    chatBody.classList.remove('conversation-active');
                }

                if (chatTitle) chatTitle.textContent = '💬 Чат';
                if (chatSubtitle) chatSubtitle.textContent = this.isAuthor ? 'Мои кандидаты' : 'Мои работодатели';

                // Деактивируем поле ввода
                document.getElementById('chatInput').disabled = true;
                document.getElementById('sendChatBtn').disabled = true;

                this.currentConversation = null;
                this.currentChatRoom = null;
            }

            // Показать конкретный чат
            showConversation(chatRoomId, partnerName, jobTitle) {
                const chatBody = document.querySelector('.chat-body');
                const chatTitle = document.getElementById('chatTitle');
                const chatSubtitle = document.getElementById('chatSubtitle');

                if (chatBody) {
                    chatBody.classList.add('conversation-active');
                }

                if (chatTitle) chatTitle.textContent = `💬 ${partnerName}`;
                if (chatSubtitle) chatSubtitle.textContent = jobTitle;

                // Активируем поле ввода
                document.getElementById('chatInput').disabled = false;
                document.getElementById('sendChatBtn').disabled = false;

                this.currentConversation = `${chatRoomId}_${partnerName}`;
                this.currentChatRoom = chatRoomId;

                this.loadChatHistory(chatRoomId);
            }

            async sendMessage() {
                const input = document.getElementById('chatInput');
                const message = input?.value.trim();

                if (!message || !this.currentChatRoom) return;

                // Optimistic update
                this.addMessageToChat('outgoing', message, 'Вы');
                input.value = '';

                try {
                    // Отправка на сервер
                    const formData = new FormData();
                    formData.append('sender_email', this.userEmail);
                    formData.append('sender_name', this.userName);
                    formData.append('message', message);
                    formData.append('token', this.isAuthor ? this.authorToken : this.candidateToken);
                    formData.append('_token', this.csrfToken);

                    const response = await fetch(`/chat/api/${this.currentChatRoom}/send`, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (!data.success) {
                        throw new Error(data.message || 'Ошибка отправки');
                    }

                    console.log('Message sent successfully');

                } catch (error) {
                    console.error('Send message error:', error);
                    // Можно добавить уведомление об ошибке
                }
            }

            addMessageToChat(type, text, senderName = null) {
                const messagesDiv = document.getElementById('chatMessages');
                if (!messagesDiv) return;

                // Убираем welcome сообщение при первом сообщении
                const welcomeMessage = messagesDiv.querySelector('.chat-welcome');
                if (welcomeMessage) {
                    welcomeMessage.remove();
                }

                const messageClass = `message message-${type}`;
                const displayName = senderName || (type === 'outgoing' ? 'Вы' : 'Собеседник');

                const messageHtml = `
                <div class="${messageClass}">
                    <div class="message-sender"><b>${displayName}</b></div>
                    <div class="message-text">${this.escapeHtml(text)}</div>
                    <div class="message-time">${new Date().toLocaleTimeString()}</div>
                </div>
            `;

                messagesDiv.innerHTML += messageHtml;
                messagesDiv.scrollTop = messagesDiv.scrollHeight;
            }

            loadChatHistory(chatRoomId) {
                const messagesDiv = document.getElementById('chatMessages');
                if (!messagesDiv) return;

                // Очищаем и добавляем демо-сообщения
                messagesDiv.innerHTML = '';

                // Временные демо-сообщения
                const demoMessages = [
                    {
                        type: 'incoming',
                        text: 'Здравствуйте! Я заинтересовался вашей вакансией. Можете рассказать подробнее?',
                        sender: 'Собеседник',
                        time: '10:30'
                    },
                    {
                        type: 'outgoing',
                        text: 'Добрый день! Конечно, это позиция Senior Developer с полной занятостью.',
                        sender: 'Вы',
                        time: '10:32'
                    }
                ];

                demoMessages.forEach(msg => {
                    this.addMessageToChat(msg.type, msg.text, msg.sender);
                });
            }

            escapeHtml(unsafe) {
                return unsafe
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;")
                    .replace(/"/g, "&quot;")
                    .replace(/'/g, "&#039;");
            }
        }

        // Инициализация
        document.addEventListener('DOMContentLoaded', function() {
            window.globalChat = new GlobalChat();
        });
    </script>
@endsection
