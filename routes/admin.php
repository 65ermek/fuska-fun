<?php
// routes/admin.php

use App\Http\Controllers\Admin\ContactRequestController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\JobCategoryController;
use App\Http\Controllers\Admin\JobController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\PhotoCleanupController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {

    // Дашборд
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Профиль
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::delete('/profile/avatar', [ProfileController::class, 'removeAvatar'])->name('profile.avatar.remove');
    // Маршруты для управления вакансиями
    Route::prefix('jobs')->name('jobs.')->middleware(['auth'])->group(function () {
        Route::get('/', [JobController::class, 'index'])->name('index');
        Route::get('/{job}', [JobController::class, 'show'])->name('show');
        Route::get('/{job}/edit', [JobController::class, 'edit'])->name('edit');
        Route::put('/{job}', [JobController::class, 'update'])->name('update');
        Route::delete('/{job}', [JobController::class, 'destroy'])->name('destroy');
        Route::patch('/{job}/status', [JobController::class, 'updateStatus'])->name('update-status');
    });
    // Маршруты для очистки фото
    Route::get('photos/cleanup', [PhotoCleanupController::class, 'index'])->name('photos.cleanup');
    Route::delete('photos/cleanup/folder/{folder}', [PhotoCleanupController::class, 'destroyFolder'])->name('photos.cleanup.folder');
    Route::delete('photos/cleanup/all', [PhotoCleanupController::class, 'destroyAll'])->name('photos.cleanup.all');
    // Маршруты для контроляя контактов
    Route::resource('contact-requests', ContactRequestController::class)->only(['index', 'show', 'destroy']);
    Route::get('contact-requests/stats', [ContactRequestController::class, 'stats'])->name('contact-requests.stats');
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/count', [NotificationController::class, 'count'])->name('count');
        Route::get('/list', [NotificationController::class, 'list'])->name('list');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::post('/{contactRequest}/mark-read', [NotificationController::class, 'markAsRead'])->name('mark-read');
    });
    // Категории вакансий - для админов и менеджеров
    Route::prefix('job-categories')->name('job-categories.')->middleware(['auth'])->group(function () {
        Route::get('/', [JobCategoryController::class, 'index'])->name('index');
        Route::get('/create', [JobCategoryController::class, 'create'])->name('create');
        Route::post('/', [JobCategoryController::class, 'store'])->name('store');
        Route::get('/{jobCategory}', [JobCategoryController::class, 'show'])->name('show');
        Route::get('/{jobCategory}/edit', [JobCategoryController::class, 'edit'])->name('edit');
        Route::put('/{jobCategory}', [JobCategoryController::class, 'update'])->name('update');
        Route::patch('/{jobCategory}', [JobCategoryController::class, 'update']);
        Route::delete('/{jobCategory}', [JobCategoryController::class, 'destroy'])->name('destroy');
    });

    // Маршруты пользователей - только для админов
    Route::prefix('users')->name('users.')->middleware('admin')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}', [UserController::class, 'show'])->name('show'); // Исправлено на {user}
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit'); // Исправлено на {user}
        Route::put('/{user}', [UserController::class, 'update'])->name('update'); // Исправлено на {user}
        Route::patch('/{user}', [UserController::class, 'update']); // Добавляем PATCH
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy'); // Исправлено на {user}
    });
    // Customers (клиенты / кандидаты)
    Route::prefix('customers')
        ->name('customers.')
        ->middleware('admin')
        ->group(function () {

            Route::get('/', [CustomerController::class, 'index'])->name('index');
            Route::get('/{customer}', [CustomerController::class, 'show'])->name('show');
            Route::delete('/{customer}', [CustomerController::class, 'destroy'])->name('destroy');

            // опционально позже
            // Route::get('/{customer}/edit', ...)
            // Route::put('/{customer}', ...)
        });

});

// Маршрут для переключения языка (вне группы admin)
Route::get('language/{lang}', [LanguageController::class, 'switch'])
    ->name('language.switch');
