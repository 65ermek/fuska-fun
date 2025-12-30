@php
    $shouldShowCookieBanner = !request()->cookie('fuska_cookie_accepted') && !app()->runningInConsole();
@endphp

@if($shouldShowCookieBanner)
    <div id="cookie-banner" style="
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: #f8f9fa;
        border-top: 1px solid #dee2e6;
        padding: 20px;
        z-index: 1050;
        display: none;
        align-items: center;
        justify-content: space-between;
        font-size: 14px;
        box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
        backdrop-filter: blur(10px);
    ">
        <div style="flex: 1; margin-right: 20px;">
            <strong>{{ __('messages.cookies.title') }}</strong>
            <span class="text-muted">|</span>
            {{ __('messages.cookies.message') }}
            <a href="{{ route('privacy') }}" class="text-primary ml-1" style="text-decoration: underline;">
                {{ __('messages.cookies.learn_more') }}
            </a>
        </div>
        <div>
            <button id="accept-cookies" class="btn btn-success btn-sm" style="min-width: 100px;">
                {{ __('messages.cookies.accept_button') }}
            </button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cookieBanner = document.getElementById('cookie-banner');
            const acceptButton = document.getElementById('accept-cookies');

            if (!cookieBanner || !acceptButton) return;

            // Если cookie уже есть → баннер не показываем
            if (document.cookie.includes('fuska_cookie_accepted=1')) {
                cookieBanner.style.display = 'none';
                return;
            }

            // Показать баннер через секунду
            setTimeout(() => {
                cookieBanner.style.display = 'flex';
            }, 1000);

            acceptButton.addEventListener('click', function() {
                cookieBanner.style.display = 'none';

                // Ставим cookie на 180 дней
                const date = new Date();
                date.setTime(date.getTime() + (180 * 24 * 60 * 60 * 1000));
                const expires = "expires=" + date.toUTCString();

                document.cookie = "fuska_cookie_accepted=1; path=/; " + expires + "; SameSite=Lax";

                // На случай, если сервер решит скрывать на sessionStorage
                sessionStorage.setItem('cookieBannerHidden', 'true');
            });
        });
    </script>


    <style>
        #cookie-banner {
            animation: slideUp 0.3s ease-out;
        }

        @keyframes slideUp {
            from {
                transform: translateY(100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @media (max-width: 768px) {
            #cookie-banner {
                flex-direction: column;
                text-align: center;
                padding: 15px;
            }

            #cookie-banner > div:first-child {
                margin-right: 0;
                margin-bottom: 10px;
            }
        }
    </style>
@endif
