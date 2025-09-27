<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // pastikan kolom ditambahkan setelah email atau sesuai kebutuhan
            $table->foreignId('kantor_id')->nullable()->after('email')->constrained('kantor')->cascadeOnDelete();
            $table->foreignId('divisi_id')->nullable()->after('kantor_id')->constrained('divisi')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['kantor_id']);
            $table->dropColumn('kantor_id');

            $table->dropForeign(['divisi_id']);
            $table->dropColumn('divisi_id');
        });
    }
};
