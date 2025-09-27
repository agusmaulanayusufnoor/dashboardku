<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Imports\KreditImport;
use App\Jobs\ProcessKreditImport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\KreditTemplateExport;

class ImportKreditController extends Controller
{
    public function index()
    {
        return Inertia::render('ImportKredit/Index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tgl_report' => 'required|date',
            'file' => 'required|mimes:xlsx,xls,csv|max:102400'
        ]);

        try {
            // Simpan file ke storage
            $filePath = $request->file('file')->store('imports');

            // Dispatch job ke queue
            ProcessKreditImport::dispatch($filePath, $request->tgl_report, auth()->id());

            // Log informasi
            Log::info('File import telah dikirim ke queue', [
                'tgl_report' => $request->tgl_report,
                'file_path' => $filePath,
                'user_id' => auth()->id(),
            ]);

            // Kembalikan response dengan flash message
            return redirect()->back()->with('success', 'File sedang diproses di background. Anda akan mendapatkan notifikasi setelah proses selesai.');
        } catch (\Exception $e) {
            // Log error umum
            Log::error('Error mengirim file ke queue', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    public function downloadTemplate()
    {
        return Excel::download(new KreditTemplateExport, 'template_import_kredit.xlsx');
    }
}
