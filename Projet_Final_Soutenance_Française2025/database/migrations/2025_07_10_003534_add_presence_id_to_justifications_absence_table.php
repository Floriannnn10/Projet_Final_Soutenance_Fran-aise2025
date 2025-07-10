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
        Schema::table('justifications_absence', function (Blueprint $table) {
            $table->foreignId('presence_id')->nullable()->constrained('presences')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('justifications_absence', function (Blueprint $table) {
            $table->dropForeign(['presence_id']);
            $table->dropColumn('presence_id');
        });
    }
};
