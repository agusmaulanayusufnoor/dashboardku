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
            'tgl_report' => 'required',
            'file' => 'required|mimes:xlsx,xls,csv|max:102400'
        ]);

        try {
            // Log informasi request
            Log::info('Request diterima', [
                'tgl_report' => $request->tgl_report,
                'file_name' => $request->file('file')->getClientOriginalName(),
                'file_size' => $request->file('file')->getSize(),
                'user_id' => auth()->id(),
            ]);

            // Simpan file ke storage
            $filePath = $request->file('file')->store('imports');
            Log::info('File disimpan', ['path' => $filePath]);

            // Dispatch job ke queue
            ProcessKreditImport::dispatch($filePath, $request->tgl_report, auth()->id());
            Log::info('Job dikirim ke queue');

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
