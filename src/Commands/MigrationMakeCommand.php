<?php

namespace Craftsman\Commands;

use Craftsman\BusinessLogic\App;

use function Craftsman\join_paths;

class MigrationMakeCommand extends Command
{
    public function __construct()
    {
        parent::__construct('make');
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

        if (empty($options['name'])) {
            App::getInstance()->printMissingOptions('name');
            return false;
        }

        $migrationDirName = date('YmdHi') . '-' . $options['name'];
        $currentMigrationDir = join_paths($migrationsDir, $migrationDirName);

        if (file_exists($currentMigrationDir)) {
            printf("Migration already exists\n");
            return false;
        }
        if (!mkdir($currentMigrationDir, 0777, true)) {
            printf("Could not create directory: %s\n", $currentMigrationDir);
            return false;
        }

        $upFileName = join_paths($currentMigrationDir, 'up.sql');
        $downFileName = join_paths($currentMigrationDir, 'down.sql');

        $upFile = fopen($upFileName, 'w+');
        if (!$upFile) {
            printf("Could not create file: %s\n", $upFileName);
            return false;
        }

        fclose($upFile);

        $downFile = fopen($downFileName, 'w+');
        if (!$upFile) {
            printf("Could not create file: %s\n", $downFileName);
            return false;
        }

        fclose($downFile);

        printf("Migration \"%s\" created\n", $migrationDirName);

        return true;
    }
}
