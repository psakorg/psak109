<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
{
    // Validasi foto jika ada
    $request->validate([
        'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'], // max 2MB
    ]);

    $user = $request->user();
    $user->fill($request->validated());

    // Upload foto jika ada
    if ($request->hasFile('photo')) {
        // Simpan file foto dan ambil path-nya
        $path = $request->file('photo')->store('profile_photos', 'public');

        // Hapus foto lama jika ada
        if ($user->upload_foto) {
            Storage::disk('public')->delete($user->upload_foto);
        }

        // Simpan path foto baru di profil pengguna
        $user->upload_foto = $path;
    }

    // Mengatur ulang email_verified_at jika email diubah
    if ($user->isDirty('email')) {
        $user->email_verified_at = null;
    }

    // Simpan perubahan pengguna
    $user->save();

    return Redirect::route('profile.edit')->with('status', 'profile-updated');
}

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
