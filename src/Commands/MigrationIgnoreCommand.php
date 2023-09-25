<?php

namespace Craftsman\Commands;

use Craftsman\Facades\MigrationFacade;

use function Craftsman\getDbConnection;
use function Craftsman\read_dir_custom;

class MigrationIgnoreCommand extends Command
{
    public function __construct()
    {
        parent::__construct('ignore');
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
            $migrationName = MigrationFacade::getMigrationName($dir);
            if (!empty($ranMigrations[$migrationName])) {
                continue;
            }

            $newMigrations[] = $migrationName;
            printf("Ignored - %s\n", $migrationName);
        }

        if (!empty($newMigrations)) {
            MigrationFacade::insertMigrations($mysqli, $migrationsTableName, $newMigrations, $ranMigrations);
            printf("Ignored migrations sucessfully\n");
        } else {
            printf("Nothing to ignore\n");
        }

        return true;
    }
}
