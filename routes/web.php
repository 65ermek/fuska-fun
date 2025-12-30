<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ChatOnlineStatusController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\JobActionController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\JobPhotoController;
use App\Http\Controllers\TopPaymentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'set.locale'])->group(function () {
    Route::get('/debug-session', function() {
        return [
            'session_id' => session()->getId(),
            'session_data' => session()->all(),
            'is_author' => session('author_logged_in', false),
            'author_email' => session('user_email'),
            'customer_email' => session('customer_email'),
            'expected_author' => 'tanatarltd@gmail.com'
        ];
    });
    // Главная — список объявлений
    Route::get('/', [JobController::class, 'index'])->name('jobs.index');
    Route::get('/privacy', function () { return view('privacy');})->name('privacy');
    Route::get('/terms', function () { return view('terms');})->name('terms');

    Route::get('/my-conversations', [ChatController::class, 'getMyConversations'])->name('my-conversations');

    // 🔥 УБРАЛ дублирующий роут customer.authenticate
    Route::post('/customer/authenticate', [CustomerController::class, 'authenticate'])->name('customer.authenticate');

    // Создание объявления
    Route::get('/jobs/create', [JobController::class, 'create'])->name('jobs.create');
    Route::post('/jobs', [JobController::class, 'store'])->name('jobs.store');

    // Анонимные действия по ID
    Route::post('/jobs/{job}/report', [JobController::class, 'report'])->name('jobs.report');
    Route::post('/jobs/{slug}/prolong', [JobController::class, 'prolong'])->name('jobs.prolong');
    Route::post('/jobs/request-password', [JobController::class, 'requestPassword'])->name('jobs.request_password');

    // Управление по slug (без регистрации)
    Route::get('/manage/{slug}', [JobController::class, 'manage'])->name('jobs.manage');
    Route::post('/manage/{slug}', [JobController::class, 'manageAction'])->name('jobs.manage_action');

    Route::post('/platba-topovani', [TopPaymentController::class, 'create']);
    Route::get('/platba-topovani/{code}', [TopPaymentController::class, 'show']);


    // Редактирование по slug
    Route::get('/edit/{slug}', [JobController::class, 'editBySlug'])->name('jobs.edit');
    Route::put('/edit/{slug}', [JobController::class, 'update'])->name('jobs.update');

    // Удаление по slug
    Route::delete('/jobs/{slug}', [JobController::class, 'destroy'])->name('jobs.destroy');


    // Удаление фото
    Route::delete('/photos/{id}', [JobPhotoController::class, 'destroy'])->name('photos.destroy');

    // Мои объявления (по cookie и email)
    Route::get('/moje-inzeraty', [JobController::class, 'myAds'])->name('jobs.my');
    Route::post('/moje-inzeraty/vypsat', [JobController::class, 'recoverAds'])->name('jobs.recover');

    // 🔥 ОСНОВНОЙ МЕТОД ОТПРАВКИ СООБЩЕНИЙ (используем ChatController вместо JobMessageController)
    Route::post('/job-message', [ChatController::class, 'send'])->name('job.message');

    // POST-запрос на переключение действия (например, избранное)
    Route::get('/favorites', [JobActionController::class, 'favorites'])->name('jobs.favorites');
    Route::post('/job-actions/toggle', [JobActionController::class, 'toggle'])->name('job-actions.toggle');
    Route::post('/job-actions/report', [JobActionController::class, 'report'])->name('job-actions.report');
    Route::get('/jobs/{slug}', [JobActionController::class, 'show'])->name('jobs.show');

    Route::get('/__scheduler', function () {
        Artisan::call('schedule:run');
        return 'Schedule run executed';
    });

    // Переключение языка
    Route::post('/set-locale', function (\Illuminate\Http\Request $r) {
        $supported = config('locales.supported', ['cs']);
        $loc = $r->input('locale', 'cs');
        if (!in_array($loc, $supported, true)) {
            $loc = config('locales.default', 'cs');
        }
        session(['locale' => $loc]);
        app()->setLocale($loc);
        return back();
    })->name('set-locale');
});

// Admin routes
require __DIR__.'/admin.php';

// 🔥 ПОДКЛЮЧАЕМ ВСЕ РОУТЫ ЧАТА ИЗ ОТДЕЛЬНОГО ФАЙЛА
require __DIR__.'/chat.php';

// Auth routes
Route::get('/admin', function () {
    return redirect()->route('login');
});

Route::get('/admin/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/admin/login', [LoginController::class, 'login']);
Route::post('/admin/logout', [LoginController::class, 'logout'])->name('logout');

// 🔥 ФИКС ДЛЯ КАНДИДАТОВ (оставляем если используется)
Route::get('/chat/candidate-fix/{chatRoomId}', function($chatRoomId) {
    $chatRoom = \App\Models\ChatRoom::with('job')->findOrFail($chatRoomId);

    session([
        'user_email' => $chatRoom->candidate_email,
        'candidate_token' => $chatRoom->candidate_token,
        'user_name' => $chatRoom->candidate_name,
        'author_token' => null
    ]);

    \Log::info('Candidate session fixed', [
        'chat_room_id' => $chatRoomId,
        'user_email' => $chatRoom->candidate_email,
        'candidate_token' => $chatRoom->candidate_token
    ]);

    return redirect()->route('chat.room', $chatRoomId);
});

Route::get('/online-status', [ChatOnlineStatusController::class, 'getOnlineStatus']);
Route::post('/online', [ChatOnlineStatusController::class, 'markOnline']);
Route::post('/offline', [ChatOnlineStatusController::class, 'markOffline']);

Route::post('/platba-topovani/{payment}/zaplatil-jsem', [TopPaymentController::class, 'markAsPaid'])->name('top-payment.paid');
Route::get('/top-payments/waiting', function () {return 'TODO: seznam plateb';})->name('admin.top-payments.waiting');

