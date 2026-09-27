<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\Auditable;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use Auditable;

    public function index()
    {
        $users = User::orderByRaw("CASE role WHEN 'owner' THEN 1 WHEN 'admin' THEN 2 WHEN 'kasir' THEN 3 END")
            ->orderBy('username')
            ->get();

        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:50|unique:users,username',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role'     => 'required|in:kasir,admin',
        ]);

        User::create([
            'username' => $request->username,
            'email'    => $request->email,
            'password' => $request->password,
            'role'     => $request->role,
        ]);

        $this->catatAudit('Tambah User', "Menambahkan user: {$request->username} (role: {$request->role})");

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        // Owner tidak bisa mengedit dirinya sendiri dari halaman ini
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa mengedit akun Anda sendiri dari halaman ini.');
        }

        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa mengedit akun Anda sendiri dari halaman ini.');
        }

        $request->validate([
            'username' => 'required|string|max:50|unique:users,username,' . $user->id,
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'role'     => 'required|in:kasir,admin',
        ]);

        $data = [
            'username' => $request->username,
            'email'    => $request->email,
            'role'     => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        $user->update($data);

        $this->catatAudit('Edit User', "Mengedit user: {$user->username} (role: {$user->role})");

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa menghapus akun Anda sendiri.');
        }

        if ($user->isOwner()) {
            return back()->with('error', 'Akun Owner tidak bisa dihapus.');
        }

        if ($user->transactions()->exists() || $user->purchases()->exists()) {
            return back()->with('error', 'User tidak bisa dihapus karena sudah pernah melakukan transaksi atau pembelian.');
        }

        $nama = $user->username;
        $user->delete();

        $this->catatAudit('Hapus User', "Menghapus user: {$nama}");

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }
}
