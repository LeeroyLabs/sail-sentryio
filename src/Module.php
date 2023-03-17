<?php

namespace Leeroy\SentryIO;

use SailCMS\Collection;
use SailCMS\Contracts\AppModule;
use SailCMS\Types\ModuleInformation;

class Module implements AppModule
{
    public function info(): ModuleInformation
    {
        Sentry::init();
        return new ModuleInformation(
            'SentryIO', 
            'SailCMS official Sentry.io Module', 
            1.0,
            '1.0.0',
            'LeeroyLabs', 
            'https://github.com/orgs/LeeroyLabs/repositories'
        );
    }

    public function cli(): Collection
    {
        return Collection::init();
    }

    public function middleware(): void
    {
    }

    public function events(): void
    {
        // register for events
    }

    // Your code
}
