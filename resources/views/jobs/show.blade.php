@extends('layouts.bazos')

@section('title', $job->title.' – Fuska.fun')

@section('content')
    <a href="{{ url()->previous() }}" class="small text-decoration-none">{!! __('messages.job_detail.back') !!}</a>

    <div class="bzs-detail card border-0 mt-2">
        <div class="card-body">
            <div class="listainzerat inzeratyflex">
                <div class="inzeratydetnadpis d-flex">
                    <h1 class="nadpisdetail">{{ $job->title }}</h1>
                    <span class="velikost10">
                         -
                        @if($job->top || $job->paid_at)
                            <span class="ztop mx-1">{{ __('messages.job_detail.top_badge') }}</span>
                        @endif
                        [{{ $job->created_at->format('j.n. Y') }}]
                    </span>
                </div>
                <div class="inzeratydetdel">
                    <a href="{{ route('jobs.manage', $job->slug) }}">{{ __('messages.job_detail.manage_ad') }}</a>
                </div>
            </div>

            @php
                $mainPhoto = $job->photos->sortBy('sort')->first();
            @endphp

            {{-- СЛАЙДЕР С ВЕРТИКАЛЬНЫМИ МИНИАТЮРАМИ СПРАВА --}}
            <div class="job-slider-wrapper mb-4">
                <div class="slider-with-vertical-thumbs">
                    {{-- Основной слайдер --}}
                    <div class="main-slider-container">
                        <div class="slider-for">
                            @foreach($job->photos->sortBy('sort') as $photo)
                                <div class="main-slide">
                                    <img src="{{ asset($photo->path) }}"
                                         class="img-fluid main-image"
                                         onclick="openFullscreenSlider({{ $loop->index }})">
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Вертикальные миниатюры --}}
                    @if($job->photos->count() > 1)
                        <div class="vertical-thumbs-container">
                            <div class="slider-nav-vertical">
                                @foreach($job->photos->sortBy('sort') as $photo)
                                    <div class="thumb-slide-vertical">
                                        <img src="{{ asset($photo->path) }}"
                                             class="img-fluid thumb-image-vertical">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="mb-3">
                <strong>{{ __('messages.job_detail.description') }}:</strong><br>
                {!! nl2br(e($job->description)) !!}
            </div>
        </div>
    </div>

    <hr class="my-4">

    <div class="job-meta d-flex flex-column flex-md-row gap-4">
        {{-- Блок: контакт и детали --}}
        <div class="job-contact flex-fill">
            <div class="mb-2"><strong>{{ __('messages.job_detail.name') }}:</strong> {{ $job->contact_name }}</div>

            <div class="mb-2">
                <strong>{{ __('messages.job_detail.phone') }}:</strong>
                <span class="teldetail" style="cursor:pointer" onclick="this.innerText='{{ $job->phone }}'">{{ __('messages.job_detail.phone_placeholder') }}</span>
            </div>

            <div class="mb-2">
                <strong>{{ __('messages.job_detail.location') }}:</strong>
                <img src="/images/png/maps.png" alt="Mapa" height="17">
                {{ $job->city }} – {{ $job->district }}
            </div>

            <div class="mb-2"><strong>{{ __('messages.job_detail.views') }}:</strong> {{ $job->views ?? 0 }} lidí</div>

            <div><strong>{{ __('messages.job_detail.salary') }}:</strong> <strong>{{ $job->price_label ?? __('messages.price_negotiable') }}</strong></div>
        </div>

        {{-- Блок: действия --}}
        <div class="job-actions flex-fill">
            <div class="d-flex align-items-start mb-2">
                <img src="/images/png/user.png" alt="Uživatel" height="17" class="me-2">
                <span>{{ __('messages.job_detail.user_ads') }}</span>
            </div>

            <div class="d-flex align-items-start mb-2">
                <img src="/images/png/favourite.png" alt="favourite" height="18" class="me-2">

                <form method="POST" action="{{ route('job-actions.toggle') }}">
                    @csrf
                    <input type="hidden" name="job_id" value="{{ $job->id }}">
                    <input type="hidden" name="action" value="favorite">
                    <button type="submit" class="btn btn-link p-0 m-0 align-baseline">
                        {{ $isFavorite ? __('messages.job_detail.favorite.remove') : __('messages.job_detail.favorite.add') }}
                    </button>
                </form>
            </div>
            <div class="d-flex align-items-start mb-2">
                <img src="/images/png/spam.png" alt="Spam" height="17" class="me-2">
                <a href="#"
                   class="text-danger report-link"
                   data-job-id="{{ $job->id }}"
                   data-report-type="report_spam">
                    {{ __('messages.job_detail.report.spam') }}
                </a>
            </div>

            <div class="d-flex align-items-start mb-2">
                <img src="/images/png/miscat.png" alt="miscat" height="17" class="me-2">
                <a href="#"
                   class="text-warning report-link"
                   data-job-id="{{ $job->id }}"
                   data-report-type="report_miscat">
                    {{ __('messages.job_detail.report.miscat') }}
                </a>
            </div>
            <div class="d-flex align-items-start mb-2">
                <img src="/images/png/print.png" alt="print" height="17" class="me-2">
                <a href="#" onclick="window.print()">{{ __('messages.job_detail.actions.print') }}</a>
            </div>

            <div class="d-flex align-items-start mb-2">
                <div class="me-2">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" class="text-primary">
                        <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                    </svg>
                </div>
                <a href="" class="text-decoration-none">
                    Пока пустой
                </a>
            </div>
            <div class="d-flex align-items-start mb-2">
                <img src="/images/png/mail.png" alt="Mail" height="17" class="me-2">
                <a href="#">{{ __('messages.job_detail.actions.recommend') }}</a>
            </div>

            <div class="d-flex align-items-start">
                <img src="/images/png/similar.png" alt="similar" height="17" class="me-2">
                <a href="#">{{ __('messages.job_detail.actions.similar') }}</a>
            </div>
        </div>
    </div>

    <div id="fuska-toast-container" class="mt-3" style="display: none;">
        <div class="alert alert-success alert-dismissible fade show" role="alert" id="fuska-toast-message">
            <span id="fuska-toast-text"></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('messages.job_detail.modal.close') }}"></button>
        </div>
    </div>

    {{-- КОМПОНЕНТ КОНТАКТНОЙ ФОРМЫ С ПРОВЕРКОЙ --}}
    @php
        $authorEmail = $job->email ?? ($job->user->email ?? '');
    @endphp

    {{-- 🔥 ОТЛАДОЧНАЯ ИНФОРМАЦИЯ --}}
    @if(app()->environment('local'))
        <div class="alert alert-warning mb-2 py-1">
            <small>
                <strong>Debug Contact Form:</strong>
                Job ID: {{ $job->id }},
                Author Email: {{ $authorEmail }},
                Author Name: {{ $job->contact_name }}
            </small>
        </div>
    @endif

    <x-contact-form
        :job-id="$job->id"
        :job-title="$job->title"
        :author-email="$authorEmail"
        :author-name="$job->contact_name"
    />

    <!-- Fullscreen Modal -->
    <div id="fullscreen-slider-modal" class="fullscreen-modal" style="display:none;">
        <button class="fullscreen-close-btn" onclick="closeFullscreenSlider()">
            ✕ {{ __('messages.job_detail.modal.close') }}
        </button>
        <div class="fullscreen-slider">
            @foreach($job->photos->sortBy('sort') as $photo)
                <div>
                    <img src="{{ asset($photo->path) }}"
                         class="fullscreen-image">
                </div>
            @endforeach
        </div>
    </div>

    <!-- Modal: Označit inzerát -->
    <div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content oranzovy">
                <div class="modal-header">
                    <h5 class="modal-title" id="reportModalLabel">{{ __('messages.job_detail.modal.report_title') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('messages.job_detail.modal.close') }}"></button>
                </div>
                <form method="POST" action="{{ route('job-actions.report') }}">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="job_id" id="reportJobId" value="">
                        <input type="hidden" name="report_type" id="reportType" value="">

                        <label for="note">{{ __('messages.job_detail.modal.note') }}</label>
                        <textarea name="note" id="note" class="form-control" rows="3" placeholder="{{ __('messages.job_detail.modal.note_placeholder') }}"></textarea>

                        <p class="small mt-3">
                            {!! __('messages.job_detail.modal.report_help', [
                                'link' => '<a href="' . e(route('terms')) . '"
                                           target="_blank"
                                           rel="noopener noreferrer"
                                           class="text-danger text-decoration-underline">
                                           ' . e(__('messages.job_detail.modal.terms_link')) . '
                                           </a>'
                            ]) !!}
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">{{ __('messages.job_detail.modal.confirm') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if(session('auto_open_chat') && session('author_chat_token'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(() => {
                    if (window.globalChat) {
                        window.globalChat.openNewChat(
                            {{ session('author_job_id') }},
                            'Chat s kandidátem',
                            '{{ session('author_candidate_email') }}',
                            'Majitel inzerátu',
                            true
                        );
                        window.globalChat.authorToken = '{{ session('author_chat_token') }}';
                    }
                }, 1000);
            });
        </script>
    @endif
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // СЛАЙДЕР С ВЕРТИКАЛЬНЫМИ МИНИАТЮРАМИ
            @if($job->photos->count() > 0)
            // Основной слайдер
            $('.slider-for').slick({
                slidesToShow: 1,
                slidesToScroll: 1,
                arrows: false,
                fade: true,
                asNavFor: '.slider-nav-vertical',
                infinite: false,
                adaptiveHeight: false,
                prevArrow: '<button type="button" class="slick-prev"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg></button>',
                nextArrow: '<button type="button" class="slick-next"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg></button>'
            });

            // Вертикальный слайдер миниатюр (только если больше 1 фото)
            @if($job->photos->count() > 1)
            $('.slider-nav-vertical').slick({
                slidesToShow: Math.min(5, {{ $job->photos->count() }}),
                slidesToScroll: 1,
                asNavFor: '.slider-for',
                dots: false,
                arrows: true,
                vertical: true, // 🔥 ВКЛЮЧАЕМ ВЕРТИКАЛЬНЫЙ РЕЖИМ
                verticalSwiping: true,
                focusOnSelect: true,
                infinite: false,
                centerMode: false,
                prevArrow: '<button type="button" class="slick-prev"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 15l-6-6-6 6"/></svg></button>',
                nextArrow: '<button type="button" class="slick-next"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg></button>',
                responsive: [
                    {
                        breakpoint: 768,
                        settings: {
                            vertical: false, // 🔥 НА МОБИЛЬНЫХ ДЕЛАЕМ ГОРИЗОНТАЛЬНЫМИ
                            slidesToShow: Math.min(4, {{ $job->photos->count() }}),
                            arrows: true
                        }
                    }
                ]
            });
            @endif
            @endif
            // ОБРАБОТЧИКИ ДЛЯ РЕПОРТОВ
            document.querySelectorAll('.report-link').forEach(function (el) {
                el.addEventListener('click', function (e) {
                    e.preventDefault();
                    const jobId = this.getAttribute('data-job-id');
                    const reportType = this.getAttribute('data-report-type');

                    document.getElementById('reportJobId').value = jobId;
                    document.getElementById('reportType').value = reportType;
                    document.getElementById('note').value = '';

                    const modal = new bootstrap.Modal(document.getElementById('reportModal'));
                    modal.show();
                });
            });

            // ОБРАБОТЧИКИ ДЛЯ ИЗБРАННОГО
            document.querySelectorAll('.toggle-action').forEach(link => {
                link.addEventListener('click', function (e) {
                    e.preventDefault();
                    const jobId = this.dataset.id;
                    const action = this.dataset.action;

                    fetch('/job-actions/toggle', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ job_id: jobId, action })
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 'added') {
                                this.textContent = '{{ __("messages.job_detail.favorite.remove") }}';
                            } else {
                                this.textContent = '{{ __("messages.job_detail.favorite.add") }}';
                            }
                        });
                });
            });
        });

        // ФУНКЦИИ ДЛЯ ПОЛНОЭКРАННОГО РЕЖИМА
        function openFullscreenSlider(slideIndex) {
            const modal = document.getElementById('fullscreen-slider-modal');

            // Инициализируем полноэкранный слайдер только один раз
            if (!$('.fullscreen-slider').hasClass('slick-initialized')) {
                $('.fullscreen-slider').slick({
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    arrows: true,
                    fade: false,
                    infinite: true, // 🔥 ВКЛЮЧАЕМ БЕСКОНЕЧНУЮ ЦИКЛИЧНОСТЬ
                    speed: 300,
                    initialSlide: slideIndex,
                    dots: true,
                    prevArrow: '<button type="button" class="slick-prev"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg></button>',
                    nextArrow: '<button type="button" class="slick-next"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg></button>',
                    // 🔥 ДОПОЛНИТЕЛЬНЫЕ НАСТРОЙКИ ДЛЯ ЛУЧШЕГО UX
                    swipe: true,
                    touchMove: true,
                    waitForAnimate: true,
                    responsive: [
                        {
                            breakpoint: 768,
                            settings: {
                                arrows: true,
                                dots: true
                            }
                        }
                    ]
                });
            } else {
                // Если уже инициализирован, просто переходим к нужному слайду
                $('.fullscreen-slider').slick('slickGoTo', slideIndex);
            }

            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';

            // Фокус на кнопку закрытия для доступности
            setTimeout(() => {
                document.querySelector('.fullscreen-close-btn').focus();
            }, 100);
        }

        function closeFullscreenSlider() {
            const modal = document.getElementById('fullscreen-slider-modal');
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        // Закрытие по ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && document.getElementById('fullscreen-slider-modal').style.display === 'flex') {
                closeFullscreenSlider();
            }
        });

        // Закрытие по клику на фон
        document.getElementById('fullscreen-slider-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeFullscreenSlider();
            }
        });

        function showFuskaAlert(message, type = 'success') {
            const container = document.getElementById('fuska-toast-container');
            const messageBox = document.getElementById('fuska-toast-message');
            const text = document.getElementById('fuska-toast-text');

            text.textContent = message;
            messageBox.classList.remove('alert-success', 'alert-danger', 'alert-warning');
            messageBox.classList.add('alert-' + type);
            container.style.display = 'block';
        }
    </script>
@endpush
