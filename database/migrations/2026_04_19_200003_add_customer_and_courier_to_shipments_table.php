<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->after('organization_id')->constrained()->nullOnDelete();
            $table->foreignId('assigned_user_id')->nullable()->after('created_by_user_id')->constrained('users')->nullOnDelete();
            $table->index(['organization_id', 'assigned_user_id']);
        });
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['assigned_user_id']);
            $table->dropColumn(['customer_id', 'assigned_user_id']);
        });
    }
};
