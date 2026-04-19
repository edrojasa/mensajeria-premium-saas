<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();

            $table->string('tracking_number', 64);

            $table->string('sender_name');
            $table->string('sender_phone', 32);
            $table->string('sender_email')->nullable();

            $table->string('recipient_name');
            $table->string('recipient_phone', 32);
            $table->string('recipient_email')->nullable();

            $table->string('origin_address_line');
            $table->string('origin_city', 120);
            $table->string('origin_region', 120)->nullable()->comment('Departamento / estado');
            $table->string('origin_postal_code', 32)->nullable();

            $table->string('destination_address_line');
            $table->string('destination_city', 120);
            $table->string('destination_region', 120)->nullable();
            $table->string('destination_postal_code', 32)->nullable();

            $table->string('reference_internal', 120)->nullable()->comment('Referencia interna del cliente');
            $table->text('notes_internal')->nullable()->comment('Notas internas operativas');

            $table->decimal('weight_kg', 10, 3)->nullable();
            $table->decimal('declared_value', 14, 2)->nullable()->comment('Valor declarado en moneda local');

            $table->string('status', 32);

            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['organization_id', 'tracking_number']);
            $table->index(['organization_id', 'created_at']);
            $table->index(['organization_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
