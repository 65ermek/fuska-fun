
    // Полностью новый класс чата без restoreChat
    class GlobalChat {
    constructor() {
    this.currentJobId = null;
    this.userEmail = null;
    this.userName = null;
    this.isAuthor = false;
    this.authorToken = null;
    this.jobTitle = null;
    this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    this.init();
}

    init() {
    console.log('Initializing GlobalChat...');

    const chatToggleBtn = document.getElementById('chatToggleBtn');
    const closeChatBtn = document.querySelector('.close-chat');
    const sendChatBtn = document.getElementById('sendChatBtn');
    const chatInput = document.getElementById('chatInput');

    // Проверяем существование элементов
    if (!chatToggleBtn) {
    console.error('Chat toggle button not found!');
    return;
}

    if (!closeChatBtn) {
    console.error('Close chat button not found!');
}

    if (!sendChatBtn) {
    console.error('Send chat button not found!');
}

    if (!chatInput) {
    console.error('Chat input not found!');
}

    // Добавляем обработчики
    chatToggleBtn.addEventListener('click', () => this.toggleChat());

    if (closeChatBtn) {
    closeChatBtn.addEventListener('click', () => this.hideChat());
}

    if (sendChatBtn) {
    sendChatBtn.addEventListener('click', () => this.sendMessage());
}

    if (chatInput) {
    chatInput.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') this.sendMessage();
});
}

    console.log('GlobalChat initialized successfully');
}

    // Основные методы управления интерфейсом
    toggleChat() {
    const modal = document.getElementById('globalChatModal');
    const toggleBtn = document.getElementById('chatToggleBtn');

    if (!modal || !toggleBtn) {
    console.error('Chat elements not found for toggle');
    return;
}

    if (modal.style.display === 'none' || modal.style.display === '') {
    this.showChat();
} else {
    this.hideChat();
}
}

    showChat() {
    const modal = document.getElementById('globalChatModal');
    const toggleBtn = document.getElementById('chatToggleBtn');

    if (modal && toggleBtn) {
    modal.style.display = 'flex';
    toggleBtn.classList.add('hidden');
    console.log('Chat shown');
}
}

    hideChat() {
    const modal = document.getElementById('globalChatModal');
    const toggleBtn = document.getElementById('chatToggleBtn');

    if (modal && toggleBtn) {
    modal.style.display = 'none';
    toggleBtn.classList.remove('hidden');
    console.log('Chat hidden');
}
}

    // Отправка сообщения
    async sendMessage() {
    const input = document.getElementById('chatInput');
    if (!input) {
    console.error('Chat input not found');
    return;
}

    const message = input.value.trim();

    if (!message) {
    console.log('Empty message, not sending');
    return;
}

    console.log('Sending message:', message);

    // Показываем сообщение в чате
    this.addMessageToChat('outgoing', message, 'Vy');
    input.value = '';

    // Демо-режим - просто логируем
    // Позже добавим реальную отправку на сервер
}

    // Добавление сообщения в чат
    addMessageToChat(type, text, senderName = null) {
    const messagesDiv = document.getElementById('chatMessages');
    if (!messagesDiv) {
    console.error('Chat messages div not found');
    return;
}

    const messageClass = `message message-${type}`;
    const displayName = senderName || (type === 'outgoing' ? 'Vy' : 'Uživatel');

    const messageHtml = `
            <div class="${messageClass}">
                <div class="message-sender"><b>${displayName}</b></div>
                <div class="message-text">${text}</div>
                <div class="message-time">${new Date().toLocaleTimeString()}</div>
            </div>
        `;

    messagesDiv.innerHTML += messageHtml;
    messagesDiv.scrollTop = messagesDiv.scrollHeight;

    console.log('Message added to chat:', { type, text, senderName: displayName });
}

    // Метод для открытия чата с конкретным объявлением
    openNewChat(jobId, jobTitle, userEmail = null, userName = null) {
    console.log('Opening new chat:', { jobId, jobTitle, userEmail, userName });

    this.currentJobId = jobId;
    this.userEmail = userEmail;
    this.userName = userName;
    this.jobTitle = jobTitle;

    // Обновляем заголовок
    const chatTitle = document.getElementById('chatTitle');
    const chatSubtitle = document.getElementById('chatSubtitle');

    if (chatTitle) chatTitle.textContent = `💬 ${jobTitle}`;
    if (chatSubtitle) chatSubtitle.textContent = userEmail || 'Nová konverzace';

    // Показываем блок ввода
    const inputContainer = document.getElementById('chatInputContainer');
    if (inputContainer) inputContainer.style.display = 'block';

    // Очищаем сообщения и показываем приветствие
    const messagesDiv = document.getElementById('chatMessages');
    if (messagesDiv) {
    messagesDiv.innerHTML = `
                <div class="chat-welcome">
                    <div class="welcome-icon">💬</div>
                    <h3>Nová konverzace</h3>
                    <p>Začněte psát první zprávu</p>
                </div>
            `;
}

    this.showChat();
}
}

    // Безопасная инициализация
    document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing GlobalChat...');

    // Проверяем существование необходимых элементов
    const chatModal = document.getElementById('globalChatModal');
    const chatToggleBtn = document.getElementById('chatToggleBtn');

    if (chatModal || chatToggleBtn) {
    window.globalChat = new GlobalChat();
    console.log('GlobalChat created successfully');
} else {
    console.warn('Chat elements not found, GlobalChat not initialized');
}
});
