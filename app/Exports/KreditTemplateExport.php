<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class KreditTemplateExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return [
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
        ];
    }

    public function array(): array
    {
        return [
            [
                '001', // no_rekening
                'ALT001', // no_alternatif
                'Ahmad Yani', // nama_nasabah
                'Jl. Merdeka No. 123', // alamat
                'Desa Contoh', // desa
                '2023-01-15', // tgl_realisasi
                12, // jkw
                '2024-01-15', // tgl_jt
                1, // type_kredit
                12.5, // suku_bunga
                10000000, // jml_pinjaman
                8500000, // baki_debet
                125000, // t_pokok
                10, // ftp
                30, // ftp_hari
                125000, // t_bunga
                10, // ftb
                30, // ftb_hari
                10, // jt
                '0', // kol_bln_lalu
                '0', // kol_bln_ini
                833333.33, // tag_pokok_bln_ini
                104166.67, // tag_bunga_bln_ini
                937500, // total_tagihan
                833333.33, // set_pokok_bln_ini
                104166.67, // set_bunga_bln_ini
                937500, // total_setoran
                'TAB001', // norek_tab
                5000000, // saldo_tab
                85, // pencapaian
                10000000, // ppka_bln_lalu
                8500000, // ppka_bln_ini
                -1500000, // selisih_ppka
                'GROUP1', // kode_group1
                'GROUP2', // kode_group2
                'GROUP3', // kode_group3
                'GROUP4', // kode_group4
                'GROUP5', // kode_group5
                11, // sisa_jkw_bln
                'K001', // kode_kantor
                'PROD001', // kode_produk
                '08123456789', // no_tlp_nasabah
                '3201012345678901', // no_ktp
                'Kecamatan Contoh', // kecamatan
                'NAS001', // nasabah_id
                'AKTIF', // flag
            ],
        ];
    }
}