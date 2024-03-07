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
        Schema::create('dns_checks', function (Blueprint $table) {
            $table->id();
            $table->string('domain');
            $table->foreignId('service_id')->constrained('services');
            $table->string('ipv4_match')->nullable();
            $table->string('ipv6_match')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dns_checks');
    }
};
