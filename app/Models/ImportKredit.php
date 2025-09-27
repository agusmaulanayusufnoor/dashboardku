<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class ImportKredit extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_rekening',
        'no_alternatif',
        'nama_nasabah',
        'alamat',
        'desa',
        'tgl_realisasi',
        'jkw',
        'tgl_jt',
        'type_kredit',
        'suku_bunga',
        'jml_pinjaman',
        'baki_debet',
        't_pokok',
        'ftp',
        'ftp_hari',
        't_bunga',
        'ftb',
        'ftb_hari',
        'jt',
        'kol_bln_lalu',
        'kol_bln_ini',
        'tag_pokok_bln_ini',
        'tag_bunga_bln_ini',
        'total_tagihan',
        'set_pokok_bln_ini',
        'set_bunga_bln_ini',
        'total_setoran',
        'norek_tab',
        'saldo_tab',
        'pencapaian',
        'ppka_bln_lalu',
        'ppka_bln_ini',
        'selisih_ppka',
        'kode_group1',
        'kode_group2',
        'kode_group3',
        'kode_group4',
        'kode_group5',
        'sisa_jkw_bln',
        'kode_kantor',
        'kode_produk',
        'no_tlp_nasabah',
        'no_ktp',
        'kecamatan',
        'nasabah_id',
        'flag',
        'tgl_report',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            Log::info('Creating ImportKredit', [
                'data' => $model->toArray(),
            ]);
        });

        static::created(function ($model) {
            Log::info('ImportKredit created successfully', [
                'id' => $model->id,
                'no_rekening' => $model->no_rekening,
            ]);
        });

        static::saving(function ($model) {
            Log::info('Saving ImportKredit', [
                'data' => $model->toArray(),
            ]);
        });

        static::saved(function ($model) {
            Log::info('ImportKredit saved successfully', [
                'id' => $model->id,
                'no_rekening' => $model->no_rekening,
            ]);
        });
    }
}