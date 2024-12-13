<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB; // Import DB facade

class ManajemenController extends Controller
{
    public function index()
    {
        $all_users = User::all(); // Ambil semua pengguna dari basis data
        return view('superadmin.usermanajemen', compact('all_users'));
    }
    
    public function tambahuser()
    {
        return view('superadmin.tambahuser');
    }

    public function AddUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nama_pt' => 'required|string|max:255',
            'company_type' => 'required|string|max:255',
            'alamat_pt' => 'required|string|max:255',
            'nomor_wa' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:tbl_users,email',
            'role' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            User::create([
                'name' => $request->name,
                'nama_pt' => $request->nama_pt,
                'company_type' => $request->company_type,
                'alamat_pt' => $request->alamat_pt,
                'nomor_wa' => $request->nomor_wa,
                'email' => $request->email,
                'role' => $request->role,
                'password' => Hash::make($request->password),
            ]);

            return redirect()->route('usermanajemen')->with('success', 'User berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan user: ' . $e->getMessage());
        }
    }

    public function loadedit($user_id)
    {
        $user = User::findOrFail($user_id);
        return view('superadmin.edit-user', compact('user'));
    }

    public function EditUser(Request $request, $user_id)
    {
        // dd($request->all());
        $request->validate([
            
            'name' => 'required|string|max:255',
            'nama_pt' => 'required|string|max:255',
            'company_type' => 'required|string|max:255',
            'alamat_pt' => 'required|string|max:255',
            'nomor_wa' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('tbl_users')->ignore($user_id, 'user_id'),
            ],
            'role' => 'required|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        try {
            $user = User::findOrFail($user_id);
            $user->name = $request->name;
            $user->nama_pt = $request->nama_pt;
            $user->company_type = $request->company_type;
            $user->alamat_pt = $request->alamat_pt;
            $user->nomor_wa = $request->nomor_wa;
            $user->email = $request->email;
            $user->role = $request->role;

            if ($request->password) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            return redirect()->route('usermanajemen')->with('success', 'User berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui user: ' . $e->getMessage());
        }
    }

    public function delete($user_id)
    {
        User::where('user_id', $user_id)->delete(); // Menggunakan user_id
        return redirect()->route('usermanajemen')->with('success', 'User berhasil dihapus.');
    }
}
