<?php

namespace Craftsman\Commands;

use Craftsman\Facades\MigrationFacade;
use mysqli_sql_exception;

use function Craftsman\getDbConnection;
use function Craftsman\join_paths;
use function Craftsman\read_dir_custom;

class MigrationRollbackCommand extends Command
{
    public function __construct()
    {
        parent::__construct('rollback');
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
        $oldMigrations = MigrationFacade::getMigrations($mysqli, $migrationsTableName);

        $currentBatch = -1;
        foreach ($oldMigrations as $currentMigration) {
            if (intval($currentMigration->batch) > $currentBatch) {
                $currentBatch = intval($currentMigration->batch);
            }
        }

        $dirs = read_dir_custom($migrationsDir, true);
        $removedMigrations = [];

        $ok = true;

        foreach($dirs as $dir) {
            $filename = join_paths($migrationsDir, $dir, 'down.sql');
            $query = file_get_contents($filename);

            $migrationName = MigrationFacade::getMigrationName($dir);
            if (empty($oldMigrations[$migrationName])) {
                continue;
            }

            $migration = $oldMigrations[$migrationName];
            if ($migration->batch != $currentBatch) {
                continue;
            }

            if (!$query) {
                printf("File not found \"%s\"\n", $filename);
                return false;
            }

            try {
                $result = $mysqli->multi_query($query);
                $mysqli->store_result();
                if ($result === false) {
                    $ok = false;
                    break;
                }
            } catch (mysqli_sql_exception $e) {
                printf("ERROR: %s\n", $e->getMessage());
                $ok = false;
                break;
            }

            printf("Rolled back - %s\n", $migrationName);
            $removedMigrations[] = $migrationName;
        }

        if (!empty($removedMigrations)) {
            $result = $mysqli->query("DELETE FROM {$migrationsTableName} WHERE batch = {$currentBatch}");
            if ($result === false) {
                printf("Could not remove migrations from migrations table\n");
                return false;
            }

            if ($ok) {
                printf("Rolled back sucessfully\n");
            } else {
                printf("Errors occured, but some migrations succeeded\n");
            }

            printf("Rolled back sucessfully\n");
        } else {
            if ($ok) {
                printf("Nothing to rollback\n");
            } else {
                printf("Could not rollback\n");
            }
        }

        return true;
    }
}
