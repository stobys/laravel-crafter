<?php

namespace SylveK\LaravelCrafter;

use Illuminate\Support\ServiceProvider;

class LaravelCrafterServiceProvider extends ServiceProvider
{
    public function register()
    {
    }

    public function boot()
    {
        // -- register commands
        $this->commands([
            Console\Commands\CrafterCommand::class,
        ]);
    }
}
