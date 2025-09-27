<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $perPage = (int) $request->input('perPage', 5);
        $sortKey = $request->input('sortKey', 'id');
        $sortOrder = $request->input('sortOrder', 'asc');

        $permissions = Permission::when($search, fn($q) =>
                $q->where('name', 'like', "%{$search}%")
            )
            ->orderBy($sortKey, $sortOrder)
            ->paginate($perPage)
            ->withQueryString();

        return Inertia::render('Master/Permissions/Index', [
            'permissions' => $permissions,
            'search' => $search,
            'perPage' => $perPage,
            'sortKey' => $sortKey,
            'sortOrder' => $sortOrder,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
        ]);

        Permission::create(['name' => $validated['name']]);

        return redirect()->back()->with('success', 'Permission berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
        ]);

        $permission->update(['name' => $validated['name']]);

        return redirect()->back()->with('success', 'Permission berhasil diupdate.');
    }

    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();

        return redirect()->back()->with('success', 'Permission berhasil dihapus.');
    }
}
