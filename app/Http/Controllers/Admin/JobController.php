<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JobCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $query = Job::with(['category', 'photos'])
            ->orderBy('top', 'desc')  // Сначала TOP объявления
            ->orderBy('created_at', 'desc')  // Потом новые
            ->orderBy('id', 'desc');  // На всякий случай по ID
        // Поиск по названию, описанию, городу
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('contact_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Обрабатываем оба варианта имени
        $category = $request->category_desktop ?? $request->category_mobile ?? $request->category;

        if ($category) {
            $query->whereHas('category', function($q) use ($category) {
                $q->where('slug', $category);
            });
        }

        // ... остальные фильтры без изменений

        $jobs = $query->paginate(20);
        $categories = JobCategory::all();

        $statuses = [
            'active' => 'Active',
            'pending' => 'Pending',
            'rejected' => 'Rejected',
            'expired' => 'Expired'
        ];

        return view('admin.jobs.index', compact('jobs', 'categories', 'statuses'));
    }

    public function show(Job $job)
    {
        $job->load(['category', 'photos', 'user']);
        return view('admin.jobs.show', compact('job'));
    }

    public function edit(Job $job)
    {
        $categories = JobCategory::all();
        $job->load('photos');

        return view('admin.jobs.edit', compact('job', 'categories'));
    }

    public function update(Request $request, Job $job)
    {
        try {
            $request->validate([
                'job_category_id' => 'required|exists:job_categories,id',
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'city' => 'required|string|max:255',
                'district' => 'nullable|string|max:255',
                'pay_type' => 'required|in:per_job,per_hour',
                'price' => 'nullable|numeric|min:0',
                'price_negotiable' => 'boolean',
                'contact_name' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:255',
                'status' => 'required|in:published,pending,hidden',
                'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
                'existing_photos' => 'nullable|string',
                'removed_photos' => 'nullable|string',
                'photo_order' => 'nullable|string',
            ]);

            // Обновляем основные данные
            $jobData = $request->except(['photos', 'existing_photos', 'removed_photos', 'photo_order', '_token', '_method']);
            $job->update($jobData);

            // Обработка существующих фотографий (удаление и сортировка)
            $this->processExistingPhotos($request, $job);

            // Обработка новых фотографий
            $this->processNewPhotos($request, $job);

            // Перезагружаем отношения
            $job->load('photos');

            return redirect()->route('admin.jobs.show', $job)
                ->with('success', __('admin.job_updated_successfully'));

        } catch (\Exception $e) {
            \Log::error('Job update error', [
                'job_id' => $job->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', __('admin.job_update_error'))
                ->withInput();
        }
    }

    /**
     * Обработка существующих фотографий (удаление и сортировка)
     */
    private function processExistingPhotos(Request $request, Job $job)
    {
        $existingPhotosInput = $request->input('existing_photos', '');
        $removedPhotosInput = $request->input('removed_photos', '');
        $photoOrderInput = $request->input('photo_order', '');

        // Обработка удаленных фотографий
        if (!empty($removedPhotosInput)) {
            $removedPhotoIds = explode(',', $removedPhotosInput);
            $removedPhotoIds = array_filter($removedPhotoIds); // Убираем пустые значения
            foreach ($removedPhotoIds as $photoId) {
                $photoId = (int)$photoId;
                if ($photoId > 0) {
                    $photo = $job->photos()->where('id', $photoId)->first();
                    if ($photo) {
                        // Удаляем файл
                        if (file_exists(public_path($photo->path))) {
                            unlink(public_path($photo->path));
                        }
                        // Удаляем запись из базы
                        $photo->delete();
                    }
                }
            }
        }

        // Обработка сортировки фотографий
        if (!empty($photoOrderInput)) {
            $orderedIds = explode(',', $photoOrderInput);
            $orderedIds = array_filter($orderedIds); // Убираем пустые значения
            foreach ($orderedIds as $sort => $photoId) {
                $photoId = (int) $photoId;
                if ($photoId > 0) {
                    $photo = $job->photos()->where('id', $photoId)->first();
                    if ($photo) {
                        $photo->update(['sort' => $sort]);
                    }
                }
            }
        }
    }
    /**
     * Обработка загрузки новых фотографий
     */
    private function processNewPhotos(Request $request, Job $job)
    {
        if (!$request->hasFile('photos')) {
            return;
        }

        $photos = $request->file('photos');

        // Начинаем с текущего количества фото
        $currentCount = $job->photos()->count();

        // Создаем папку для конкретного job если нужно
        $photoPath = 'images/jobs/' . $job->id; // Папка с ID job
        $fullPath = public_path($photoPath);

        // Создаем директорию если не существует
        if (!file_exists($fullPath)) {
            mkdir($fullPath, 0755, true);
        }

        foreach ($photos as $index => $photo) {
            if (!$photo->isValid()) {
                continue;
            }

            // Генерируем уникальное имя файла
            $extension = $photo->getClientOriginalExtension();
            $photoName = 'job_' . $job->id . '_' . time() . '_' . uniqid() . '.' . $extension;

            // Сохраняем изображение
            if ($photo->move($fullPath, $photoName)) {
                $relativePath = $photoPath . '/' . $photoName;

                // Сохраняем в базу
                $job->photos()->create([
                    'path' => $relativePath,
                    'sort' => $currentCount + $index
                ]);
            }
        }
    }

    public function updateStatus(Request $request, Job $job)
    {
        $request->validate([
            'status' => 'required|in:published,pending,hidden'
        ]);

        $job->update(['status' => $request->status]);

        return back()->with('success', __('admin.job_status_updated'));
    }

    public function destroy(Job $job)
    {
        $job->delete();

        return redirect()->route('admin.jobs.index')
            ->with('success', __('admin.job_deleted_successfully'));
    }

    // Статистика для дашборда
    public function getStats()
    {
        $stats = [
            'total' => Job::count(),
            'active' => Job::where('status', 'active')->count(),
            'pending' => Job::where('status', 'pending')->count(),
            'rejected' => Job::where('status', 'rejected')->count(),
            'today' => Job::whereDate('created_at', today())->count(),
        ];

        return $stats;
    }
}
