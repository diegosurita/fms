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
        Schema::create('duplicated_funds', function (Blueprint $table) {
            $table->foreignId('source_fund_id')->constrained('funds');
            $table->foreignId('duplicated_fund_id')->constrained('funds');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('duplicated_funds');
    }
};
