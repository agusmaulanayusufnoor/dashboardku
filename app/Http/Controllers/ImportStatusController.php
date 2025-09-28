<?php

namespace App\Http\Controllers;

use App\Models\ImportHistory;
use Illuminate\Http\Request;

class ImportStatusController extends Controller
{
    public function check($id)
    {
        $history = ImportHistory::find($id);
        
        if (!$history) {
            return response()->json(['error' => 'Import history not found'], 404);
        }
        
        return response()->json([
            'id' => $history->id,
            'status' => $history->status,
            'total_rows' => $history->total_rows,
            'success_count' => $history->success_count,
            'error_count' => $history->error_count,
            'error_message' => $history->error_message,
        ]);
    }
}