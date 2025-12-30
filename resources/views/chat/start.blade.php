{{-- resources/views/chat/start.blade.php --}}
@extends('layouts.chat') {{-- или ваш основной layout --}}

@section('title', 'Начать чат - ' . $job->title)

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-comments me-2"></i>
                            Начать чат
                        </h4>
                    </div>
                    <div class="card-body">
                        <!-- Информация об объявлении -->
                        <div class="alert alert-info">
                            <h5 class="alert-heading">{{ $job->title }}</h5>
                            <p class="mb-1"><strong>Город:</strong> {{ $job->city }}</p>
                            <p class="mb-0"><strong>Автор:</strong> {{ $job->contact_name }}</p>
                        </div>

                        <!-- Форма начала чата -->
                        <form method="POST" action="{{ route('chat.start', $job->id) }}">
                            @csrf

                            <div class="mb-3">
                                <label for="candidate_name" class="form-label">
                                    <strong>Ваше имя *</strong>
                                </label>
                                <input
                                    type="text"
                                    class="form-control @error('candidate_name') is-invalid @enderror"
                                    id="candidate_name"
                                    name="candidate_name"
                                    value="{{ old('candidate_name') }}"
                                    placeholder="Введите ваше имя"
                                    required
                                >
                                @error('candidate_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="candidate_email" class="form-label">
                                    <strong>Ваш email *</strong>
                                </label>
                                <input
                                    type="email"
                                    class="form-control @error('candidate_email') is-invalid @enderror"
                                    id="candidate_email"
                                    name="candidate_email"
                                    value="{{ old('candidate_email') }}"
                                    placeholder="your@email.com"
                                    required
                                >
                                @error('candidate_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    На этот email будут приходить уведомления о новых сообщениях
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="message" class="form-label">
                                    <strong>Первое сообщение (необязательно)</strong>
                                </label>
                                <textarea
                                    class="form-control @error('message') is-invalid @enderror"
                                    id="message"
                                    name="message"
                                    rows="3"
                                    placeholder="Напишите ваше первое сообщение автору объявления..."
                                >{{ old('message') }}</textarea>
                                @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i>
                                    Начать общение
                                </button>
                                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>
                                    Назад к объявлению
                                </a>
                            </div>
                        </form>

                        <!-- Информация о чате -->
                        <div class="mt-4 p-3 bg-light rounded">
                            <h6 class="mb-2"><i class="fas fa-info-circle me-2 text-primary"></i>Как работает чат?</h6>
                            <ul class="list-unstyled small mb-0">
                                <li><i class="fas fa-check text-success me-2"></i>Общайтесь напрямую с автором объявления</li>
                                <li><i class="fas fa-check text-success me-2"></i>Получайте уведомления на email</li>
                                <li><i class="fas fa-check text-success me-2"></i>Доступ к истории переписки</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Автофокус на поле имени
            document.getElementById('candidate_name')?.focus();
        });
    </script>
@endsection
