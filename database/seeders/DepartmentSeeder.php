<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

/**
 * Departamentos de Colombia (código DANE en external_id, ISO 3166-1 alpha-2 CO).
 * Fuente de datos: database/data/colombia/departments.json
 */
class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('data/colombia/departments.json');

        if (! is_file($path)) {
            $this->command?->error('No se encontró '.$path);

            return;
        }

        /** @var array{data?: list<array{id: int, name: string}>} $payload */
        $payload = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);

        foreach ($payload['data'] ?? [] as $row) {
            Department::firstOrCreate(
                [
                    'country_code' => 'CO',
                    'external_id' => $row['id'],
                ],
                [
                    'name' => $row['name'],
                ]
            );
        }
    }
}
