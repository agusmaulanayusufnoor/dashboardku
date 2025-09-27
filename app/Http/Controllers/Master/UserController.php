<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Kantor;
use App\Models\Divisi;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;


class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $perPage = $request->get('perPage', 5);

        $users = User::with(['kantor', 'divisi', 'roles'])
            ->when($search, fn ($q) =>
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
            )
            ->paginate($perPage);

        $roles = Role::all();
        $kantors = Kantor::all();
        $divisis = Divisi::all();

        return Inertia::render('Master/Users/Index', [
            'users' => $users,
            'roles' => $roles,
            'kantors' => $kantors,
            'divisis' => $divisis,
            'search' => $search,
            'perPage' => $perPage,
        ]);
    }

    public function store(Request $request)
    {
        
        $data = $request->validate([
            'username'  => 'required|unique:users',
            'name'      => 'required|string',
            'email'     => 'required|email|unique:users',
            'password'  => 'required|min:6',
            'kantor_id' => 'required|exists:kantor,id',
            'divisi_id' => 'required|exists:divisi,id',
            'roles'     => 'array'
        ]);

        $user = User::create([
            'username'  => $data['username'],
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => bcrypt($data['password']),
            'kantor_id' => $data['kantor_id'],
            'divisi_id' => $data['divisi_id'],
        ]);

        if (!empty($data['roles'])) {
            $user->syncRoles($data['roles']);
        }

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
       
        $data = $request->validate([
            'username'  => 'required|unique:users,username,' . $user->id,
            'name'      => 'required|string',
            'email'     => 'required|email|unique:users,email,' . $user->id,
            'password'  => 'nullable|min:6',
            'kantor_id' => 'required|exists:kantor,id',
            'divisi_id' => 'required|exists:divisi,id',
            'roles'     => 'array'
        ]);

        $user->update([
            'username'  => $data['username'],
            'name'      => $data['name'],
            'email'     => $data['email'],
            'kantor_id' => $data['kantor_id'],
            'divisi_id' => $data['divisi_id'],
            'password'  => $data['password'] ? bcrypt($data['password']) : $user->password,
        ]);

        if (!empty($data['roles'])) {
            $user->syncRoles($data['roles']);
        }

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }
}
