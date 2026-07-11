<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trades', function (Blueprint $table) {
            $table->id();
            $table->string('pair', 20);
            $table->string('trade_type', 20);
            $table->string('direction', 10);
            $table->decimal('entry_price', 16, 8);
            $table->decimal('exit_price', 16, 8)->nullable();
            $table->dateTime('entry_date');
            $table->dateTime('exit_date')->nullable();
            $table->decimal('size', 16, 8);
            $table->decimal('stop_loss', 16, 8)->nullable();
            $table->decimal('take_profit', 16, 8)->nullable();
            $table->decimal('pnl_amount', 16, 2)->default(0);
            $table->decimal('pnl_pips', 10, 2)->nullable();
            $table->string('outcome', 20)->default('open');
            $table->decimal('fees', 10, 2)->default(0);
            $table->string('strategy', 100)->nullable();
            $table->text('notes')->nullable();
            $table->string('screenshot_path', 255)->nullable();
            $table->timestamps();

            $table->index('pair');
            $table->index('outcome');
            $table->index('entry_date');
            $table->index('trade_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trades');
    }
};
