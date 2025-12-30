<?php
// app/Http/Controllers/JobActionController.php
namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\JobAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JobActionController extends Controller
{
    public function show($slug, Request $request)
    {
        $job = Job::where('slug', $slug)->first();

        if (!$job) {
            abort(404);
        }

        abort_if($job->status !== 'published', 404);

        // Подсчет просмотров
        $viewed = session('viewed_jobs', []);

        if (!in_array($job->id, $viewed)) {
            $job->increment('views');
            $viewed[] = $job->id;
            $viewed = array_slice($viewed, -50);
            session(['viewed_jobs' => $viewed]);
        }

        // 1. Получаем email из сессии или cookie
        $email = $job->email;

        // 2. Записываем в сессию, если надо
        if ($email && !session()->has('email')) {
            session(['email' => $email]);
        }

        // 3. Проверяем избранное
        $isFavorite = false;
        $email = $job->email;
        if ($email) {
            $isFavorite = JobAction::where('email', $email)
                ->where('job_id', $job->id)
                ->where('action', 'favorite')
                ->exists();
        }

        return view('jobs.show', [
            'job' => $job->load('category', 'photos'),
            'isFavorite' => $isFavorite,
        ]);
    }
    public function toggle(Request $request)
    {
        $jobId = $request->input('job_id');
        $action = $request->input('action');
        $email = session('email');

        if (!$email) {
            return redirect()->back()->with('error', __('messages.job_actions.not_logged_in'));
        }

        $existing = JobAction::where('email', $email)
            ->where('job_id', $jobId)
            ->where('action', $action)
            ->first();

        if ($existing) {
            $existing->delete();
        } else {
            JobAction::create([
                'email'   => $email,
                'job_id'  => $jobId,
                'action'  => $action,
                'ip'      => $request->ip(), // ← добавляем IP
            ]);
        }

        return redirect()->back()->with('ok', __('messages.job_actions.action_success'));
    }

    public function favorites()
    {
        $email = session('email') ?? request()->cookie('fuska_email');

        if (!$email) {
            return redirect()->route('jobs.index')->with('error', __('messages.job_actions.not_logged_in'));
        }

        $favoriteJobIds = JobAction::where('email', $email)
            ->where('action', 'favorite')
            ->pluck('job_id');

        $favorites = Job::whereIn('id', $favoriteJobIds)
            ->with(['photos', 'category'])
            ->where('status', 'published')
            ->orderByDesc('created_at')
            ->get();

        return view('jobs.favorites', compact('favorites'));
    }

    public function report(Request $request)
    {
        $request->validate([
            'job_id' => 'required|exists:jobs,id',
            'report_type' => 'required|string',
            'note' => 'nullable|string|max:1000',
        ]);

        $email = session('email') ?? null;

        // Проверка на дублирующий репорт
        $exists = JobAction::where('email', $email)
            ->where('job_id', $request->job_id)
            ->where('action', $request->report_type)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('info', __('messages.job_actions.already_reported'));
        }

        JobAction::create([
            'email' => $email,
            'job_id' => $request->job_id,
            'action' => $request->report_type,
            'meta' => json_encode(['note' => $request->note]),
            'ip' => $request->ip(),
        ]);

        return redirect()->back()->with('ok', __('messages.job_actions.report_thanks'));
    }
}
