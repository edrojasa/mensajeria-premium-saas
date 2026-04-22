<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Asegura credenciales y estado de correo coherentes para el usuario administrador principal (desarrollo / recuperación).
     */
    public function up(): void
    {
        $email = 'andresrojas@rojastech.com.co';

        \App\Models\User::query()->where('email', $email)->update([
            'password' => Hash::make('123456'),
            'email_verified_at' => now(),
        ]);
    }

    /**
     * No revertimos hash de contraseña (no preservado).
     */
    public function down(): void
    {
        //
    }
};
