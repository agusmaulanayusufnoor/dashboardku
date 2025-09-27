<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('import_kredits', function (Blueprint $table) {
            $table->date('tgl_report')->after('flag')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('import_kredits', function (Blueprint $table) {
            $table->dropColumn('tgl_report');
        });
    }
};