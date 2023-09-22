<?php

/**
 * CONFIGS
 */

define('CONF_ENV_VAR_NAME_DB_HOST', 'DB_HOST');
define('CONF_ENV_VAR_NAME_DB_USER', 'DB_USER');
define('CONF_ENV_VAR_NAME_DB_PASS', 'DB_PASS');
define('CONF_ENV_VAR_NAME_DB_NAME', 'DB_NAME');
define('CONF_ENV_VAR_NAME_DB_PORT', 'DB_PORT');
define('CONF_ENV_VAR_NAME_STORAGE_LINK_TARGET', 'STORAGE_LINK_TARGET');
define('CONF_ENV_VAR_NAME_STORAGE_LINK', 'STORAGE_LINK');

define('CONF_STORAGE_LINK_TARGET', __DIR__ . config(CONF_ENV_VAR_NAME_STORAGE_LINK, true, '/storage'));
define('CONF_STORAGE_LINK', __DIR__ . config(CONF_ENV_VAR_NAME_STORAGE_LINK, true, '/public/storage'));


function readEnvFile()
{
    $envFile = __DIR__ . '/.env';

    if (!file_exists($envFile)) {
        printf(".env file not found\n");
        die();
    }

    $contents = file_get_contents($envFile);

    $lines = explode("\n", $contents);

    global $_ENV;
    $_ENV = [];

    foreach ($lines as $line) {
        $configArray = explode('=', $line);

        if (empty($configArray[0])) {
            continue;
        }

        $key = $configArray[0];
        $value = '';

        if (isset($configArray[1])) {
            $value = $configArray[1];

            $value = trim(str_replace([ '"', "'" ], '', $value));

            if ($value === 'true') {
                $value = true;
            } else if ($value === 'false') {
                $value = false;
            } else if (is_numeric($value)) {
                $intVal =  intval($value);
                $floatVal = floatval($value);

                if ($intVal != $floatVal) {
                    $value = $floatVal;
                } else {
                    $value = $intVal;
                }
            }
        }

        $_ENV[$key] = $value;
    }
}

function config(string $name, bool $optional = false, mixed $default = null)
{
    if (!isset($_ENV[$name])) {
        if ($optional) {
            return $default;
        }

        printf("Config not set in .env file: \"%s\"\n", $name);
        die();
    }

    return $_ENV[$name];
}

/**
 * END CONFIGS
 */
