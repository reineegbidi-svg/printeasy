<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pricing_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('printer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('service_type', ['print', 'photocopy', 'scan']);
            $table->enum('format', ['A4', 'A3', 'A5', 'letter'])->default('A4');
            $table->enum('color_mode', ['color', 'bw'])->default('bw');
            $table->decimal('price_per_unit', 10, 2);
            $table->decimal('setup_fee', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['printer_id', 'service_type', 'format', 'color_mode'], 'pricing_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pricing_rules');
    }
};
