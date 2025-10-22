<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::with('roles');

        // Filter berdasarkan pencarian nama atau email
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // Filter berdasarkan peran
        if ($request->has('role') && $request->role != '') {
            $role = $request->role;
            $query->whereHas('roles', function ($q) use ($role) {
                $q->where('name', $role);
            });
        }

        $users = $query->latest()->paginate(10)->withQueryString();
        
        // Ambil semua peran untuk ditampilkan di dropdown filter
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'roles' => 'required|array'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->syncRoles($request->roles);

        // ✅ LOG USER CREATION
        \App\Models\ActivityLog::log('user_created', [
            'description' => "Created new user: {$user->name} with role(s): " . implode(', ', $request->roles),
            'metadata' => [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'assigned_roles' => $request->roles,
            ]
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Biasanya tidak digunakan untuk manajemen pengguna, bisa dibiarkan kosong
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'roles' => 'required|array'
        ]);

        // ✅ ENHANCED LOGGING: Capture before/after changes
        $originalData = $user->getOriginal();
        $originalRoles = $user->roles->pluck('name')->toArray();

        $user->update(['name' => $validated['name'], 'email' => $validated['email']]);
        $user->syncRoles($validated['roles']);

        // Track changes
        $changes = [];
        if ($originalData['name'] != $user->name) {
            $changes['name'] = ['before' => $originalData['name'], 'after' => $user->name];
        }
        if ($originalData['email'] != $user->email) {
            $changes['email'] = ['before' => $originalData['email'], 'after' => $user->email];
        }
        if ($originalRoles != $validated['roles']) {
            $changes['roles'] = ['before' => $originalRoles, 'after' => $validated['roles']];
        }

        // ✅ LOG USER UPDATE
        \App\Models\ActivityLog::log('user_updated', [
            'description' => "Updated user: {$user->name}" . (count($changes) > 0 ? " (" . implode(', ', array_keys($changes)) . " changed)" : ""),
            'metadata' => [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'current_roles' => $validated['roles'],
                'changes' => $changes,
                'changed_fields' => array_keys($changes),
            ]
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Show the form for resetting user password.
     */
    public function resetPasswordForm(User $user)
    {
        return view('admin.users.reset-password', compact('user'));
    }

    /**
     * Reset user password.
     */
    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (Auth::user()->id === $user->id) {
            return redirect()->route('admin.users.index')->with('error', 'Anda tidak dapat mengubah password akun Anda sendiri melalui fitur ini.');
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // ✅ LOG PASSWORD RESET
        \App\Models\ActivityLog::log('user_password_reset', [
            'description' => "Reset password for user: {$user->name}",
            'metadata' => [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
            ]
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Password user berhasil diubah.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if (Auth::user()->id === $user->id) {
            return redirect()->route('admin.users.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // Store data before deletion for logging
        $userData = [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_email' => $user->email,
            'user_roles' => $user->roles->pluck('name')->toArray(),
        ];

        $user->delete();

        // ✅ LOG USER DELETION
        \App\Models\ActivityLog::log('user_deleted', [
            'description' => "Deleted user: {$userData['user_name']} ({$userData['user_email']})",
            'metadata' => $userData
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus.');
    }
}
