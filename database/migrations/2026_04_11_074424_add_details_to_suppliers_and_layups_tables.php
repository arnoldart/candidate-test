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
        Schema::table('supplier', function (Blueprint $table) {
            $table->string('primary_contact')->nullable()->after('name');
            $table->string('location')->nullable()->after('primary_contact');
            $table->string('material_certifications')->nullable()->after('location');
            $table->date('last_audit_date')->nullable()->after('material_certifications');
        });

        Schema::table('clt_layups', function (Blueprint $table) {
            $table->enum('status', ['Active', 'Draft', 'Archived'])->default('Draft')->after('name');
            $table->string('created_by')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplier', function (Blueprint $table) {
            $table->dropColumn(['primary_contact', 'location', 'material_certifications', 'last_audit_date']);
        });

        Schema::table('clt_layups', function (Blueprint $table) {
            $table->dropColumn(['status', 'created_by']);
        });
    }
};
