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
        Schema::table('check_errors', function (Blueprint $table) {
            $table->foreignId('service_id')->constrained('services');
            $table->enum('from_status', ['AVAILABLE', 'PARTIAL', 'OUTAGE', 'MAINTENANCE'])->nullable();
            $table->enum('to_status', ['AVAILABLE', 'PARTIAL', 'OUTAGE', 'MAINTENANCE'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('check_errors', function (Blueprint $table) {
            $table->dropForeign(['service_id']);
            $table->dropColumn('service_id');
            $table->dropColumn('from_status');
            $table->dropColumn('to_status');
        });
    }
};
