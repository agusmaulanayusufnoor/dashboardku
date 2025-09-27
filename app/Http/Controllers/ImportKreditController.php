<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Imports\KreditImport;
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
            // Log informasi file
            Log::info('Memulai import file', [
                'tgl_report' => $request->tgl_report,
                'nama_file' => $request->file('file')->getClientOriginalName(),
                'ukuran_file' => $request->file('file')->getSize() . ' bytes',
                'tipe_file' => $request->file('file')->getMimeType(),
            ]);

            // Hapus data yang ada untuk tanggal report yang sama
            $deletedCount = DB::table('import_kredits')->where('tgl_report', $request->tgl_report)->delete();
            Log::info("Menghapus {$deletedCount} data untuk tanggal report {$request->tgl_report}");

            // Import data
            $import = new KreditImport($request->tgl_report);
            Excel::import($import, $request->file('file'));


            // Log hasil import
            Log::info('Import data selesai', [
                'total_baris' => $import->getRowCount(),
                'berhasil' => $import->getSuccessCount(),
                'gagal' => $import->getErrorCount(),
            ]);

            // Kembalikan response dengan flash message
            return redirect()->back()->with('success', "Import selesai! Total: {$import->getRowCount()} baris, Berhasil: {$import->getSuccessCount()}, Gagal: {$import->getErrorCount()}");
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            // Log error validasi
            Log::error('Error validasi import', [
                'errors' => $e->failures(),
            ]);

            // Format error untuk ditampilkan
            $errorMessages = [];
            foreach ($e->failures() as $failure) {
                $errorMessages[] = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
            }

            return redirect()->back()->with('error', 'Validasi gagal: ' . implode('<br>', $errorMessages));
        } catch (\Exception $e) {
            // Log error umum
            Log::error('Error import data', [
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
