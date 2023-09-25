<?php

namespace Craftsman\Commands;

use function Craftsman\getDbConnection;

class MigrationSetupCommand extends Command
{
    public function __construct()
    {
        parent::__construct('setup');
    }

    public function exec(array $options): bool
    {
        $mysqli = getDbConnection();
        $migrationsTableName = CONF_MIGRATIONS_TABLE;
        if (!empty($options['name'])) {
            $migrationsTableName = trim($options['name']);
        }

        $query = <<<SQL
            CREATE TABLE {$migrationsTableName} (
                id INT NOT NULL AUTO_INCREMENT,
                name TEXT,
                batch INT,
                PRIMARY KEY (id)
            );
        SQL;

        $mysqli->query($query);

        return true;
    }
}
