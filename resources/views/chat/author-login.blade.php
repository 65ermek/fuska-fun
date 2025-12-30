{{-- resources/views/chat/author-login.blade.php --}}
@extends('layouts.chat')

@section('title', 'Вход для авторов')
@section('header', 'Вход для авторов')

@section('content')
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6">
        <div class="text-center mb-6">
            <i class="fas fa-user-tie text-4xl text-blue-500 mb-4"></i>
            <h2 class="text-2xl font-bold text-gray-900">Вход для работодателей</h2>
            <p class="text-gray-600 mt-2">Введите email и токен от вашего объявления</p>
        </div>

        <form method="POST" action="{{ route('chat.author.login.post') }}">
            @csrf

            <div class="mb-4">
                <label for="author_email" class="block text-sm font-medium text-gray-700 mb-2">
                    Email указанный в объявлении
                </label>
                <input
                    type="email"
                    id="author_email"
                    name="author_email"
                    value="{{ old('author_email') }}"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    placeholder="vasya@example.com"
                >
                @error('author_email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="token" class="block text-sm font-medium text-gray-700 mb-2">
                    Токен редактирования
                </label>
                <input
                    type="text"
                    id="token"
                    name="token"
                    value="{{ old('token') }}"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Введите токен из письма"
                >
                @error('token')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button
                type="submit"
                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
                <i class="fas fa-sign-in-alt mr-2"></i>
                Войти в чаты
            </button>
        </form>

        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
            <h3 class="text-sm font-medium text-gray-900 mb-2">Где найти токен?</h3>
            <p class="text-sm text-gray-600">
                Токен был отправлен вам на email при создании объявления.
                Также его можно найти в письме с ссылкой для редактирования объявления.
            </p>
        </div>
    </div>
@endsection
