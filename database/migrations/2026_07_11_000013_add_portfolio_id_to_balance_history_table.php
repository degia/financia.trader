<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('balance_history', function (Blueprint $table) {
            $table->foreignId('portfolio_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
        });

        // Drop the single-column unique and replace with composite
        Schema::table('balance_history', function (Blueprint $table) {
            $table->dropUnique('balance_history_date_unique');
            $table->unique(['portfolio_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::table('balance_history', function (Blueprint $table) {
            $table->dropForeign(['portfolio_id']);
            $table->dropColumn('portfolio_id');
        });
    }
};
