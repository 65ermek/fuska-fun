<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title','Fuska.fun')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Стили -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="{{ asset('css/fuska.css') }}?v={{ time() }}" rel="stylesheet">
    <link href="{{ asset('css/chat.css') }}?v={{ time() }}" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">
<div id="loadingOverlay" class="loading-overlay" style="display: none;">
    <div class="spinner"></div>
    <div class="loading-text">Загрузка...</div>
</div>
<x-header />

@if(session('ok'))
    <div class="container mt-3">
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('ok') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
@endif

<main class="container my-3">
    <div class="row">
        <div class="col-12 col-lg-3 mb-3 mb-lg-0">
            <x-sidebar-categories :categories="$categories ?? null" :active="request('category')" />
        </div>
        <div class="col-12 col-lg-9 flex-grow-1">
            @yield('content')
        </div>
    </div>
</main>

<!-- Компонент чата -->
<x-chat />
<!-- Компонент системы -->
<x-system-indicator />

{{-- footer --}}
@include('components.footer')

{{-- кнопка наверх --}}
<button id="toTopBtn" class="to-top-btn" type="button" aria-label="Zpět nahoru">↑</button>

<!-- Скрипты -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>

@stack('scripts')

<!-- Cookie баннер -->
@include('components.cookie-banner')
<script>
    // Глобальные функции для управления спиннером
    window.showLoading = function(text = 'Загрузка...') {
        const overlay = document.getElementById('loadingOverlay');
        const textElement = overlay.querySelector('.loading-text');

        if (textElement) {
            textElement.textContent = text;
        }

        overlay.style.display = 'flex';
        document.body.style.overflow = 'hidden'; // Блокируем прокрутку
    };

    window.hideLoading = function() {
        const overlay = document.getElementById('loadingOverlay');
        overlay.style.display = 'none';
        document.body.style.overflow = ''; // Разблокируем прокрутку
    };
</script>
{{-- в layouts/bazos.blade.php перед закрывающим </body> --}}
<script>
    console.log('🐛 Customer Debug:', {
        email: '{{ session('customer_email') }}',
        id: '{{ session('customer_id') }}',
        name: '{{ session('customer_name') }}',
        source: '{{ session('customer_source') }}'
    });
</script>
</body>
</html>
