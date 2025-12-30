<!-- Глобальный чат (скрыт по умололчанию) -->
<div id="globalChatModal" class="global-chat-modal" style="display: none;">
    <!-- Заголовок чата -->
    <div class="chat-header">
        <div class="chat-header-info">
            <!-- 🔥 ИСПРАВЛЕНО: Убрали дублирование ID -->
            <div class="chat-title-line">
                <div class="chat-title" id="chatTitle">💬 Chat</div>
                <div class="chat-subtitle" id="chatSubtitle">Contact list</div> <!-- 🔥 БЫЛО chat-title, СТАЛО chat-subtitle -->
            </div>
            <!-- 🔥 ПЕРЕМЕЩЕНО: Статус на отдельную строку, показывается только в чате -->
            <div class="chat-status-line" id="chatStatusLine" style="display: none;">
                <span class="status-indicator online"></span>
                <span class="status-text">online</span>
            </div>
        </div>
        <button class="close-chat" title="Close">&times;</button>
    </div>

    <!-- Основное тело чата -->
    <div class="chat-body">
        <!-- Левая панель - список чатов -->
        <div class="chat-sidebar" id="chatSidebar">
            <div class="chat-list" id="chatList">
                <div class="chat-list-header">
                    <h3>My conversations</h3>
                </div>
                <div class="chat-list-empty">
                    No active conversations
                </div>
            </div>
        </div>

        <!-- Правая панель - сообщения (появляется при выборе чата) -->
        <div class="chat-main" id="chatMain">
            <!-- Кнопка назад -->
            <div class="chat-back-button" onclick="globalChat.showConversationList()">
                <i>←</i> Back to conversations
            </div>

            <div class="chat-messages-container">
                <!-- Сообщения загружаются динамически -->
                <div class="chat-messages" id="chatMessages"></div>
                <button id="scrollToBottomBtn" class="scroll-to-bottom-btn">
                    ↓
                </button>
            </div>

            <!-- Блок ввода сообщения -->
            <div class="chat-input-container" id="chatInputContainer">
                <div class="chat-input">
                    <input type="text" id="chatInput" placeholder="Type a message...">
                    <button id="sendChatBtn" title="Send message">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Кнопка открытия чата -->
<div id="chatToggleBtn" class="chat-toggle-btn">
    💬
    <div class="chat-notification-badge" id="chatNotification" style="display: none;">3</div>
</div>

<div id="userData"
     data-user-email="{{ $customer->email ?? auth()->user()->email ?? '' }}"
     data-user-name="{{ $customer->name ?? auth()->user()->name ?? 'User' }}"
     style="display: none;">
</div>
<style>
    .scroll-to-bottom-btn {
        position: absolute;
        right: 200px;
        bottom: 70px;
        width: 48px;
        height: 48px;
        border-radius: 50%;
        border: none;
        background: #2a8cff;
        color: white;
        font-size: 22px;
        cursor: pointer;
        z-index: 999999 !important; /* ← самое важное */
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        pointer-events: none; /* ← чтобы не мешала пока скрыта */
        transition: opacity .2s ease;
    }

    .scroll-to-bottom-btn.show {
        opacity: 1;
        pointer-events: auto; /* ← чтобы снова кликабельная */
    }
    .chat-window {
        overflow: hidden;  /* УБИРАЕМ скролл здесь */
    }
    .chat-messages {
        height: 100%;
        overflow-y: auto;
        position: relative;
    }
</style>
<script>
    window.LANG_LAST_SEEN = "{{ __('messages.last_seen') }}";
