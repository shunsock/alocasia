<?php

declare(strict_types=1);

namespace Alocasia\Controller;

readonly class OneLinerController implements IController
{
    public string $src;

    public function __construct(string $src) {
        $this->src = $src;
    }
    public function run(): void {
        echo "OneLinerController Running...\n";
    }
}