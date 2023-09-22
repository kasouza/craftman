<?php

namespace Craftsman;

function join_paths()
{
    $paths = array();

    foreach (func_get_args() as $arg) {
        if ($arg !== '') { $paths[] = $arg; 
        }
    }

    return preg_replace('#/+#', '/', join('/', $paths));
}

function read_dir_custom(string $dir, bool $reverse = false)
{
    return array_values(array_diff(scandir($dir, $reverse ? 1 : 0), ['..', '.', '.gitkeep']));
}

function getDbConnection(): mysqli
{
    $host = config(CONF_ENV_VAR_NAME_DB_HOST);
    $user = config(CONF_ENV_VAR_NAME_DB_USER);
    $password = config(CONF_ENV_VAR_NAME_DB_PASS);
    $dbName = config(CONF_ENV_VAR_NAME_DB_NAME);
    $port = config(CONF_ENV_VAR_NAME_DB_PORT, true);

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $mysqli = new mysqli($host, $user, $password, $dbName, $port);
    return $mysqli;
}