</script>
<script>
    window.currentCustomer = @json($customer ?? null);

    class GlobalChat {
        constructor() {
            console.log('🔍 DEBUG: GlobalChat constructor started');

            // 🔥 ИСПРАВЛЕНО: Правильная инициализация переменных
            this.currentJobId = null;
            this.userEmail = this.getUserEmailFromPage();
            this.userName = this.getUserNameFromPage();
            this.isAuthor = false;
            this.authorToken = null;
            this.jobTitle = null;
            this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            this.unreadCount = 0;
            this.currentConversation = null;
            this.pollingInterval = null;
            this.lastMessageId = 0;

            // 🔥 ДОБАВЛЕНО: Для хранения статусов онлайн
            this.onlineStatuses = {};
            this.currentChatRoom = null; // Добавлено для совместимости
            this.startOnlineStatusPolling();


            console.log('🔍 DEBUG: User data initialized:', {
                email: this.userEmail,
                name: this.userName,
                hasEmail: !!this.userEmail
            });

            this.init();
            this.initPolling();
            this.checkAuthorAutoLogin();
        }

        // 🔥 ИСПРАВЛЕНО: Правильный polling
        initPolling() {
            this.pollingInterval = setInterval(() => {
                if (this.userEmail && typeof this.userEmail === 'string' && this.userEmail.includes('@') && this.currentConversation) {
                    this.checkNewMessages();
                }
            }, 3000);
        }
        scrollMessagesToBottom() {
            const chat = document.getElementById('chatMessages');
            if (!chat) return;

            setTimeout(() => {
                chat.scrollTop = chat.scrollHeight;
            }, 0);
        }
        initScrollBtn() {
            // найдем элементы
            this.scrollBtn = document.getElementById("scrollToBottomBtn");
            this.messagesBox = document.getElementById("chatMessages");

            // отладочные сообщения
            console.log('initScrollBtn called, btn:', this.scrollBtn, 'box:', this.messagesBox);

            if (!this.scrollBtn || !this.messagesBox) {
                // если не найдено — попробуем повесить повторную инициализацию через небольшой таймаут
                console.warn('Scroll btn or messages box not found, retrying in 200ms...');
                setTimeout(() => this.initScrollBtn(), 200);
                return;
            }

            // click handler — скролим к последнему элементу сообщения, это надежнее чем scrollHeight
            this.scrollBtn.addEventListener("click", (e) => {
                e.preventDefault();
                // найдем последний визуальный узел сообщения
                const last = this.messagesBox.lastElementChild;
                if (last) {
                    // используем scrollIntoView с плавной прокруткой
                    last.scrollIntoView({ behavior: "smooth", block: "end" });
                } else {
                    // fallback на scrollHeight
                    this.messagesBox.scrollTo({ top: this.messagesBox.scrollHeight, behavior: "smooth" });
                }
                this.scrollBtn.classList.remove("show");
            });

            // scroll handler — показываем/скрываем кнопку
            const checkAtBottom = () => {
                const atBottom = (this.messagesBox.scrollTop + this.messagesBox.clientHeight) >= (this.messagesBox.scrollHeight - 60);
                if (atBottom) {
                    this.scrollBtn.classList.remove("show");
                } else {
                    this.scrollBtn.classList.add("show");
                }
            };

            // сразу проверить состояние (на случай если уже не внизу)
            checkAtBottom();

            // подписываемся
            this.messagesBox.addEventListener("scroll", checkAtBottom);

            // также — наблюдатель за DOM, если сообщения добавляются через innerHTML и мешают событиям
            if (!this._mutationObserver && window.MutationObserver) {
                this._mutationObserver = new MutationObserver((mutations) => {
                    // при добавлении узлов — если пользователь внизу, прокрутим автоматически
                    // иначе — покажем кнопку
                    if (this.isUserNearBottom()) {
                        // используем rAF чтобы дождаться отрисовки
                        window.requestAnimationFrame(() => {
                            this.messagesBox.scrollTop = this.messagesBox.scrollHeight;
                            this.scrollBtn.classList.remove("show");
                        });
                    } else {
                        // показываем кнопку
                        this.scrollBtn.classList.add("show");
                    }
                });

                this._mutationObserver.observe(this.messagesBox, { childList: true, subtree: false });
            }
        }
        init() {
            console.log('✅ GlobalChat initialized - Unified layout');

            const chatToggleBtn = document.getElementById('chatToggleBtn');
            const closeChatBtn = document.querySelector('.close-chat');
            const sendChatBtn = document.getElementById('sendChatBtn');
            const chatInput = document.getElementById('chatInput');

            if (chatToggleBtn) chatToggleBtn.addEventListener('click', () => this.toggleChat());
            if (closeChatBtn) closeChatBtn.addEventListener('click', () => this.hideChat());
            if (sendChatBtn) sendChatBtn.addEventListener('click', () => this.sendMessage());
            if (chatInput) chatInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') this.sendMessage();
            });
            this.initScrollBtn();
            this.showConversationList();
            this.loadRealChats();
        }
        // Показывает кнопку
        showScrollBtn() {
            if (!this.scrollToBottomBtn) return;
            this.scrollToBottomBtn.classList.add('show');
        }

// Скрывает кнопку
        hideScrollBtn() {
            if (!this.scrollToBottomBtn) return;
            this.scrollToBottomBtn.classList.remove('show');
        }

// Прокрутка вниз. если instant=false — smooth
        scrollToBottom(forceSmooth = false) {
            const box = this.messagesBox || document.getElementById('chatMessages');
            if (!box) return;

            box.scrollTo({
                top: box.scrollHeight,
                behavior: forceSmooth ? 'smooth' : 'auto'
            });

            // после прокрутки прячем кнопку
            this.hideScrollBtn();
        }

// Проверка — находится ли пользователь "у самого низа"
        isUserNearBottom(threshold = 80) {
            const box = this.messagesBox || document.getElementById('chatMessages');
            if (!box) return true;
            return (box.scrollTop + box.clientHeight) >= (box.scrollHeight - threshold);
        }

