<?php

namespace App\Http\Controllers;

use App\Models\ImportHistory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ImportHistoryController extends Controller
{
    public function index()
    {
        $histories = ImportHistory::with('user')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return Inertia::render('ImportHistory/Index', [
            'histories' => $histories,
        ]);
    }
}