<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->foreignId('sender_customer_id')->constrained('customers')->cascadeOnDelete();
            $table->string('receiver_name', 100);
            $table->string('receiver_phone', 20)->nullable();
            $table->foreignId('partner_id')->constrained('partners')->cascadeOnDelete();
            $table->decimal('amount', 18, 4);
            $table->foreignId('currency_id')->constrained('currencies')->cascadeOnDelete();
            $table->decimal('fee', 18, 4)->default(0);
            $table->enum('status', ['pending', 'sent', 'paid', 'settled', 'cancelled'])->default('pending');
            $table->string('otp_code', 10)->nullable();
            $table->timestamp('otp_expires_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('status');
            $table->index('code');
            $table->index('sender_customer_id');
            $table->index('partner_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};