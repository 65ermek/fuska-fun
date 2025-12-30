<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\ChatOnlineStatusController;
use Illuminate\Support\Facades\Route;

Route::prefix('chat')->name('chat.')->group(function () {

    // === API МАРШРУТЫ ===
    Route::prefix('api')
        ->name('api.')
        ->middleware('chat.lastseen')      // ← ВОТ ЗДЕСЬ НУЖНО
        ->group(function () {

            // 🔥 ОСНОВНЫЕ ОПЕРАЦИИ С ЧАТАМИ
            Route::get('/conversations', [ChatController::class, 'getConversations'])->name('conversations');
            Route::post('/send-message', [ChatController::class, 'sendMessage'])->name('send-message');
            Route::get('/messages', [ChatController::class, 'getMessages'])->name('messages');
            Route::get('/check-messages', [ChatController::class, 'checkNewMessages'])->name('check-messages');
            Route::post('/create-chat', [ChatController::class, 'createChat'])->name('create-chat');

            // 🔥 АВТОРСКИЕ МАРШРУТЫ
            Route::get('/author-conversations', [ChatController::class, 'getAuthorConversations'])->name('author-conversations');
            Route::post('/create-author-chat', [ChatController::class, 'createAuthorChat'])->name('create-author-chat');
            Route::get('/check-author-auth', [ChatController::class, 'checkAuthorAuth'])->name('check-author-auth');

            // 🔥 ОПЕРАЦИИ С КОНКРЕТНЫМИ ЧАТ-КОМНАТАМИ
            Route::prefix('{chatRoom}')->group(function () {
                Route::get('/info', [ChatController::class, 'getChatInfoApi'])->name('info');
                Route::post('/close', [ChatController::class, 'closeChatApi'])->name('close');
            });

            // 🔥 ДОПОЛНИТЕЛЬНЫЕ API
            Route::get('/unread-count', [ChatController::class, 'getUnreadCountApi'])->name('unread-count');

            /*// 🔥 ONLINE-STATUS
            Route::post('/online', [ChatOnlineStatusController::class, 'markOnline'])->name('online');
            Route::post('/offline', [ChatOnlineStatusController::class, 'markOffline'])->name('offline');
            Route::get('/online-status', [ChatOnlineStatusController::class, 'getOnlineStatus'])->name('online-status');*/

            Route::post('/update-online-status', [ChatOnlineStatusController::class, 'updateOnlineStatus'])->name('update-online-status');
            Route::get('/get-online-status', [ChatOnlineStatusController::class, 'getOnlineStatusApi'])->name('get-online-status');
            Route::get('/get-all-statuses', [ChatOnlineStatusController::class, 'getAllStatuses'])->name('get-all-statuses');
            Route::get('/chat-statuses/{chatRoomId}', [ChatOnlineStatusController::class, 'getChatStatuses'])->name('chat-statuses');
        });
});

// 🔥 ПУБЛИЧНЫЕ МАРШРУТЫ для авторов
Route::prefix('chat/autor')
    ->name('chat.author.')
    ->middleware('chat.lastseen')       // ← ДЛЯ АВТОРОВ ТОЖЕ НУЖНО!
    ->group(function () {

        Route::get('/{token}/{job_id}', [ChatController::class, 'authorChat'])
            ->name('show')
            ->withoutMiddleware(['identifycustomer']);

        Route::post('/send', [ChatController::class, 'sendAuthorMessage'])->name('send');
    });
