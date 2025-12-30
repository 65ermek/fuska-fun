<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactRequest;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Получить количество непрочитанных уведомлений
     */
    public function count()
    {
        $unreadCount = ContactRequest::unread()->count();

        return response()->json([
            'count' => $unreadCount,
            'html' => $unreadCount > 0 ?
                '<span class="badge badge-warning navbar-badge">'.$unreadCount.'</span>' :
                ''
        ]);
    }

    /**
     * Получить список непрочитанных уведомлений
     */
    public function list()
    {
        $notifications = ContactRequest::unread()
            ->with('job')
            ->latest()
            ->limit(10)
            ->get()
            ->map(function($request) {
                return [
                    'id' => $request->id,
                    'title' => 'Новое сообщение: ' . Str::limit($request->job->title, 30),
                    'message' => Str::limit($request->message, 50),
                    'time' => $request->created_at->diffForHumans(),
                    'url' => route('admin.contact-requests.show', $request),
                    'email' => $request->email,
                ];
            });

        return response()->json($notifications);
    }

    /**
     * Пометить все как прочитанные
     */
    public function markAllAsRead()
    {
        ContactRequest::unread()->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Пометить одно как прочитанное
     */
    public function markAsRead(ContactRequest $contactRequest)
    {
        $contactRequest->markAsRead();

        return response()->json(['success' => true]);
    }
}
