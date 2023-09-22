<?php

namespace Craftsman\BusinessLogic;

class App
{
    private static ?App $instance = null;

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new App();
        }

        return self::$instance;
    }

    private CommandManager $commandManager;

    public function __construct()
    {
        $this->commandManager = new CommandManager();
    }

    public function printHelp()
    {
        printf("How to use:\n");
        printf("\tphp util.php <command_namespace>:<command_name> [--option=value] [--boolean_option]\n\n");

        printf("Available commands:\n");

        foreach ($this->commandManager->getCommands() as $namespace=>$commands) {
            foreach ($commands as $command) {
                printf("\t%s:%s\n", $namespace, $command->getName());
            }

            printf("\n");
        }
    }

    public function printInvalidOptions(string $option)
    {
        printf("Invalid option: %s\n", $option);
    }

    public function printMissingOptions(string $option)
    {
        printf("Missing option: %s\n", $option);
    }

    public function run(int $argc, array $argv)
    {
        $options = Options::parseCommandLineArgs($argc, $argv);
        if ($options === null) {
            return;
        }

        if (!$this->commandManager->execCommandString($options->getCommandString(), $options->getOptions())) {
            return;
        }
    }


    public function withCommands(string $namespace, array $commands): App
    {
        foreach ($commands as $command) {
            $this->commandManager->register($namespace, $command);
        }

        return $this;
    }
}
