<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactRequest;
use Illuminate\Http\Request;

class ContactRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ContactRequest::with('job');

        // Применяем фильтры через scopes
        if ($request->filled('status')) {
            $query->status($request->status);
        }

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('date_from') || $request->filled('date_to')) {
            $query->dateRange($request->date_from, $request->date_to);
        }

        $contactRequests = $query->orderBy('id', 'desc')->paginate(10);

        // Статистика
        $stats = [
            'total' => ContactRequest::count(),
            'sent' => ContactRequest::status('sent')->count(),
            'failed' => ContactRequest::status('failed')->count(),
            'pending' => ContactRequest::status('pending')->count(),
            'today' => ContactRequest::whereDate('created_at', today())->count(),
            'week' => ContactRequest::where('created_at', '>=', now()->subWeek())->count(),
        ];

        return view('admin.contact_requests.index', compact('contactRequests', 'stats'));
    }

    /**
     * Display the specified resource.
     */
// В методе show добавляем пометку как прочитанное
    public function show(ContactRequest $contactRequest)
    {
        // Помечаем как прочитанное при просмотре
        if (!$contactRequest->is_read) {
            $contactRequest->markAsRead();
        }

        $contactRequest->load('job');
        return view('admin.contact_requests.show', compact('contactRequest'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContactRequest $contactRequest)
    {
        $contactRequest->delete();

        return redirect()->route('admin.contact_requests.index')
            ->with('success', 'Kontaktní požadavek byl úspěšně smazán.');
    }

    /**
     * Custom method for statistics
     */
    public function stats()
    {
        // Статистика по дням за последний месяц
        $dailyStats = ContactRequest::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subMonth())
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Топ объявлений по количеству контактов
        $jobStats = ContactRequest::with('job')
            ->selectRaw('job_id, COUNT(*) as count')
            ->groupBy('job_id')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        return view('admin.contact_requests.stats', compact('dailyStats', 'jobStats'));
    }
}
