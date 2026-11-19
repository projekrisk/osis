<?php

namespace App\Http\Controllers;

use App\Models\Tps;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('q', '');
        $role = $request->get('role', '');

        $tpsList = Tps::orderBy('kode_tps')->get();

        $users = User::with('tps')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($role, fn ($q) => $q->where('role', $role))
            ->orderBy('role')
            ->orderBy('name')
            ->get();

        return view('users.index', compact('users', 'tpsList', 'search', 'role'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:admin,panitia',
            'tps_id' => 'nullable|exists:tps,id',
            'password' => 'required|string|min:6',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'tps_id' => $validated['role'] === 'panitia' ? ($validated['tps_id'] ?? null) : null,
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('users.index')
            ->with('success', "Pengguna {$validated['name']} ({$validated['role']}) berhasil ditambahkan.");
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,panitia',
            'tps_id' => 'nullable|exists:tps,id',
            'password' => 'nullable|string|min:6',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'tps_id' => $validated['role'] === 'panitia' ? ($validated['tps_id'] ?? null) : null,
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()->route('users.index')
            ->with('success', "Data pengguna {$user->name} berhasil diperbarui.");
    }

    public function destroy(User $user)
    {
        if (Auth::id() === $user->id) {
            return redirect()->route('users.index')
                ->with('error', 'Anda tidak dapat menghapus akun yang sedang digunakan.');
        }

        $nama = $user->name;
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', "Pengguna {$nama} telah dihapus.");
    }
}
