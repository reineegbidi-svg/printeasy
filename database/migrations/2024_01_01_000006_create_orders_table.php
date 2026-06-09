<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('printer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('service_type', ['print', 'photocopy', 'scan']);
            $table->enum('format', ['A4', 'A3', 'A5', 'letter'])->default('A4');
            $table->enum('color_mode', ['color', 'bw'])->default('bw');
            $table->unsignedInteger('quantity')->default(1);
            $table->unsignedInteger('pages')->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->enum('status', [
                'pending', 'accepted', 'in_progress', 'completed', 'rejected', 'cancelled',
            ])->default('pending');
            $table->enum('payment_method', ['online', 'on_delivery'])->default('on_delivery');
            $table->enum('payment_status', ['unpaid', 'pending', 'paid', 'refunded'])->default('unpaid');
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->text('notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'created_at']);
            $table->index(['user_id', 'status']);
            $table->index(['printer_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
