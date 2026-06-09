<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_approved')->default(false)->after('is_available');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('accepted_at')->nullable()->after('status');
            $table->index(['status', 'printer_id']);
        });

        // Imprimeurs existants : approuvés par défaut
        DB::table('users')->where('role', 'printer')->update(['is_approved' => true]);

        // Commandes en attente : retirer l'attribution automatique (file d'attente ouverte)
        DB::table('orders')
            ->where('status', 'pending')
            ->update(['printer_id' => null, 'accepted_at' => null]);
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['status', 'printer_id']);
            $table->dropColumn('accepted_at');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_approved');
        });
    }
};
