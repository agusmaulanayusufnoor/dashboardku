<?php

namespace App\Imports;

use App\Models\ImportKredit;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class KreditImport implements ToModel, WithHeadingRow, WithValidation
{
    private $rowCount = 0;
    private $successCount = 0;
    private $errorCount = 0;

    public function __construct()
    {
        Log::info('Memulai proses import data kredit');
    }

    public function model(array $row)
    {
        $this->rowCount++;

        // Log setiap baris yang diproses
        Log::info("Memproses baris ke-{$this->rowCount}", [
            'no_rekening' => $row['no_rekening'] ?? null,
            'nama_nasabah' => $row['nama_nasabah'] ?? null,
        ]);

        try {
            // Validasi data wajib
            if (empty($row['no_rekening']) || empty($row['nama_nasabah']) || empty($row['tgl_realisasi']) || empty($row['jml_pinjaman'])) {
                $this->errorCount++;
                Log::warning("Data tidak lengkap pada baris ke-{$this->rowCount}", [
                    'row_data' => $row,
                ]);
                return null;
            }

            $data = [
                'no_rekening' => $row['no_rekening'],
                'no_alternatif' => $row['no_alternatif'] ?? null,
                'nama_nasabah' => $row['nama_nasabah'],
                'alamat' => $row['alamat'] ?? null,
                'desa' => $row['desa'] ?? null,
                'tgl_realisasi' => $this->transformDate($row['tgl_realisasi']),
                'jkw' => $row['jkw'] ?? null,
                'tgl_jt' => $this->transformDate($row['tgl_jt']),
                'type_kredit' => $row['type_kredit'] ?? null,
                'suku_bunga' => $row['suku_bunga'] ?? null,
                'jml_pinjaman' => $row['jml_pinjaman'],
                'baki_debet' => $row['baki_debet'] ?? null,
                't_pokok' => $row['t_pokok'] ?? null,
                'ftp' => $row['ftp'] ?? null,
                'ftp_hari' => $row['ftp_hari'] ?? null,
                't_bunga' => $row['t_bunga'] ?? null,
                'ftb' => $row['ftb'] ?? null,
                'ftb_hari' => $row['ftb_hari'] ?? null,
                'jt' => $row['jt'] ?? null,
                'kol_bln_lalu' => $row['kol_bln_lalu'] ?? null,
                'kol_bln_ini' => $row['kol_bln_ini'] ?? null,
                'tag_pokok_bln_ini' => $row['tag_pokok_bln_ini'] ?? null,
                'tag_bunga_bln_ini' => $row['tag_bunga_bln_ini'] ?? null,
                'total_tagihan' => $row['total_tagihan'] ?? null,
                'set_pokok_bln_ini' => $row['set_pokok_bln_ini'] ?? null,
                'set_bunga_bln_ini' => $row['set_bunga_bln_ini'] ?? null,
                'total_setoran' => $row['total_setoran'] ?? null,
                'norek_tab' => $row['norek_tab'] ?? null,
                'saldo_tab' => $row['saldo_tab'] ?? null,
                'pencapaian' => $row['pencapaian'] ?? null,
                'ppka_bln_lalu' => $row['ppka_bln_lalu'] ?? null,
                'ppka_bln_ini' => $row['ppka_bln_ini'] ?? null,
                'selisih_ppka' => $row['selisih_ppka'] ?? null,
                'kode_group1' => $row['kode_group1'] ?? null,
                'kode_group2' => $row['kode_group2'] ?? null,
                'kode_group3' => $row['kode_group3'] ?? null,
                'kode_group4' => $row['kode_group4'] ?? null,
                'kode_group5' => $row['kode_group5'] ?? null,
                'sisa_jkw_bln' => $row['sisa_jkw_bln'] ?? null,
                'kode_kantor' => $row['kode_kantor'] ?? null,
                'kode_produk' => $row['kode_produk'] ?? null,
                'no_tlp_nasabah' => $row['no_tlp_nasabah'] ?? null,
                'no_ktp' => $row['no_ktp'] ?? null,
                'kecamatan' => $row['kecamatan'] ?? null,
                'nasabah_id' => $row['nasabah_id'] ?? null,
                'flag' => $row['flag'] ?? null,
                'tgl_report' => isset($row['tgl_report']) ? $this->transformDate($row['tgl_report']) : null,
            ];

            // Log data yang akan disimpan
            Log::info("Data yang akan disimpan untuk baris ke-{$this->rowCount}", $data);

            // Coba insert langsung ke database untuk debugging
            $id = DB::table('import_kredits')->insertGetId($data);

            if ($id) {
                $this->successCount++;
                Log::info("Berhasil menyimpan data baris ke-{$this->rowCount} dengan ID: {$id}");
            } else {
                $this->errorCount++;
                Log::error("Gagal menyimpan data baris ke-{$this->rowCount}");
            }

            return null; // Karena sudah diinsert langsung, tidak perlu return model
        } catch (\Exception $e) {
            $this->errorCount++;
            Log::error("Error memproses baris ke-{$this->rowCount}", [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'row_data' => $row,
            ]);
            return null;
        }
    }

    public function rules(): array
    {
        return [
            'no_rekening' => 'required|string|max:20',
            'nama_nasabah' => 'required|string|max:200',
            'tgl_realisasi' => 'required',
            'jml_pinjaman' => 'required|numeric|min:0',
        ];
    }
    public function customValidationMessages()
    {
        return [
            'no_rekening.required' => 'Kolom nomor rekening wajib diisi',
            'no_rekening.string' => 'Nomor rekening harus berupa teks',
            'no_rekening.max' => 'Nomor rekening maksimal 20 karakter',
            'nama_nasabah.required' => 'Kolom nama nasabah wajib diisi',
            'nama_nasabah.string' => 'Nama nasabah harus berupa teks',
            'nama_nasabah.max' => 'Nama nasabah maksimal 200 karakter',
            'tgl_realisasi.required' => 'Kolom tanggal realisasi wajib diisi',
            'tgl_realisasi.date' => 'Format tanggal realisasi tidak valid',
            'tgl_jt.date' => 'Format tanggal jatuh tempo tidak valid',
            'jml_pinjaman.required' => 'Kolom jumlah pinjaman wajib diisi',
            'jml_pinjaman.numeric' => 'Jumlah pinjaman harus berupa angka',
            'jml_pinjaman.min' => 'Jumlah pinjaman minimal 0',
        ];
    }
    private function transformDate($value)
    {
        if (empty($value)) {
            return null;
        }

        try {
            // Jika value adalah numeric (format Excel)
            if (is_numeric($value)) {
                return Date::excelToDateTimeObject($value)->format('Y-m-d');
            }

            // Coba berbagai format tanggal yang mungkin
            $formats = [
                'd/m/Y',  // 25/11/2013
                'm/d/Y',  // 11/25/2013
                'Y-m-d',  // 2013-11-25
                'd-m-Y',  // 25-11-2013
                'm-d-Y',  // 11-25-2013
            ];

            foreach ($formats as $format) {
                try {
                    $date = \Carbon\Carbon::createFromFormat($format, $value);
                    return $date->format('Y-m-d');
                } catch (\Exception $e) {
                    // Lanjut ke format berikutnya
                }
            }

            // Jika semua format gagal, coba parse default
            return \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            Log::error('Error transform tanggal', [
                'value' => $value,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
    public function getRowCount(): int
    {
        return $this->rowCount;
    }

    public function getSuccessCount(): int
    {
        return $this->successCount;
    }

    public function getErrorCount(): int
    {
        return $this->errorCount;
    }
}
