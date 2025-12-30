{{-- resources/views/chat/my-chats.blade.php --}}
@extends('layouts.chat')

@section('title', 'Мои чаты')
@section('header', 'Мои чаты')

@section('content')
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-medium text-gray-900">Все чаты</h2>
                <span class="text-sm text-gray-500" id="chats-count"></span>
            </div>
        </div>

        <div class="divide-y divide-gray-200" id="chats-list">
            <!-- Список чатов будет загружен через AJAX -->
            <div class="p-8 text-center text-gray-500">
                <i class="fas fa-comments text-4xl mb-4"></i>
                <p>Загрузка чатов...</p>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadChats();

            function loadChats() {
                const userEmail = '{{ session("user_email") }}';
                const token = '{{ session("candidate_token") }}' || '{{ $job->edit_token ?? "" }}';

                if (!userEmail || !token) {
                    document.getElementById('chats-list').innerHTML = `
                <div class="p-8 text-center text-gray-500">
                    <i class="fas fa-exclamation-triangle text-2xl mb-4"></i>
                    <p>Необходима авторизация для просмотра чатов</p>
                </div>
            `;
                    return;
                }

                fetch(`/chat/api/my-chats?user_email=${encodeURIComponent(userEmail)}&token=${encodeURIComponent(token)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            displayChats(data.chats);
                        } else {
                            showError('Ошибка загрузки чатов');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showError('Ошибка загрузки чатов');
                    });
            }

            function displayChats(chats) {
                const chatsList = document.getElementById('chats-list');
                const chatsCount = document.getElementById('chats-count');

                if (chats.length === 0) {
                    chatsList.innerHTML = `
                <div class="p-8 text-center text-gray-500">
                    <i class="fas fa-comments text-4xl mb-4"></i>
                    <p>У вас пока нет чатов</p>
                    <p class="text-sm mt-2">Начните общение, откликнувшись на объявление</p>
                </div>
            `;
                    chatsCount.textContent = '0 чатов';
                    return;
                }

                chatsCount.textContent = `${chats.length} ${pluralize(chats.length, ['чат', 'чата', 'чатов'])}`;

                chatsList.innerHTML = chats.map(chat => `
            <div class="p-4 hover:bg-gray-50 cursor-pointer chat-item" data-chat-id="${chat.id}">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">
                                ${getInitials(chat.candidate_name)}
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-900 truncate">
                                    ${chat.candidate_name}
                                </p>
                                <div class="flex items-center space-x-2 ml-2">
                                    ${chat.unread_count > 0 ? `
                                        <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-500 rounded-full">
                                            ${chat.unread_count}
                                        </span>
                                    ` : ''}
                                    <span class="text-xs text-gray-500">
                                        ${formatTime(chat.updated_at)}
                                    </span>
                                </div>
                            </div>
                            <p class="text-sm text-gray-600 truncate mt-1">
                                ${chat.job?.title || 'Объявление'}
                            </p>
                            <p class="text-sm text-gray-500 truncate mt-1">
                                ${chat.last_message?.message || 'Нет сообщений'}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </div>
                </div>
            </div>
        `).join('');

                // Добавляем обработчики клика
                document.querySelectorAll('.chat-item').forEach(item => {
                    item.addEventListener('click', function() {
                        const chatId = this.getAttribute('data-chat-id');
                        window.location.href = `/chat/room/${chatId}`;
                    });
                });
            }

            function getInitials(name) {
                return name.split(' ').map(n => n[0]).join('').toUpperCase();
            }

            function formatTime(dateString) {
                const date = new Date(dateString);
                const now = new Date();
                const diff = now - date;

                if (diff < 24 * 60 * 60 * 1000) {
                    return date.toLocaleTimeString('cs-CZ', { hour: '2-digit', minute: '2-digit' });
                } else {
                    return date.toLocaleDateString('cs-CZ');
                }
            }

            function pluralize(number, forms) {
                const cases = [2, 0, 1, 1, 1, 2];
                return forms[(number % 100 > 4 && number % 100 < 20) ? 2 : cases[Math.min(number % 10, 5)]];
            }

            function showError(message) {
                document.getElementById('chats-list').innerHTML = `
            <div class="p-8 text-center text-red-500">
                <i class="fas fa-exclamation-triangle text-2xl mb-4"></i>
                <p>${message}</p>
                <button onclick="loadChats()" class="mt-2 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    Попробовать снова
                </button>
            </div>
        `;
            }

            // Обновляем чаты каждые 30 секунд
            setInterval(loadChats, 30000);
        });
    </script>
@endsection
