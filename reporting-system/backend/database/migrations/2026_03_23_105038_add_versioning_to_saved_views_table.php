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
        Schema::table('saved_views', function (Blueprint $table) {
            $table->integer('version')->default(1)->after('config');
            $table->foreignId('parent_id')->nullable()->after('version')->constrained('saved_views')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('saved_views', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['version', 'parent_id']);
        });
    }
};
