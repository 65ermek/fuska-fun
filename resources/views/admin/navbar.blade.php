<!-- resources/views/admin/navbar.blade.php -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars"></i>
            </a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{ route('admin.dashboard') }}" class="nav-link">@lang('admin.dashboard')</a>
        </li>
    </ul>

    <ul class="navbar-nav ml-auto">
        {{-- уведомления --}}
        <li class="nav-item dropdown mr-3">
            <a class="nav-link" data-toggle="dropdown" href="#" id="notifications-dropdown">
                <i class="far fa-bell"></i>
                <span class="badge badge-danger position-absolute" id="notification-count"
                      style="top: 0; right: 0; font-size: 0.6rem; padding: 2px 4px; background: transparent;">
                0
                </span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        <span class="dropdown-item dropdown-header" id="notification-header">
            <span id="notification-count-text"></span>
        </span>
                <div class="dropdown-divider"></div>
                <div id="notifications-list">
                    {{-- Уведомления будут загружены через AJAX --}}
                </div>
                <div class="dropdown-divider"></div>
                <a href="{{ route('admin.contact-requests.index') }}" class="dropdown-item dropdown-footer">
                    Все сообщения
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item dropdown-footer text-center" id="mark-all-read">
                    Пометить все как прочитанные
                </a>
            </div>
        </li>
        <!-- Language Switch -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="flag-icon flag-icon-{{
                    app()->getLocale() == 'ru' ? 'ru' :
                    (app()->getLocale() == 'uz' ? 'uz' :
                    (app()->getLocale() == 'uk' ? 'ua' :
                    (app()->getLocale() == 'ro' ? 'ro' :
                    (app()->getLocale() == 'cs' ? 'cz' : 'us'))))
                }}"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right p-0">
                <a href="{{ route('language.switch', ['lang' => 'ru']) }}"
                   class="dropdown-item {{ app()->getLocale() == 'ru' ? 'active' : '' }}">
                    <i class="flag-icon flag-icon-ru mr-2"></i> Русский
                </a>
                <a href="{{ route('language.switch', ['lang' => 'en']) }}"
                   class="dropdown-item {{ app()->getLocale() == 'en' ? 'active' : '' }}">
                    <i class="flag-icon flag-icon-us mr-2"></i> English
                </a>
                <a href="{{ route('language.switch', ['lang' => 'uz']) }}"
                   class="dropdown-item {{ app()->getLocale() == 'uz' ? 'active' : '' }}">
                    <i class="flag-icon flag-icon-uz mr-2"></i> O'zbekcha
                </a>
                <a href="{{ route('language.switch', ['lang' => 'uk']) }}"
                   class="dropdown-item {{ app()->getLocale() == 'uk' ? 'active' : '' }}">
                    <i class="flag-icon flag-icon-ua mr-2"></i> Українська
                </a>
                <a href="{{ route('language.switch', ['lang' => 'ro']) }}"
                   class="dropdown-item {{ app()->getLocale() == 'ro' ? 'active' : '' }}">
                    <i class="flag-icon flag-icon-ro mr-2"></i> Română
                </a>
                <a href="{{ route('language.switch', ['lang' => 'cs']) }}"
                   class="dropdown-item {{ app()->getLocale() == 'cs' ? 'active' : '' }}">
                    <i class="flag-icon flag-icon-cz mr-2"></i> Čeština
                </a>
            </div>
        </li>

        <!-- User Menu -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#" style="display: flex">
                <div class="image">
                    @auth
                        <img src="{{ Auth::user()->avatar ? asset('images/avatars/' . Auth::user()->avatar) : asset('images/avatars/default-avatar.png') }}"
                             class="img-circle elevation-2 mr-3" style="width: 32px"
                             alt="{{ Auth::user()->name }}">
                    @else
                        <img src="{{ asset('images/avatars/default-avatar.png') }}"
                             class="img-circle elevation-2"
                             alt="Guest">
                    @endauth
                </div>
                {{ auth()->user()->name ?? 'User' }}
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <a href="/admin/profile" class="dropdown-item">
                    <i class="fas fa-user mr-2"></i> @lang('admin.profile')
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt mr-2"></i> @lang('admin.logout')
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </li>
    </ul>
</nav>
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const notificationDropdown = document.getElementById('notifications-dropdown');
            const notificationCount = document.getElementById('notification-count');
            const notificationCountText = document.getElementById('notification-count-text');
            const notificationsList = document.getElementById('notifications-list');
            const markAllReadBtn = document.getElementById('mark-all-read');

            // Загружаем количество уведомлений
            function loadNotificationCount() {
                fetch('{{ route("admin.notifications.count") }}')
                    .then(response => response.json())
                    .then(data => {
                        notificationCount.innerHTML = data.html;
                        notificationCountText.textContent = data.count + ' Непрочитанных уведомлений';
                    });
            }

            // Загружаем список уведомлений
            function loadNotifications() {
                fetch('{{ route("admin.notifications.list") }}')
                    .then(response => response.json())
                    .then(data => {
                        if (data.length === 0) {
                            notificationsList.innerHTML = `
                        <div class="dropdown-item">
                            <div class="text-center text-muted py-2">
                                <i class="far fa-check-circle fa-2x mb-2"></i>
                                <p class="mb-0">Нет новых уведомлений</p>
                            </div>
                        </div>
                    `;
                            return;
                        }

                        let html = '';
                        data.forEach(notification => {
                            html += `
                        <a href="${notification.url}" class="dropdown-item notification-item" data-id="${notification.id}">
                            <div class="media">
                                <div class="media-body">
                                    <h6 class="dropdown-item-title mb-1">
                                        ${notification.title}
                                    </h6>
                                    <p class="text-sm text-muted mb-0">${notification.message}</p>
                                    <div class="d-flex justify-content-between align-items-center mt-1">
                                        <small class="text-muted">${notification.email}</small>
                                        <small class="text-muted">${notification.time}</small>
                                    </div>
                                </div>
                            </div>
                        </a>
                        <div class="dropdown-divider"></div>
                    `;
                        });
                        notificationsList.innerHTML = html;

                        // Добавляем обработчики для пометки как прочитанного
                        document.querySelectorAll('.notification-item').forEach(item => {
                            item.addEventListener('click', function() {
                                const notificationId = this.getAttribute('data-id');
                                markAsRead(notificationId);
                            });
                        });
                    });
            }

            // Пометить как прочитанное
            function markAsRead(notificationId) {
                fetch(`/admin/notifications/${notificationId}/mark-read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                });
            }

            // Пометить все как прочитанные
            markAllReadBtn.addEventListener('click', function(e) {
                e.preventDefault();

                fetch('{{ route("admin.notifications.mark-all-read") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                }).then(() => {
                    loadNotificationCount();
                    loadNotifications();
                });
            });

            // Загружаем уведомления при открытии dropdown
            notificationDropdown.addEventListener('click', function() {
                loadNotifications();
            });

            // Автообновление каждые 30 секунд
            setInterval(loadNotificationCount, 30000);

            // Первоначальная загрузка
            loadNotificationCount();
        });
    </script>

    <style>

        /* Для AdminLTE если используется */
        .navbar-nav .notification-badge {
            top: 5px;
            right: 7px;
        }

        .notification-item:hover {
            background-color: #f8f9fa;
            text-decoration: none;
        }

        .dropdown-item-title {
            font-size: 0.9rem;
            font-weight: 600;
        }

        .text-sm {
            font-size: 0.8rem;
        }
    </style>
@endpush
