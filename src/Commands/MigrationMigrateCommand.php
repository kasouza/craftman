<?php

namespace Craftsman\Commands;

use Craftsman\Facades\MigrationFacade;
use mysqli_sql_exception;

use function Craftsman\getDbConnection;
use function Craftsman\join_paths;
use function Craftsman\read_dir_custom;

class MigrationMigrateCommand extends Command
{
    public function __construct()
    {
        parent::__construct('migrate');
    }

    public function exec(array $options): bool
    {
        $migrationsDir = getcwd() . DIRECTORY_SEPARATOR .'migrations';
        if (!empty($options['dir'])) {
            $migrationsDir = $options['dir'];
            if (!str_starts_with(DIRECTORY_SEPARATOR, '/')) {
                $migrationsDir = getcwd() . DIRECTORY_SEPARATOR . $migrationsDir;
            }
        }

        $mysqli = getDbConnection();

        $migrationsTableName = CONF_MIGRATIONS_TABLE;
        if (!empty($options['name'])) {
            $migrationsTableName = trim($options['name']);
        }

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

            try {
                $result = $mysqli->query($query);
                if ($result === false) {
                    return false;
                }
            } catch (mysqli_sql_exception $e) {
                printf("ERROR: %s\n", $e->getMessage());
                return false;
            }

            $newMigrations[] = $migrationName;
            printf("Migrated - %s\n", $migrationName);
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
