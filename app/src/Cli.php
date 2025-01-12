<?php

declare(strict_types=1);

namespace Alocasia;

readonly class Cli
{
    private Router $router;

    public function __construct(Router $router) {
        $this->router = $router;
    }

    /**
     * @return void
     */
    public function run(): void {
        $controller = $this->router->route();
        $controller->run();
    }
}