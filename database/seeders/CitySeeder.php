<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Department;
use Illuminate\Database\Seeder;

/**
 * Municipios/ciudades asociados a departamentos (DIVIPOLA-like id en external_id).
 * Requiere haber ejecutado DepartmentSeeder antes.
 * Fuente: database/data/colombia/cities.json
 */
class CitySeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('data/colombia/cities.json');

        if (! is_file($path)) {
            $this->command?->error('No se encontró '.$path);

            return;
        }

        /** @var array{data?: list<array{id: int, name: string, departmentId: int}>} $payload */
        $payload = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);

        $skipped = 0;

        foreach ($payload['data'] ?? [] as $row) {
            $department = Department::query()
                ->where('country_code', 'CO')
                ->where('external_id', $row['departmentId'])
                ->first();

            if ($department === null) {
                $skipped++;

                continue;
            }

            City::firstOrCreate(
                [
                    'department_id' => $department->id,
                    'external_id' => $row['id'],
                ],
                [
                    'name' => $row['name'],
                ]
            );
        }

        if ($skipped > 0) {
            $this->command?->warn("CitySeeder: se omitieron {$skipped} registros por departamento desconocido.");
        }
    }
}
