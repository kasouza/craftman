<?php

namespace Craftsman\Commands;

use Craftsman\Facades\MigrationFacade;

class MigrationMigrateCommand extends Command
{
    public function __construct()
    {
        parent::__construct('migrate');
    }

    public function exec(array $options): bool
    {
        $migrationsDir = __DIR__ . DIRECTORY_SEPARATOR .'migrations';
        if (!empty($options['dir'])) {
            $migrationsDir = $options['dir'];
            if (!str_starts_with(DIRECTORY_SEPARATOR, '/')) {
                $migrationsDir = __DIR__ . DIRECTORY_SEPARATOR . $migrationsDir;
            }
        }

        $mysqli = getDbConnection();

        $migrationsTableName = 'migrations';
        $ranMigrations = MigrationFacade::getMigrations($mysqli, $migrationsTableName);

        $newMigrations = [];
        $dirs = read_dir_custom($migrationsDir);

        foreach($dirs as $dir) {
            $filename = join_paths($migrationsDir, $dir, 'up.sql');
            $query = file_get_contents($filename);

            $migrationName = MigrationFacade::getMigrationName($dir);
            if (!empty($ranMigrations[$migrationName])) {
                continue;
            }

            if (!$query) {
                printf("File not found \"%s\"\n", $filename);
                return false;
            }

            $result = $mysqli->query($query);
            if ($result === false) {
                return false;
            }

            $newMigrations[] = $migrationName;
            printf("Migrated back - %s\n", $migrationName);
        }

        if (!empty($newMigrations)) {
            MigrationFacade::insertMigrations($mysqli, $migrationsTableName, $newMigrations, $ranMigrations);
            printf("Migrated sucessfully\n");
        } else {
            printf("Nothing to migrate\n");
        }

        return true;
    }
}
