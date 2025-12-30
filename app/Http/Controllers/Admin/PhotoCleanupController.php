<?php
// app/Http\Controllers/Admin/PhotoCleanupController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Job;

class PhotoCleanupController extends Controller
{
    public function index()
    {
        $photosPath = public_path('images/jobs');
        $folders = [];
        $orphanedPhotos = [];

        // Получаем все папки с job ID
        if (file_exists($photosPath)) {
            $folders = array_filter(scandir($photosPath), function($item) use ($photosPath) {
                return is_dir($photosPath . '/' . $item) && !in_array($item, ['.', '..']);
            });

            // Проверяем какие папки принадлежат несуществующим job
            foreach ($folders as $folder) {
                $jobId = (int) $folder;
                if ($jobId > 0 && !Job::where('id', $jobId)->exists()) {
                    $folderPath = $photosPath . '/' . $folder;

                    $photos = array_filter(scandir($folderPath), function($item) use ($folderPath) {
                        return !in_array($item, ['.', '..']) && is_file($folderPath . '/' . $item);
                    });

                    // Получаем дату создания папки (дата создания объявления)
                    $createdAt = filectime($folderPath);

                    // Предполагаем дату удаления (можно настроить логику)
                    // Например: если папка старше 30 дней, считаем что удалена 30 дней назад
                    $folderAgeDays = (time() - $createdAt) / (60 * 60 * 24);
                    $deletedAt = $folderAgeDays > 30 ? now()->subDays(30) : now()->subDays($folderAgeDays);
                    $daysSinceDeletion = now()->diffInDays($deletedAt);

                    $orphanedPhotos[$folder] = [
                        'photos_count' => count($photos),
                        'size' => $this->getFolderSize($folderPath),
                        'created_at' => $createdAt,
                        'deleted_at' => $deletedAt,
                        'days_since_deletion' => $daysSinceDeletion
                    ];
                }
            }
        }

        return view('admin.photos.index', compact('orphanedPhotos'));
    }

    public function destroyFolder($folder)
    {
        try {
            $folderPath = public_path('images/jobs/' . $folder);

            if (!file_exists($folderPath)) {
                return back()->with('error', 'Папка не найдена');
            }

            // Удаляем все файлы в папке
            $files = array_filter(scandir($folderPath), function($item) use ($folderPath) {
                return !in_array($item, ['.', '..']);
            });

            foreach ($files as $file) {
                unlink($folderPath . '/' . $file);
            }

            // Удаляем саму папку
            rmdir($folderPath);

            return back()->with('success', "Папка {$folder} и все фото удалены");

        } catch (\Exception $e) {
            return back()->with('error', 'Ошибка при удалении: ' . $e->getMessage());
        }
    }

    public function destroyAll()
    {
        try {
            $photosPath = public_path('images/jobs');
            $deletedCount = 0;

            if (file_exists($photosPath)) {
                $folders = array_filter(scandir($photosPath), function($item) use ($photosPath) {
                    return is_dir($photosPath . '/' . $item) && !in_array($item, ['.', '..']);
                });

                foreach ($folders as $folder) {
                    $jobId = (int) $folder;
                    if ($jobId > 0 && !Job::where('id', $jobId)->exists()) {
                        $folderPath = $photosPath . '/' . $folder;

                        // Удаляем файлы
                        $files = array_filter(scandir($folderPath), function($item) use ($folderPath) {
                            return !in_array($item, ['.', '..']);
                        });

                        foreach ($files as $file) {
                            unlink($folderPath . '/' . $file);
                        }

                        rmdir($folderPath);
                        $deletedCount++;
                    }
                }
            }

            return back()->with('success', "Удалено {$deletedCount} папок с фото");

        } catch (\Exception $e) {
            return back()->with('error', 'Ошибка при удалении: ' . $e->getMessage());
        }
    }

    private function getFolderSize($path)
    {
        $size = 0;
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path)) as $file) {
            if ($file->isFile()) {
                $size += $file->getSize();
            }
        }
        return number_format($size / 1024 / 1024, 2) . ' MB';
    }
}
