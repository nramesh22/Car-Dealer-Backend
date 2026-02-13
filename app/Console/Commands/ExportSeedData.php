<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ExportSeedData extends Command
{
    protected $signature = 'export:seed-data {--tables=} {--exclude=} {--output=}';
    protected $description = 'Export current database data to a seeder file.';

    public function handle(): int
    {
        $tablesOption = $this->option('tables');
        $excludeOption = $this->option('exclude');
        $outputPath = $this->option('output') ?: database_path('seeders/ExportedDataSeeder.php');

        $defaultExcludes = ['migrations', 'password_reset_tokens'];
        $excludes = $excludeOption
            ? array_filter(array_map('trim', explode(',', $excludeOption)))
            : $defaultExcludes;

        $tables = $tablesOption
            ? array_filter(array_map('trim', explode(',', $tablesOption)))
            : $this->getAllTables();

        $tables = array_values(array_diff($tables, $excludes));

        if (empty($tables)) {
            $this->error('No tables selected for export.');
            return 1;
        }

        $seedStatements = [];
        foreach ($tables as $table) {
            $rows = DB::table($table)->get();
            if ($rows->isEmpty()) {
                continue;
            }

            $exportRows = $rows->map(function ($row) {
                return (array) $row;
            })->all();

            $rowsExport = var_export($exportRows, true);
            $seedStatements[] = "        DB::table('{$table}')->insert({$rowsExport});";
        }

        $seedBody = empty($seedStatements)
            ? "        // No data found to export.\n"
            : implode("\n\n", $seedStatements) . "\n";

        $content = $this->buildSeederFile($seedBody);
        File::put($outputPath, $content);

        $this->info("Seeder exported to: {$outputPath}");
        $this->info('Run it with: php artisan db:seed --class=ExportedDataSeeder');

        return 0;
    }

    private function getAllTables(): array
    {
        $driver = DB::getDriverName();
        if ($driver !== 'mysql') {
            $this->warn("Driver '{$driver}' not explicitly supported; falling back to schema tables list.");
        }

        $results = DB::select('SHOW TABLES');
        if (empty($results)) {
            return [];
        }

        $firstRow = (array) $results[0];
        $columnName = array_key_first($firstRow);

        $tables = [];
        foreach ($results as $row) {
            $rowArray = (array) $row;
            if (isset($rowArray[$columnName])) {
                $tables[] = $rowArray[$columnName];
            }
        }

        return $tables;
    }

    private function buildSeederFile(string $seedBody): string
    {
        return <<<PHP
<?php

namespace Database\\Seeders;

use Illuminate\\Database\\Seeder;
use Illuminate\\Support\\Facades\\DB;

class ExportedDataSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }

{$seedBody}        if (DB::getDriverName() === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }
}
PHP;
    }
}
