<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settlements', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->foreignId('partner_id')->constrained('partners')->cascadeOnDelete();
            $table->decimal('amount', 18, 4);
            $table->foreignId('currency_id')->constrained('currencies')->cascadeOnDelete();
            $table->enum('type', ['cash', 'bank', 'adjustment'])->default('cash');
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('completed');
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->index('status');
            $table->index('partner_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settlements');
    }
};