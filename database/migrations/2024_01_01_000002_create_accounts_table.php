<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name', 100);
            $table->enum('type', [
                'cash', 'bank', 'customer', 'partner', 
                'income', 'expense', 'liability', 
                'equity', 'revenue', 'cost_of_sales'
            ]);
            $table->foreignId('currency_id')->constrained('currencies')->onDelete('restrict');
            $table->boolean('is_active')->default(true);
            $table->string('description')->nullable();
            $table->timestamps();
            
            $table->index('type');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};