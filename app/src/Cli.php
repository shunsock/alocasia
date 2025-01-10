<?php

declare(strict_types=1);

namespace Alocasia;

class Cli
{
    public function __construct() {
        echo "Cli Object is constructed\n";
    }

    /**
     * @return void
     */
    public function run(): void {
        echo "Cli is running\n";
    }
}