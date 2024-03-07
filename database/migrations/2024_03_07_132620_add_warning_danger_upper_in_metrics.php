<?php

use App\Models\Metric;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('metrics', function (Blueprint $table) {
            DB::statement("ALTER TABLE metrics ALTER COLUMN warning_under TYPE integer USING (warning_under::integer);");
            $table->integer("warning_upper")->nullable();
            $table->integer("danger_upper")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('metrics', function (Blueprint $table) {
            DB::statement("ALTER TABLE metrics ALTER COLUMN warning_under TYPE varchar USING (warning_under::varchar);");
            $table->dropColumn("warning_upper");
            $table->dropColumn("danger_upper");
        });
    }
};
