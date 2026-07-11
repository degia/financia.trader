<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('balance_history', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->decimal('balance', 16, 2);
            $table->decimal('equity', 16, 2)->nullable();
            $table->decimal('deposit', 16, 2)->default(0);
            $table->decimal('withdrawal', 16, 2)->default(0);
            $table->string('notes', 255)->nullable();
            $table->timestamps();

            $table->unique('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('balance_history');
    }
};
