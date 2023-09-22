<?php

namespace App\BusinessLogic;

class Options
{
    private function __construct(private string $commandString, private array $options)
    {
        readEnvFile();
    }

    public static function parseCommandLineArgs(int $argc, array $argv)
    {
        if ($argc == 1) {
            App::getInstance()->printHelp();
            return null;
        }

        $command = $argv[1];
        $options = [];

        for ($i = 2; $i < $argc; $i++) {
            $arg = $argv[$i];
            
            if (!str_starts_with($arg, '--')) {
                App::getInstance()->printInvalidOptions($arg);
                return null;
            }

            $arg = ltrim($arg, '--');
            $argArray = explode('=', $arg);

            if (count($argArray) > 2) {
                App::getInstance()->printInvalidOptions($arg);
                return null;
            }

            $options[$argArray[0]] = true;
            if (!empty($argArray[1])) {
                $options[$argArray[0]] = $argArray[1];
            }

            unset($arg);
        }

        return new self($command, $options);
    }

    public function getCommandString(): string
    {
        return $this->commandString;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
