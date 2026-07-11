<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trades', function (Blueprint $table) {
            $table->foreignId('portfolio_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->foreignId('strategy_id')->nullable()->after('portfolio_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('trades', function (Blueprint $table) {
            $table->dropForeign(['portfolio_id']);
            $table->dropForeign(['strategy_id']);
            $table->dropColumn(['portfolio_id', 'strategy_id']);
        });
    }
};
