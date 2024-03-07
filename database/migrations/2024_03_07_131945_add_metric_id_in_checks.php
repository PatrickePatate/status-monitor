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
        Schema::table('dns_checks', function (Blueprint $table) {
            $table->foreignId('metric_id')->nullable()->constrained('metrics');
        });
        Schema::table('http_checks', function (Blueprint $table) {
            $table->foreignId('metric_id')->nullable()->constrained('metrics');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dns_checks', function (Blueprint $table) {
            $table->dropForeign(['metric_id']);
            $table->dropColumn('metric_id');
        });
        Schema::table('http_checks', function (Blueprint $table) {
            $table->dropForeign(['metric_id']);
            $table->dropColumn('metric_id');
        });
    }
};
