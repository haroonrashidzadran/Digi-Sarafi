<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partner_ledgers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->constrained('partners')->cascadeOnDelete();
            $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries')->nullOnDelete();
            $table->decimal('amount', 18, 4);
            $table->foreignId('currency_id')->constrained('currencies')->cascadeOnDelete();
            $table->enum('direction', ['debit', 'credit']);
            $table->string('description', 255)->nullable();
            $table->timestamps();
            
            $table->index('partner_id');
            $table->index('journal_entry_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partner_ledgers');
    }
};