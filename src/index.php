<?php

/**
 * craftsman.php
 *
 * Poor mans' artisan
 *
 * A script to help with a few common things, such as:
 *     - Migrations
 *     - Symlinking storage to public folder
 *
 * @package   craftsman.php
 * @author    kasouza
 * @copyright 2023 kasouza
 * @license   https://opensource.org/license/mit/ MIT
 * @version   CVS: 1.0.0
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\BusinessLogic\App;
use App\Commands\HelpCommand;
use App\Commands\MigrationMakeCommand;
use App\Commands\MigrationMigrateCommand;
use App\Commands\MigrationRollbackCommand;
use App\Commands\MigrationSetupCommand;
use App\Commands\StorageLinkCommand;

App::getInstance()
    ->withCommands(
        'help', [
        new HelpCommand(),
        ]
    )
    ->withCommands(
        'migration', [
            new MigrationMakeCommand(),
            new MigrationMigrateCommand(),
            new MigrationRollbackCommand(),
            new MigrationSetupCommand(),
        ]
    )
    ->withCommands(
        'storage', [
        new StorageLinkCommand()
        ]
    )
    ->run($argc, $argv);
