<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $perPage = (int) $request->input('perPage', 5);
        $sortKey = $request->input('sortKey', 'id');
        $sortOrder = $request->input('sortOrder', 'asc');

        $roles = Role::with('permissions')
            ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy($sortKey, $sortOrder)
            ->paginate($perPage)
            ->withQueryString();

        $allPermissions = Permission::all()
            ->groupBy(fn($perm) => explode('.', $perm->name)[0])
            ->map(fn($group) => $group->values()->toArray())
            ->toArray();

        return Inertia::render('Master/Roles/Index', [
            'roles' => $roles,
            'permissions' => $allPermissions,
            'search' => $search,
            'perPage' => $perPage,
            'sortKey' => $sortKey,
            'sortOrder' => $sortOrder,
        ]);
    }

    public function store(Request $request)
    {
        
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'array'
        ]);

        $role = Role::create(['name' => $validated['name']]);

        if (!empty($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        return redirect()->back()->with('success', 'Role berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        
        $role = Role::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'array'
        ]);

        $role->update(['name' => $validated['name']]);

        $role->syncPermissions($validated['permissions'] ?? []);

        return redirect()->back()->with('success', 'Role berhasil diupdate.');
    }

    public function destroy($id)
    {
     
        $role = Role::findOrFail($id);
        $role->delete();

        return redirect()->back()->with('success', 'Role berhasil dihapus.');
    }
}
