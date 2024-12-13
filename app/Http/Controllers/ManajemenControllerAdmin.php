<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class ManajemenControllerAdmin extends Controller
{
    public function index()
    {
        $admin = auth()->user();
        $all_users = User::where('nama_pt', $admin->nama_pt)->get();
        return view('admin.usermanajemen', compact('all_users'));
    }

    public function loadadduseradmin()
    {
        return view('admin.tambahuser');
    }

    public function AddUserAdmin(Request $request): RedirectResponse
    {
        // Validasi form
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nomor_wa' => ['required', 'string', 'regex:/^[0-9\+]{10,15}$/'],
            'nama_pt' => ['required', 'string'],
            'alamat_pt' => ['required', 'string'],
            'company_type' => ['required', 'string'],
            'role' => ['required', 'string'], 
            'email' => ['required', 'string', 'email', 'max:255', 'unique:tbl_users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        try {
            // Buat user baru
            $user = User::create([
                'name' => $request->name,
                'nomor_wa' => $request->nomor_wa,
                'nama_pt' => auth()->user()->nama_pt,
                'alamat_pt' => auth()->user()->alamat_pt,
                'company_type' => $request->company_type,
                'role' => $request->role,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            event(new Registered($user));

            return redirect()->route('admin.usermanajemen')->with('status', 'User berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->route('load.admin.add.user')->with('fail', 'Gagal menambahkan user: ' . $e->getMessage());
        }
    }

    public function loadeditadmin($user_id)
    {
        $admin = auth()->user();
        $user = User::where('user_id', $user_id)->where('nama_pt', $admin->nama_pt)->firstOrFail();
        return view('admin.edit-user', compact('user'));
    }

    public function EditUserAdmin(Request $request, $user_id): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nomor_wa' => ['required', 'string', 'regex:/^[0-9\+]{10,15}$/'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:tbl_users,email,' . $user_id . ',user_id'],
            'role' => ['required', 'string'],
            'nama_pt' => ['required', 'string'],
            'alamat_pt' => ['required', 'string'],
            'company_type' => ['required', 'string'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        try {
            $admin = auth()->user();
            $user = User::where('user_id', $user_id)->where('nama_pt', $admin->nama_pt)->firstOrFail();
            $user->update([
                'name' => $request->name,
                'nomor_wa' => $request->nomor_wa,
                'email' => $request->email,
                'role' => $request->role,
                'nama_pt' => $request->nama_pt,
                'alamat_pt' => $request->alamat_pt,
                'company_type' => $request->company_type,
            ]);

            // Jika password diisi, update password
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
                $user->save();
            }

            return redirect()->route('admin.usermanajemen')->with('success', 'User berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->route('admin.usermanajemen')->with('fail', 'Gagal memperbarui user: ' . $e->getMessage());
        }
    }

    public function deleteadmin($user_id)
    {
        try {
            $admin = auth()->user();
            $user = User::where('user_id', $user_id)->where('nama_pt', $admin->nama_pt)->firstOrFail();
            $user->delete();

            return redirect()->route('admin.usermanajemen')->with('success', 'User berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('admin.usermanajemen')->with('fail', 'Gagal menghapus user: ' . $e->getMessage());
        }
    }
}
