<?php
// app/Http/Controllers/Admin/ProfileController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class ProfileController extends Controller
{
    public function index()
    {
        return view('admin.users.profile');
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.profile')
            ->with('success', __('admin.updated_successfully'));
    }
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $user = auth()->user();

        // Удаляем старый аватар если есть
        if ($user->avatar && file_exists(public_path('images/avatars/' . $user->avatar))) {
            unlink(public_path('images/avatars/' . $user->avatar));
        }

        // Сохраняем новый аватар
        $avatarName = 'avatar_' . $user->id . '_' . time() . '.' . $request->avatar->extension();
        $request->avatar->move(public_path('images/avatars'), $avatarName);

        // ОБНОВЛЯЕМ поле avatar в базе данных
        $user->update(['avatar' => $avatarName]);

        return redirect()->route('admin.profile')
            ->with('success', __('admin.avatar_updated_successfully'));
    }

    public function removeAvatar(Request $request)
    {
        $user = auth()->user();

        if ($user->avatar && file_exists(public_path('images/avatars/' . $user->avatar))) {
            unlink(public_path('images/avatars/' . $user->avatar));
        }

        $user->update(['avatar' => null]);

        return redirect()->route('admin.profile')
            ->with('success', __('admin.avatar_removed_successfully'));
    }
}
