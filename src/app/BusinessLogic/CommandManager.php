<?php

namespace App\BusinessLogic;

use App\Commands\Command;
use Error;

class CommandManager
{
    private array $commands = [];

    /*
     * Regiter a command to be called when the command string is: <$namespace>:<$commandName>
     * @param string $namespace String to be used as the namespace for a command 
     * @param Command $command
     */
    public function register(string $namespace, Command $command)
    {
        $name = $command->getName();

        if (empty($this->commands[$namespace])) {
            $this->commands[$namespace] = [];
        }

        if (!empty($this->commands[$namespace][$name])) {
            throw new Error('Command has already been registered: ' . $name);
        }

        $this->commands[$namespace][$name] = $command;
    }

    /*
     * @param string $commandString in the format <$namespace>:<$commandName>
     * @param array<string, string> $options options to be passed to the Command exec function
     *
     * @return bool wheter or not the command execution was sucessfull
     *
     */
    public function execCommandString(string $commandString, array $options): bool
    {
        $commandArray = explode(':', trim($commandString));
        if (count($commandArray) !== 2) {
            return false;
        }

        $namespaceString = trim($commandArray[0]);
        $commandName = trim($commandArray[1]);

        if (empty($this->commands[$namespaceString])) {
            printf("Command not found: \"{$commandName}\"\n");
            return false;
        }

        if (empty($this->commands[$namespaceString][$commandName])) {
            printf("Command not found: \"{$commandName}\"\n");
            return false;
        }

        $command = $this->commands[$namespaceString][$commandName];

        return $command->exec($options);
    }

    public function getCommands(): array
    {
        return $this->commands;
    }
}
