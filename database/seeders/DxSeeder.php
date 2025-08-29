<?php

namespace Database\Seeders;

use App\Models\Diagnosis;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class DxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Diagnosis::truncate();
        $csv_file = database_path('src/dxs.csv');

        if (! File::exists($csv_file)) {
            throw new \RuntimeException("CSV file not found: {$csv_file}");
        }

        $handle = fopen($csv_file, 'r');

        // Skip header row
        fgetcsv($handle);

        $records = [];
        $record_count = 0;
        $total_record_count = 0;

        while (($data = fgetcsv($handle)) !== false) {

            $title = trim(str_replace('- ', '', $data[1]));
            if (in_array($title, ['_NOCODEASSIGNED', 'CODE'])) {
                continue;
            }

            if ($record_count++ < 500) {
                $total_record_count++;
                $records[] = [
                    'code' => $data[0],
                    'title' => $title,
                    'description' => '',
                ];

                continue;
            }
            Diagnosis::factory()->createMany($records);
            $records = [];

            echo $total_record_count.PHP_EOL;
            $record_count = 0;
        }

        fclose($handle);
    }
}
