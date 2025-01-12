<?php

declare(strict_types=1);

namespace Alocasia;

use Alocasia\Controller\FileController;
use Alocasia\Controller\HelpMessageController;
use Alocasia\Controller\IController;
use Alocasia\Controller\InteractiveController;
use Alocasia\Controller\OneLinerController;

readonly class Router
{
    private int $numberOfArgs;

    /** @var non-empty-array<int, string> $args */
    private array $args;

    /**
     * @param int $numberOfArgs
     * @param non-empty-array<int, string> $args
     */
    public function __construct(
        int $numberOfArgs,
        array $args
    ) {
        $this->numberOfArgs = $numberOfArgs;
        $this->args = $args;
    }

    /**
     * @return IController
     */
    public function route(): IController {
        return match ($this->numberOfArgs) {
            2 => match ($this->args[1]) {
                "-i" | "--interactive" => new InteractiveController(),
                "-h" | "--help" => new HelpMessageController(),
                default => new FileController(file_path: $this->args[1]),
            },
            3 => match ($this->args[1]) {
                "-o" | "--oneliner" => new OnelinerController(src: $this->args[2]),
                default => new HelpMessageController(),
            },
            default => new HelpMessageController(),
        };
    }
}
