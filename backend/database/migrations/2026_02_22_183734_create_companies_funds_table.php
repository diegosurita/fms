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
        Schema::create('companies_funds', function (Blueprint $table) {
            $table->foreignId('company')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('fund');

            $table->unique(['company', 'fund']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies_funds');
    }
};
