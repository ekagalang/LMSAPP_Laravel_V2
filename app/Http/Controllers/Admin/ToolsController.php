<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Models\Role;

class ToolsController extends Controller
{
    public function index()
    {
        return view('admin.tools.index');
    }

    public function refreshPermissionCache(Request $request)
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        cache()->clear();
        return redirect()->back()->with('success', 'Permission cache berhasil di-refresh.');
    }

    public function exportRoleMatrix()
    {
        $roles = Role::with('permissions')->get()->map(function ($role) {
            return [
                'role' => $role->name,
                'permissions' => $role->permissions->pluck('name')->sort()->values()->all(),
            ];
        })->values()->all();

        $json = json_encode(['generated_at' => now()->toIso8601String(), 'roles' => $roles], JSON_PRETTY_PRINT);
        $filename = 'role_matrix_' . now()->format('Ymd_His') . '.json';
        return response($json, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
