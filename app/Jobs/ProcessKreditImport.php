<?php

namespace App\Jobs;

use App\Imports\KreditImport;
use App\Models\ImportHistory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ProcessKreditImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;
    protected $tglReport;
    protected $userId;
    protected $importHistory;

    public function __construct($filePath, $tglReport, $userId)
    {
        $this->filePath = $filePath;
        $this->tglReport = $tglReport;
        $this->userId = $userId;
    }

    public function handle(): void
    {
        try {
            Log::info('Job dimulai', [
                'file_path' => $this->filePath,
                'tgl_report' => $this->tglReport,
                'user_id' => $this->userId,
            ]);

            // Periksa apakah file ada menggunakan Storage facade dengan disk local
            if (!Storage::disk('local')->exists($this->filePath)) {
                throw new \Exception("File tidak ditemukan: {$this->filePath}");
            }
            Log::info('File ditemukan', ['path' => $this->filePath]);

            // Update status menjadi processing
            $this->importHistory = ImportHistory::create([
                'user_id' => $this->userId,
                'tgl_report' => $this->tglReport,
                'file_path' => $this->filePath,
                'status' => 'processing',
            ]);

            Log::info('Import history dibuat', ['id' => $this->importHistory->id]);

            // Hapus data yang ada untuk tanggal report yang sama
            $deletedCount = DB::table('import_kredits')->where('tgl_report', $this->tglReport)->delete();
            Log::info("Menghapus {$deletedCount} data untuk tanggal report {$this->tglReport}");

            // Import data - pastikan melewatkan tgl_report
            $import = new KreditImport($this->tglReport);
            Log::info('KreditImport dibuat', ['tgl_report' => $this->tglReport]);
            
            // Gunakan Storage path untuk import dengan disk local
            Excel::import($import, Storage::disk('local')->path($this->filePath));
            
            // Log hasil import
            Log::info('Import data selesai via queue', [
                'total_baris' => $import->getRowCount(),
                'berhasil' => $import->getSuccessCount(),
                'gagal' => $import->getErrorCount(),
            ]);
            
            // Update status menjadi completed
            $this->importHistory->update([
                'total_rows' => $import->getRowCount(),
                'success_count' => $import->getSuccessCount(),
                'error_count' => $import->getErrorCount(),
                'status' => 'completed',
            ]);
            
            Log::info('Import history diupdate menjadi completed');
            
            // Hapus file setelah selesai
            Storage::disk('local')->delete($this->filePath);
            Log::info('File dihapus', ['path' => $this->filePath]);
            
        } catch (\Exception $e) {
            Log::error('Error import data via queue', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            // Update status menjadi failed
            if ($this->importHistory) {
                $this->importHistory->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);
                Log::info('Import history diupdate menjadi failed');
            }
            
            // Hapus file jika ada error
            if (Storage::disk('local')->exists($this->filePath)) {
                Storage::disk('local')->delete($this->filePath);
                Log::info('File dihapus karena error', ['path' => $this->filePath]);
            }
        }
    }
}