<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clt_layups', function (Blueprint $table) {
            $table->string('species_grade')->nullable()->after('name');
            $table->unsignedInteger('revision')->default(1)->after('species_grade');
        });
    }

    public function down(): void
    {
        Schema::table('clt_layups', function (Blueprint $table) {
            $table->dropColumn('species_grade');
            $table->dropColumn('revision');
        });
    }
};
