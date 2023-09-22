<?php

namespace App\Facades;

use mysqli;
use stdClass;

class MigrationFacade
{
    public static function getMigrationName(string $dir): string
    {
        $migrationArray = explode('-', $dir);
        return implode('-', array_slice($migrationArray, 1));
    }

    public static function getMigrations(mysqli $mysqli, string $migrationsTableName): array
    {
        $stmt = $mysqli->prepare('SELECT id, name, batch FROM ' . $migrationsTableName . ';');
        $stmt->execute();

        $migrations = [];

        $stmt->bind_result($id, $name, $batch);
        while ($stmt->fetch()) {
            $migrations[$name] = new stdClass;
            $migrations[$name]->name = $name;
            $migrations[$name]->batch = $batch;
        }

        return $migrations;
    }

    public static function insertMigrations(mysqli $mysqli, string $migrationsTableName, array $migrations, array $oldMigrations): void
    {
        $oldBatch = -1;
        foreach ($oldMigrations as $oldMigration) {
            if (intval($oldMigration->batch) > $oldBatch) {
                $oldBatch = intval($oldMigration->batch);
            }
        }

        $sql = "INSERT INTO {$migrationsTableName} (name, batch) VALUES";
        $currentBatch = $oldBatch + 1;

        foreach ($migrations as $idx=>$migration) {
            if ($idx > 0) {
                $sql .= ',';
            }

            $sql .= " ('{$migration}', {$currentBatch})";
        }

        $sql .= ';';

        $mysqli->query($sql);
    }
}
