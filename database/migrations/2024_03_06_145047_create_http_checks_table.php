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
        Schema::create('http_checks', function (Blueprint $table) {
            $table->id();
            $table->text('url');
            $table->enum('method', ['get','post']);
            $table->json('request_args')->nullable();
            $table->foreignId('service_id')->constrained('services');
            $table->integer('http_code')->nullable()->comment('If null, do not check');
            $table->longText('http_body')->nullable()->comment('If null, do not check');
            $table->boolean('check_cert')->default(false);
            $table->json('provide_headers')->nullable()->comment('If null, do not use it to check');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('http_checks');
    }
};
