<?php
// app/Http/Controllers/Admin/JobCategoryController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class JobCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!in_array(auth()->user()->role, ['admin', 'manager'])) {
                abort(403, 'Access denied.');
            }
            return $next($request);
        });
    }
    public function index()
    {
        $categories = JobCategory::ordered()->paginate(10);
        return view('admin.job-categories.index', compact('categories'));
    }

    public function create()
    {
        // Автоматически определяем следующий порядок сортировки
        $nextSortOrder = JobCategory::max('sort') + 10;

        return view('admin.job-categories.create', compact('nextSortOrder'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:job_categories',
            'slug' => 'required|string|max:255|unique:job_categories',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'sort' => 'nullable|integer'
        ]);

        $data = [
            'name' => $request->name,
            'slug' => Str::slug($request->slug),
            'sort' => $request->sort ?? JobCategory::max('sort') + 10,
            'image' => 'categories/ostatni.jpg',
        ];

        // Обработка загрузки изображения
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = 'category_' . time() . '_' . uniqid() . '.' . $image->extension();

            // Создаем папку если не существует
            $imagePath = public_path('images/categories');
            if (!file_exists($imagePath)) {
                mkdir($imagePath, 0755, true);
            }

            // Сохраняем изображение
            $image->move($imagePath, $imageName);
            $data['image'] = 'categories/' . $imageName;
        }

        JobCategory::create($data);

        return redirect()->route('admin.job-categories.index')
            ->with('success', __('admin.category_created_successfully'));
    }

    public function show(JobCategory $jobCategory)
    {
        $jobCategory->loadCount('jobs');
        return view('admin.job-categories.show', [
            'category' => $jobCategory
        ]);
    }
    public function edit(JobCategory $jobCategory)
    {
        $jobCategory->loadCount('jobs');
        return view('admin.job-categories.edit', compact('jobCategory'));
    }
    public function update(Request $request, JobCategory $jobCategory)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:job_categories,name,' . $jobCategory->id,
            'slug' => 'required|string|max:255|unique:job_categories,slug,' . $jobCategory->id,
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'sort' => 'nullable|integer',
        ]);

        $data = [
            'name' => $request->name,
            'slug' => Str::slug($request->slug),
            'description' => $request->description,
            'sort' => $request->sort ?? $jobCategory->sort,
        ];

        // Обработка удаления изображения
        if ($request->has('remove_image') && $jobCategory->image && $jobCategory->image !== 'default.jpg') {
            // Удаляем файл если это не изображение по умолчанию
            $oldImagePath = public_path('images/' . $jobCategory->image);
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
            $data['image'] = 'default.jpg';
        }

        // Обработка загрузки нового изображения
        if ($request->hasFile('image')) {
            // Удаляем старое изображение если это не изображение по умолчанию
            if ($jobCategory->image && $jobCategory->image !== 'categories/ostatni.jpg') {
                $oldImagePath = public_path('images/' . $jobCategory->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $image = $request->file('image');
            $imageName = 'category_' . $jobCategory->id . '_' . time() . '.' . $image->extension();

            // Создаем папку если не существует
            $imagePath = public_path('images/categories');
            if (!file_exists($imagePath)) {
                mkdir($imagePath, 0755, true);
            }

            // Сохраняем изображение
            $image->move($imagePath, $imageName);
            $data['image'] = 'categories/' . $imageName;
        }

        $jobCategory->update($data);

        return redirect()->route('admin.job-categories.show', $jobCategory)
            ->with('success', __('admin.category_updated_successfully'));
    }

    public function destroy(JobCategory $jobCategory)
    {
        $jobCategory->delete();

        return redirect()->route('admin.job-categories.index')
            ->with('success', __('admin.category_deleted_successfully'));
    }
}
