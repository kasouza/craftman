<?php

namespace Craftsman\Commands;

use Craftsman\BusinessLogic\App;

class HelpCommand extends Command
{
    public function __construct()
    {
        parent::__construct('help');
    }

    public function exec(array $options): bool
    {
        App::getInstance()->printHelp();
        return true;
    }
}
