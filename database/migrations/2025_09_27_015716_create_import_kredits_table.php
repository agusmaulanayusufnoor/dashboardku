<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('import_kredits', function (Blueprint $table) {
            $table->id();
            $table->string('no_rekening', 20);
            $table->string('no_alternatif', 20);
            $table->string('nama_nasabah', 200);
            $table->text('alamat');
            $table->text('desa');
            $table->date('tgl_realisasi');
            $table->integer('jkw');
            $table->date('tgl_jt');
            $table->integer('type_kredit');
            $table->decimal('suku_bunga', 4, 2);
            $table->decimal('jml_pinjaman', 12, 2);
            $table->decimal('baki_debet', 12, 2);
            $table->decimal('t_pokok', 12, 2);
            $table->integer('ftp');
            $table->integer('ftp_hari');
            $table->decimal('t_bunga', 12, 2);
            $table->integer('ftb');
            $table->integer('ftb_hari');
            $table->integer('jt');
            $table->string('kol_bln_lalu', 3);
            $table->string('kol_bln_ini', 3);
            $table->decimal('tag_pokok_bln_ini', 12, 2);
            $table->decimal('tag_bunga_bln_ini', 12, 2);
            $table->decimal('total_tagihan', 12, 2);
            $table->decimal('set_pokok_bln_ini', 12, 2);
            $table->decimal('set_bunga_bln_ini', 12, 2);
            $table->decimal('total_setoran', 12, 2);
            $table->string('norek_tab', 20);
            $table->decimal('saldo_tab', 12, 2);
            $table->decimal('pencapaian', 12, 2);
            $table->decimal('ppka_bln_lalu', 12, 2);
            $table->decimal('ppka_bln_ini', 12, 2);
            $table->decimal('selisih_ppka', 12, 2);
            $table->string('kode_group1', 200);
            $table->string('kode_group2', 200);
            $table->string('kode_group3', 200);
            $table->string('kode_group4', 200);
            $table->string('kode_group5', 200);
            $table->integer('sisa_jkw_bln');
            $table->string('kode_kantor', 100);
            $table->string('kode_produk', 100);
            $table->string('no_tlp_nasabah', 20);
            $table->string('no_ktp', 20);
            $table->string('kecamatan', 100);
            $table->string('nasabah_id', 20);
            $table->string('flag', 200);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_kredits');
    }
};