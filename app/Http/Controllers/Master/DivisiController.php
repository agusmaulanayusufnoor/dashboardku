<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use Illuminate\Http\Request;
use Inertia\Inertia;


class DivisiController extends Controller
{
    public function index(Request $request)
    {
        $query = Divisi::query();

        // Search
        if ($search = $request->input('search')) {
            $query->where('nama_divisi', 'like', "%$search%");
        }

        // Sorting
        $sortKey = $request->input('sortKey', 'id');
        $sortOrder = $request->input('sortOrder', 'asc');
        $query->orderBy($sortKey, $sortOrder);

        // Pagination
        $perPage = $request->input('perPage', 5);
        $divisies = $query->paginate($perPage)->withQueryString();

        return Inertia::render('Master/Divisi/Index', [
            'divisies' => $divisies,
            'search' => $search,
            'perPage' => $perPage,
            'sortKey' => $sortKey,
            'sortOrder' => $sortOrder,
        ]);
    }

    public function store(Request $request)
    {
      
        $data = $request->validate([
            'nama_divisi' => 'required|string|max:100',
        ]);

        Divisi::create($data);

        return redirect()->route('divisi.index');
    }

    public function update(Request $request, $id)
    {
        
        $divisi = Divisi::findOrFail($id);

        $data = $request->validate([
            'nama_divisi' => 'required|string|max:100',
        ]);

        $divisi->update($data);

        return redirect()->route('divisi.index');
    }

    public function destroy($id)
    {
   
        $divisi = Divisi::findOrFail($id);
        $divisi->delete();

        return redirect()->route('divisi.index');
    }
}
