<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Kantor;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class KantorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $search = $request->input('search', '');
        $perPage = (int) $request->input('perPage', 5);
        $sortKey = $request->input('sortKey', 'id');
        $sortOrder = $request->input('sortOrder', 'asc');

        $query = Kantor::query();

        // Searching
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('kode_kantor', 'like', "%{$search}%")
                  ->orWhere('nama_kantor', 'like', "%{$search}%");
            });
        }

        // Sorting (default by id ascending)
        $query->orderBy($sortKey, $sortOrder);

        $kantors = $query->paginate($perPage)->withQueryString();

        return Inertia::render('Master/Kantor/Index', [
            'kantors' => $kantors,
            'search' => $search,
            'perPage' => $perPage,
            'sortKey' => $sortKey,
            'sortOrder' => $sortOrder,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        $validated = $request->validate([
            'kode_kantor' => 'required|string|max:50|unique:kantor,kode_kantor',
            'nama_kantor' => 'required|string|max:255',
        ]);

        $kantor = Kantor::create($validated);

        return redirect()->back()->with('success', 'Kantor berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
       
        $kantor = Kantor::findOrFail($id);

        $validated = $request->validate([
            'kode_kantor' => 'required|string|max:50|unique:kantor,kode_kantor,' . $kantor->id,
            'nama_kantor' => 'required|string|max:255',
        ]);

        $kantor->update($validated);

        return redirect()->back()->with('success', 'Kantor berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
      
        $kantor = Kantor::findOrFail($id);
        $kantor->delete();

        return redirect()->back()->with('success', 'Kantor berhasil dihapus.');
    }
}
