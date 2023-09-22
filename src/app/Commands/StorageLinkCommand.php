<?php

namespace App\Commands;

class StorageLinkCommand extends Command
{
    public function __construct()
    {
        parent::__construct('link');
    }

    public function exec(array $options): bool
    {
        $target = CONF_STORAGE_LINK_TARGET;
        $storageLink = CONF_STORAGE_LINK;

        if (!empty($options['target'])) {
            $target = $options['target'];
        }

        if (!empty($options['link'])) {
            $storageLink = $options['link'];
        }

        if (!symlink($target, $storageLink)) {
            printf("Could not symlink storage\n");
            return false;
        }

        printf("Storage linked successfully\n");

        return true;
    }
}