// Вызывать каждый раз, когда добавлено новое сообщение (вместо непосредственного скролла)
        onMessageAppended() {
            // если пользователь уже был внизу — автоскроллим
            if (this.isUserNearBottom()) {
                // плавно прокручиваем вниз
                this.scrollToBottom(true);
            } else {
                // пользователь листал вверх — показываем кнопку (чтобы он сам прокрутил)
                this.showScrollBtn();
            }
        }
        startOnlineStatusPolling() {
            setInterval(() => {
                if (!this.currentContactEmail) return;

                fetch(`/online-status?email=${encodeURIComponent(this.currentContactEmail)}`)
                    .then(r => r.json())
                    .then(status => {

                        // сохраним в память
                        this.onlineStatuses[this.currentContactEmail] = status;

                        // обновляем заголовок
                        this.updateChatHeaderStatus(status);

                        // обновляем статус в списке
                        const chatElement = document.querySelector(`[data-chat-id="${this.currentConversation}"]`);
                        if (chatElement) {
                            this.updateChatItemStatus(chatElement, status);
                        }
                    });
            }, 4000);
        }

        // 🔥 ИСПРАВЛЕНО: renderList с data-атрибутами
        renderList(chats) {
            const listContainer = document.getElementById('chatList');

            if (!listContainer) {
                console.error('❌ chatList container not found in DOM');
                return;
            }

            listContainer.innerHTML = '';

            if (!chats || chats.length === 0) {
                listContainer.innerHTML = `
            <div class="no-chats">Žádné konverzace</div>
        `;
                return;
            }

            chats.forEach(chat => {
                const item = document.createElement('div');
                item.className = 'chat-item';

                // 🔥 ДОБАВЛЕНО: data-атрибуты для хранения данных
                item.dataset.chatId = chat.chat_room_id;
                if (chat.contactEmail) {
                    item.dataset.contactEmail = chat.contactEmail;
                }
                if (chat.contactName) {
                    item.dataset.contactName = chat.contactName;
                }
                if (chat.jobTitle) {
                    item.dataset.jobTitle = chat.jobTitle;
                }

                item.innerHTML = `
            <div class="chat-item-left">
                <div class="chat-item-contact">${chat.contactName}</div>
                <div class="chat-item-job">${chat.jobTitle}</div>
                <div class="chat-item-last">${chat.lastMessage}</div>
            </div>

            ${chat.unread > 0 ? `
                <div class="chat-unread">${chat.unread}</div>
            ` : ''}
        `;

                item.addEventListener('click', () => {
                    console.log('🎯 Chat item clicked:', {
                        chatId: chat.chat_room_id,
                        contactName: chat.contactName,
                        jobTitle: chat.jobTitle
                    });

                    // 🔥 ПЕРЕДАЕМ ВСЕ НЕОБХОДИМЫЕ ДАННЫЕ
                    this.showConversation(
                        chat.chat_room_id,
                        chat.contactName,
                        chat.jobTitle
                    );
                });

                listContainer.appendChild(item);
            });
        }

        // 🔥 ОБНОВЛЕНО: Обновление статуса в заголовке чата
        updateChatHeaderStatus(status) {
            const statusLine = document.getElementById('chatStatusLine');
            const subtitle = document.getElementById('chatSubtitle');

            if (!statusLine || !subtitle) return;

            if (status.is_online) {
                statusLine.style.display = 'flex';
                statusLine.innerHTML = `
            <span class="status-indicator online"></span>
            <span class="status-text">online</span>
        `;
                subtitle.textContent = '';
            } else {
                statusLine.style.display = 'none';
                const lastSeenLabel = window.LANG_LAST_SEEN || "Last seen:";

                subtitle.textContent = `${lastSeenLabel} ${status.last_seen}`;
            }
        }

        // 🔥 ОБНОВЛЕНО: Обновление статуса в элементе чата
        updateChatItemStatus(chatElement, status) {
            let statusElement = chatElement.querySelector('.chat-item-status');

            if (!statusElement) {
                statusElement = document.createElement('div');
                statusElement.className = 'chat-item-status';
                chatElement.querySelector('.chat-item-contact').appendChild(statusElement);
            }

            if (status.is_online) {
                statusElement.innerHTML = `
            <span class="status-indicator online"></span>
        `;
            } else {
                statusElement.innerHTML = `
            <span class="last-seen">${this.formatLastSeen(status.last_seen)}</span>
        `;
            }
        }

        // 🔥 ИСПРАВЛЕНО: Проверка автора
        async checkAuthorSession() {
            try {
                const response = await fetch('/chat/api/check-author-auth');
                const data = await response.json();

                if (data.authorized) {
                    this.isAuthor = true;
                    this.userEmail = data.author.email;
                    this.userName = data.author.name;
                    this.authorToken = data.author.token;
                    this.currentJobId = data.author.job_id;
                    console.log('✅ Author session confirmed:', data.author);
                    return true;
                }
                return false;
            } catch (error) {
                console.error('Error checking author session:', error);
                return false;
            }
        }

        // 🔥 ОБНОВЛЕНО: Форматирование времени последней активности
        formatLastSeen(timestamp) {
            if (!timestamp) return 'recently';

            const now = new Date();
            const lastSeen = new Date(timestamp);
            const diffMs = now - lastSeen;
            const diffMins = Math.floor(diffMs / 60000);
            const diffHours = Math.floor(diffMs / 3600000);
            const diffDays = Math.floor(diffMs / 86400000);

            if (diffMins < 1) return 'just now';
            if (diffMins < 60) return `${diffMins}m ago`;
            if (diffHours < 24) return `${diffHours}h ago`;
            if (diffDays < 7) return `${diffDays}d ago`;

            return lastSeen.toLocaleDateString();
        }

        // 🔥 ОБНОВЛЕНО: Показ списка контактов
        showConversationList() {
            console.log('📋 Switching to conversation list view');

            const chatBody = document.querySelector('.chat-body');
            const chatTitle = document.getElementById('chatTitle');
            const chatSubtitle = document.getElementById('chatSubtitle');
            const statusLine = document.getElementById('chatStatusLine');

            if (chatBody) chatBody.classList.remove('conversation-active');

            // 🔥 ВОЗВРАЩАЕМ ЗАГОЛОВОК ДЛЯ СПИСКА КОНТАКТОВ
            if (chatTitle) {
                chatTitle.textContent = '💬 Chat';
                console.log('✅ Set chat title to:', chatTitle.textContent);
            }
            if (chatSubtitle) {
                chatSubtitle.textContent = 'Contact list';
                console.log('✅ Set chat subtitle to:', chatSubtitle.textContent);
            }
            if (statusLine) statusLine.style.display = 'none'; // Скрываем статус

            this.currentConversation = null;
            this.currentChatRoom = null; // Сбрасываем тоже
        }

        // 🔥 ИСПРАВЛЕНО: Показ конкретного чата с валидацией
        showConversation(chatId, contactName, jobTitle) {
            console.log("🎯 showConversation called with:", {
                chatId,
                contactName,
                jobTitle,
                isValid: !!chatId && chatId !== 'undefined' && chatId !== 'null'
            });

            // 🔥 ВАЛИДАЦИЯ chatId
            if (!chatId || chatId === 'undefined' || chatId === 'null') {
                console.error('❌ Invalid chatId:', chatId);
                alert('Chyba: Nelze otevřít chat (neplatné ID)');
                return;
            }

            const chatBody = document.querySelector('.chat-body');
            const chatTitle = document.getElementById('chatTitle');
            const chatSubtitle = document.getElementById('chatSubtitle');
            const statusLine = document.getElementById('chatStatusLine');

            if (chatBody) chatBody.classList.add('conversation-active');

            // 🔥 ОБНОВЛЯЕМ ЗАГОЛОВОК ДЛЯ ЧАТА
            if (chatTitle) {
                chatTitle.textContent = `💬 ${contactName || 'Chat'}`;
                console.log('✅ Set chat title to:', chatTitle.textContent);
            }
            if (chatSubtitle) {
                chatSubtitle.textContent = jobTitle || '';
                console.log('✅ Set chat subtitle to:', chatSubtitle.textContent);
            }
            if (statusLine) statusLine.style.display = 'flex'; // Показываем контейнер статуса

            this.currentConversation = chatId;
            this.currentChatRoom = chatId; // Для совместимости
            this.currentContactEmail = this.getContactEmailFromChat(chatId);

            // 🔥 ВЫЗЫВАЕМ loadChatHistory с проверкой
            console.log('🔄 Loading chat history for ID:', chatId);
            this.loadChatHistory(chatId);

            // 🔥 ОБНОВЛЯЕМ СТАТУС В ЗАГОЛОВКЕ
            if (this.currentContactEmail && this.onlineStatuses[this.currentContactEmail]) {
                this.updateChatHeaderStatus(this.onlineStatuses[this.currentContactEmail]);
            }
        }

        // 🔥 ДОБАВЛЕНО: Получение email контакта из чата
        getContactEmailFromChat(chatId) {
            // Ищем элемент чата с этим ID
            const chatElement = document.querySelector(`[data-chat-id="${chatId}"]`);
            if (chatElement && chatElement.dataset.contactEmail) {
                console.log('✅ Found contact email from DOM:', chatElement.dataset.contactEmail);
                return chatElement.dataset.contactEmail;
            }

            console.log('⚠️ Contact email not found in DOM for chat:', chatId);
            return null;
        }

        // 🔥 ИСПРАВЛЕНО: renderChatList с правильными полями
        renderChatList(chats) {
            console.log("🔥 Raw chats data from API:", chats);

            const normalized = chats.map(chat => {
                // 🔥 ОБЕСПЕЧИВАЕМ НАЛИЧИЕ ОБЯЗАТЕЛЬНЫХ ПОЛЕЙ
                const chatRoomId = chat.chat_room_id || chat.id || chat.room_id || `chat_${Date.now()}_${Math.random()}`;

                return {
                    chat_room_id: chatRoomId, // 🔥 КРИТИЧЕСКИ ВАЖНО
                    contactName: chat.contactName || chat.partner_name || "Unknown Contact",
                    contactEmail: chat.contactEmail || "",
                    jobTitle: chat.jobTitle || chat.job_title || "",
                    lastMessage: chat.lastMessage || chat.last_message || "",
                    unread: chat.unread || chat.unread_count || 0,
                };
            });

            console.log("🔥 Normalized chats for rendering:", normalized);
            this.renderList(normalized);
        }

        // 🔥 ОБНОВЛЕНО: Форматирование времени для списка
        formatTime(timestamp) {
            if (!timestamp) return 'now';

            const date = new Date(timestamp);
            const now = new Date();
            const diffMs = now - date;
            const diffMins = Math.floor(diffMs / 60000);
            const diffHours = Math.floor(diffMs / 3600000);
            const diffDays = Math.floor(diffMs / 86400000);

            if (diffMins < 1) return 'now';
            if (diffMins < 60) return `${diffMins}m`;
            if (diffHours < 24) return `${diffHours}h`;
            if (diffDays < 7) return `${diffDays}d`;

            return date.toLocaleDateString();
        }

        // 🔥 ИСПРАВЛЕНО: Автологин автора
        async checkAuthorAutoLogin() {
            const urlParams = new URLSearchParams(window.location.search);
            const autoOpen = urlParams.get('auto_open');

            if (autoOpen) {
                console.log('🔄 Auto-opening chat for author');
                const isAuthor = await this.checkAuthorSession();

                if (isAuthor) {
                    setTimeout(() => {
                        this.toggleChat();
                        // this.loadAuthorConversations();
                    }, 1000);
                }
            }
        }

        // 🔥 ИСПРАВЛЕНО: Загрузка реальных чатов с обработкой пустых ответов
        async loadRealChats() {
            try {
                console.log('🔄 Loading REAL conversations from database');

                // 🔥 ИСПРАВЛЕНО: Используем только ОСНОВНОЙ endpoint
                const apiUrl = `/chat/api/conversations?email=${encodeURIComponent(this.userEmail)}`;
                console.log('📡 Using main API URL:', apiUrl);

                const response = await fetch(apiUrl);

                console.log('📡 Response status:', response.status);
                console.log('📡 Response ok:', response.ok);

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const responseText = await response.text();
                console.log('📡 Raw response:', responseText);

                if (!responseText || responseText.trim() === '') {
                    console.warn('⚠️ Empty response from server, using demo data');
                    this.loadDemoChats();
                    return;
                }

                const conversations = JSON.parse(responseText);
                console.log('✅ REAL conversations loaded:', conversations);

                if (conversations && Array.isArray(conversations) && conversations.length > 0) {
                    this.renderChatList(conversations);
                    const totalUnread = conversations.reduce((sum, chat) => sum + (chat.unread_count || chat.unread || 0), 0);
                    this.showNotification(totalUnread);
                } else {
                    console.log('📭 No conversations found in database, using demo data');
                    this.loadDemoChats();
                }

            } catch (error) {
                console.error('❌ Error loading real conversations:', error);
                console.log('🔄 Falling back to demo data');
                this.loadDemoChats();
            }
        }

        // 🔥 ИСПРАВЛЕНО: Получение временного email
        getTemporaryEmail() {
            const possibleEmails = [
                new URLSearchParams(window.location.search).get('email'),
                document.querySelector('[data-user-email]')?.dataset.userEmail,
                document.querySelector('[data-email]')?.dataset.email,
                document.querySelector('input[type="email"]')?.value,
                document.querySelector('input[name="email"]')?.value,
                this.extractEmailFromPage()
            ];

            const foundEmail = possibleEmails.find(email => email && typeof email === 'string' && email.includes('@'));
            return foundEmail || 'fallback@example.com';
        }

        // 🔥 УЛУЧШЕНО: Поиск email в тексте страницы
        extractEmailFromPage() {
            try {
                const emailRegex = /[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/g;
                const bodyText = document.body.innerText;
                const matches = bodyText.match(emailRegex);

                if (matches) {
                    // Ищем email который выглядит как реальный (не demo@example.com и т.д.)
                    const realEmails = matches.filter(email =>
                        !email.includes('example.com') &&
                        !email.includes('test.com') &&
                        !email.includes('demo.com') &&
                        email.length > 5
                    );

                    if (realEmails.length > 0) {
                        return realEmails[0];
                    }

                    // Если реальных нет, берем первый
                    return matches[0];
                }
            } catch (error) {
                console.error('Error extracting email from page:', error);
            }

            return null;
        }

        // 🔥 ИСПРАВЛЕНО: Проверка новых сообщений
        async checkNewMessages() {
            if (!this.currentConversation || !this.userEmail) {
                console.log('⚠️ Skipping new messages check - no conversation selected');
                return;
            }

            try {
                const apiUrl = `/chat/api/check-messages?chat_room_id=${this.currentConversation}&email=${encodeURIComponent(this.userEmail)}&last_id=${this.lastMessageId}`;
                const response = await fetch(apiUrl);

                if (!response.ok) {
                    if (response.status === 400) return;
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const newMessages = await response.json();

                if (newMessages && newMessages.length > 0) {
                    console.log('🆕 New messages found:', newMessages.length);
                    newMessages.forEach(msg => {
                        // 🔥 ИСПРАВЛЕНО: Правильные параметры
                        this.addMessageToChat(msg.type, msg.message, msg.sender_name, msg.created_at, msg.id);
                        this.lastMessageId = Math.max(this.lastMessageId, msg.id);
                    });
                    this.loadRealChats(); // Обновляем список чатов
                    this.scrollMessagesToBottom();
                }
            } catch (error) {
                console.error('Error checking new messages:', error);
            }
        }

        // 🔥 ИСПРАВЛЕНО: Загрузка истории чата с улучшенной отладкой
        async loadChatHistory(chatRoomId) {
            console.trace("📞 loadChatHistory called from stack:");

            const messagesDiv = document.getElementById('chatMessages');
            if (!messagesDiv) {
                console.error('❌ chatMessages element not found');
                return;
            }

            console.log("➡️ loadChatHistory called with argument:", chatRoomId);
            console.log("➡️ currentConversation:", this.currentConversation);
            console.log("➡️ currentChatRoom:", this.currentChatRoom);

            messagesDiv.innerHTML = '<div class="loading">Načítání zpráv...</div>';

            try {
                // 🔥 1) БЕРЁМ chat_room_id ПРАВИЛЬНО
                const roomId =
                    chatRoomId ||
                    this.currentConversation ||
                    this.currentChatRoom ||
                    null;

                console.log("🆔 Final roomId to use:", roomId);

                if (!roomId) {
                    console.warn('❗ loadChatHistory called without chatRoomId');
                    messagesDiv.innerHTML = '<div class="no-messages">Žádné zprávy (není vybrán chat)</div>';
                    this.lastMessageId = 0;
                    return;
                }

                // 🔥 2) Email пользователя
                if (!this.userEmail) {
                    this.userEmail = this.getUserEmailFromPage();
                }

                const apiUrl =
                    `/chat/api/messages?chat_room_id=${encodeURIComponent(roomId)}` +
                    `&email=${encodeURIComponent(this.userEmail)}`;

                console.log('🔄 Loading chat history from:', apiUrl);

                // 🔥 3) ДЕЛАЕМ ЗАПРОС
                const response = await fetch(apiUrl);

                console.log('📡 Response status:', response.status);
                console.log('📡 Response content-type:', response.headers.get('content-type'));

                if (!response.ok) {
                    const txt = await response.text().catch(() => '');
                    console.error('❌ Error text:', txt);
                    throw new Error(`HTTP error ${response.status}`);
                }

                // 🔥 4) ПРОБУЕМ JSON
                let data;
                try {
                    data = await response.json();
                } catch (e) {
                    const raw = await response.text().catch(() => '');
                    console.error('❌ Invalid JSON! Raw:', raw);
                    throw new Error("Server returned invalid JSON");
                }

                console.log("✅ Chat history data received:", data);

                messagesDiv.innerHTML = '';

                // 🔥 5) Поддержка ВСЕХ возможных форматов
                let messages = [];

                if (Array.isArray(data)) {
                    messages = data;
                } else if (Array.isArray(data.messages)) {
                    messages = data.messages;
                } else if (Array.isArray(data.data)) {
                    messages = data.data;
                } else if (data && typeof data === 'object') {
                    // Пробуем найти массив сообщений в объекте
                    for (const key in data) {
                        if (Array.isArray(data[key])) {
                            messages = data[key];
                            break;
                        }
                    }
                }

                console.log("📨 Extracted messages:", messages);

                // 🔥 6) Нет сообщений
                if (!messages || messages.length === 0) {
                    messagesDiv.innerHTML = '<div class="no-messages">Žádné zprávy</div>';
                    this.lastMessageId = 0;
                    return;
                }

                // 🔥 7) Показываем сообщения
                this.lastMessageId = 0;

                messages.forEach(msg => {
                    const msgId = msg.id ?? msg.message_id ?? null;
                    const msgText = msg.message ?? msg.text ?? "";
                    const createdAt = msg.created_at ?? msg.time ?? null;

                    const isAuthor = msg.is_author != null
                        ? !!msg.is_author
                        : msg.sender_email === this.userEmail;

                    const senderName =
                        msg.sender_name ??
                        (isAuthor ? this.getCleanCustomerName() : "Uživatel");

                    const type = isAuthor ? "outgoing" : "incoming";

                    this.addMessageToChat(type, msgText, senderName, createdAt, msgId);

                    if (msgId) {
                        this.lastMessageId = Math.max(this.lastMessageId, msgId);
                    }
                });

                console.log("✅ Chat history loaded. lastMessageId =", this.lastMessageId);
                this.scrollMessagesToBottom();

            } catch (error) {
                console.error("❌ Error loading messages:", error);
                messagesDiv.innerHTML =
                    `<div class="error">Chyba při načítání zpráv: ${error.message}</div>`;
            }
        }

        // 🔥 ИСПРАВЛЕНО: Отправка сообщения с правильным именем
        async sendMessage() {
            const input = document.getElementById('chatInput');
            const message = input?.value.trim();

            if (!message) {
                console.warn('❌ Cannot send empty message');
                return;
            }

            if (!this.currentConversation) {
                console.error('❌ Cannot send message - no conversation selected');
                alert('Vyberte prosím chat pro odeslání zprávy');
                return;
            }

            console.log('📤 Sending message with data:', {
                chat_room_id: this.currentConversation,
                sender_email: this.userEmail,
                message: message
            });

            // Добавляем сообщение в UI (временно, пока не получим ответ)
            this.addMessageToChat('outgoing', message, 'Vy: ');
            this.scrollToBottom(true);
            input.value = '';

            try {
                const response = await fetch('/chat/api/send-message', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken
                    },
                    body: JSON.stringify({
                        chat_room_id: this.currentConversation,
                        sender_email: this.userEmail,
                        message: message
                    })
                });

                const result = await response.json();

                if (!response.ok) {
                    throw new Error(result.message || 'Send failed');
                }

                console.log('✅ Message sent successfully:', result);

                // Обновляем lastMessageId
                if (result.message_id) {
                    this.lastMessageId = Math.max(this.lastMessageId, result.message_id);
                }

            } catch (error) {
                console.error('❌ Error sending message:', error);
                // Можно добавить уведомление пользователю
            }
            this.onMessageAppended();
        }

        // 🔥 ДОБАВЬТЕ ЭТОТ МЕТОД: Очистка имени кандидата
        getCleanCustomerName() {
            let name = this.userName;

            // Убираем email из имени если есть дублирование
            if (name && name.includes('tanatar.sro@seznam.cz')) {
                name = name.replace('tanatar.sro@seznam.cz', '').trim();
                name = name || 'Ермек'; // fallback
            }

            // Убираем любые email из имени
            const emailRegex = /[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/g;
            name = name.replace(emailRegex, '').trim();

            return name || 'Candidate';
        }

        // 🔥 ИСПРАВЛЕНО: Получение email для кандидата
        getUserEmailFromPage() {
            console.log('🔍 DEBUG: Searching for email on page...');

            // 1. Проверяем сессию автора в первую очередь
            if (sessionStorage.getItem('author_token')) {
                const authorEmail = sessionStorage.getItem('user_email');
                if (authorEmail && authorEmail.includes('@')) {
                    console.log('✅ Found author email from session:', authorEmail);
                    return authorEmail;
                }
            }

            // 2. Проверяем данные кандидата из PHP сессии
            const customerEmailFromData = document.querySelector('[data-user-email]')?.dataset.userEmail;
            if (customerEmailFromData && customerEmailFromData.includes('@')) {
                console.log('✅ Found customer email from data attribute:', customerEmailFromData);
                return customerEmailFromData;
            }

            // 3. 🔥 НОВОЕ: Проверяем window.currentCustomer (из PHP)
            if (window.currentCustomer && window.currentCustomer.email && window.currentCustomer.email.includes('@')) {
                console.log('✅ Found customer email from window.currentCustomer:', window.currentCustomer.email);
                return window.currentCustomer.email;
            }

            // 4. 🔥 НОВОЕ: Проверяем window.currentUser (из отладочного скрипта)
            if (window.currentUser && window.currentUser.email && window.currentUser.email.includes('@')) {
                console.log('✅ Found customer email from window.currentUser:', window.currentUser.email);
                return window.currentUser.email;
            }

            // 5. 🔥 НОВОЕ: Ищем email в тексте страницы
            const pageEmail = this.extractEmailFromPage();
            if (pageEmail) {
                console.log('✅ Found customer email from page text:', pageEmail);
                return pageEmail;
            }

            // 6. 🔥 НОВОЕ: Проверяем форму на странице
            const formEmail = document.querySelector('input[type="email"]')?.value ||
                document.querySelector('input[name="email"]')?.value;
            if (formEmail && formEmail.includes('@')) {
                console.log('✅ Found customer email from form:', formEmail);
                return formEmail;
            }

            console.warn('❌ No email found on page, using fallback');
            return 'fallback@example.com';
        }

        // 🔥 ИСПРАВЛЕНО: Получение имени для кандидата
        getUserNameFromPage() {
            console.log('🔍 DEBUG: Searching for user name...');

            // 1. Проверяем window.currentCustomer (из PHP)
            if (window.currentCustomer && window.currentCustomer.name && window.currentCustomer.name !== 'User') {
                let name = window.currentCustomer.name;
                console.log('✅ Found name from window.currentCustomer:', name);

                // 🔥 УБИРАЕМ ДУБЛИРОВАНИЕ EMAIL В ИМЕНИ
                if (name.includes('tanatar.sro@seznam.cz')) {
                    name = name.replace('tanatar.sro@seznam.cz', '').trim();
                    name = name || 'Ермек'; // fallback если имя пустое
                }
                return name;
            }

            // 2. Проверяем window.currentUser (из отладочного скрипта)
            if (window.currentUser && window.currentUser.name && window.currentUser.name !== 'User') {
                let name = window.currentUser.name;
                console.log('✅ Found name from window.currentUser:', name);

                if (name.includes('tanatar.sro@seznam.cz')) {
                    name = name.replace('tanatar.sro@seznam.cz', '').trim();
                    name = name || 'Ермек';
                }
                return name;
            }

            // 3. Проверяем data-атрибуты
            const nameElement = document.querySelector('[data-user-name]');
            if (nameElement && nameElement.dataset.userName && nameElement.dataset.userName !== 'User') {
                let name = nameElement.dataset.userName;
                console.log('✅ Found name from data-attribute:', name);

                if (name.includes('tanatar.sro@seznam.cz')) {
                    name = name.replace('tanatar.sro@seznam.cz', '').trim();
                    name = name || 'Ермек';
                }
                return name;
            }

            // 4. 🔥 НОВОЕ: Ищем имя в тексте страницы
            const pageName = this.extractNameFromPage();
            if (pageName) {
                console.log('✅ Found name from page text:', pageName);
                return pageName;
            }

            // 5. 🔥 НОВОЕ: Проверяем форму на странице
            const formName = document.querySelector('input[name="name"]')?.value;
            if (formName && formName !== 'User') {
                console.log('✅ Found name from form:', formName);
                return formName;
            }

            console.warn('❌ No user name found, using fallback');
            return 'Ермек'; // fallback имя
        }

        // 🔥 ДОБАВЬТЕ ЭТОТ МЕТОД: Поиск имени в тексте страницы
        extractNameFromPage() {
            try {
                // Ищем русские имена в тексте страницы
                const nameRegex = /[А-Яа-яЁё]{2,20}/g;
                const bodyText = document.body.innerText;
                const matches = bodyText.match(nameRegex);

                if (matches) {
                    // Ищем имя "Ермек" или подобные
                    const foundName = matches.find(name =>
                        name.includes('Ермек') ||
                        name.includes('ермек') ||
                        name.length > 2
                    );

                    return foundName || matches[0];
                }
            } catch (error) {
                console.error('Error extracting name from page:', error);
            }

            return null;
        }

        // 🔥 ИСПРАВЛЕНО: Добавление сообщения в чат
        addMessageToChat(type, text, senderName = null, timestamp = null, messageId = null) {
            const messagesDiv = document.getElementById('chatMessages');
            if (!messagesDiv) return;

            const messageClass = `message message-${type}`;
            const displayName = senderName || (type === 'outgoing' ? 'Vy' : 'Uživatel');
            const time = timestamp ? new Date(timestamp).toLocaleTimeString('cs-CZ', {
                hour: '2-digit', minute: '2-digit'
            }) : new Date().toLocaleTimeString('cs-CZ', {
                hour: '2-digit', minute: '2-digit'
            });

            const messageHtml = `
                <div class="${messageClass}" data-message-id="${messageId || ''}">
                    <div class="message-sender"><b>${displayName}</b></div>
                    <div class="message-text">${text}</div>
                    <div class="message-time">${time}</div>
                </div>
            `;

            messagesDiv.innerHTML += messageHtml;
            this.onMessageAppended();

            // Обновляем lastMessageId если передан
            if (messageId) {
                this.lastMessageId = Math.max(this.lastMessageId, messageId);
            }
        }

        showNotification(count) {
            const notification = document.getElementById('chatNotification');
            if (notification) {
                this.unreadCount = count;
                notification.textContent = count > 99 ? '99+' : count;
                notification.style.display = count > 0 ? 'flex' : 'none';
            }
        }

        toggleChat() {
            const modal = document.getElementById('globalChatModal');
            const toggleBtn = document.getElementById('chatToggleBtn');

            if (modal && toggleBtn) {
                if (modal.style.display === 'none' || modal.style.display === '') {
                    this.showChat();
                } else {
                    this.hideChat();
                }
            }
        }

        showChat() {
            const modal = document.getElementById('globalChatModal');
            const toggleBtn = document.getElementById('chatToggleBtn');

            if (modal && toggleBtn) {
                modal.style.display = 'flex';
                toggleBtn.classList.add('hidden');
                this.showConversationList();
                this.loadRealChats(); // Загружаем чаты при открытии
            }
        }

        hideChat() {
            const modal = document.getElementById('globalChatModal');
            const toggleBtn = document.getElementById('chatToggleBtn');

            if (modal && toggleBtn) {
                modal.style.display = 'none';
                toggleBtn.classList.remove('hidden');
            }
        }

        loadDemoChats() {
            console.log('🎭 Loading demo chats');
            const demoChats = [{
                chat_room_id: 'demo_1',
                contactName: 'Jan Novák',
                contactEmail: 'jan@example.com',
                jobTitle: 'Stavbyvedoucí - Praha',
                lastMessage: 'Dobrý den, mám zájem o práci...',
                unread: 2
            }];
            this.renderChatList(demoChats);
            this.showNotification(2);
        }

        destroy() {
            if (this.pollingInterval) {
                clearInterval(this.pollingInterval);
            }
        }
    }

    // Инициализация
    document.addEventListener('DOMContentLoaded', function() {
        console.log('🚀 DOM loaded, initializing GlobalChat');
        window.globalChat = new GlobalChat();
    });
</script>

{{-- Отладочная информация --}}
<script>
    console.log('🔍 PAGE DEBUG INFO:');
    console.log('Customer data:', @json($customer ?? null));
    console.log('Session data:', {
        customer_email: "{{ session('customer_email') }}",
        customer_name: "{{ session('customer_name') }}",
        user_email: "{{ session('user_email') }}",
        author_logged_in: "{{ session('author_logged_in') }}"
    });

    // 🔥 ИСПРАВЛЕНО: Правильная передача данных кандидата ИЗ БАЗЫ ДАННЫХ
    window.currentCustomer = @json($customer ?? null);
    window.currentUser = {
        email: "{{ $customer->email ?? session('customer_email') ?? '' }}",
        name: "{{ $customer->name ?? session('customer_name') ?? 'Ермек' }}" // 🔥 FALLBACK "Ермек" вместо "User"
    };

    console.log('Final global variables:', {
        currentCustomer: window.currentCustomer,
        currentUser: window.currentUser
    });
</script>
