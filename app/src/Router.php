<?php

declare(strict_types=1);

namespace Alocasia;

use Alocasia\Controller\ControllerOfInterpretingFromFile;
use Alocasia\Controller\ControllerOfInterpretingFromOneLineExpression;
use Alocasia\Controller\ControllerOfInterpretingInteractively;
use Alocasia\Controller\ControllerOfShowingHelpMessage;
use Alocasia\Controller\IController;

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
        int   $numberOfArgs,
        array $args
    )
    {
        $this->numberOfArgs = $numberOfArgs;
        $this->args = $args;
    }

    /**
     * @return IController
     */
    public function route(): IController
    {
        return match ($this->numberOfArgs) {
            2 => match ($this->args[1]) {
                "-i", "--interactive" => new ControllerOfInterpretingInteractively(),
                '-h', '--help' => new ControllerOfShowingHelpMessage(),
                default => new ControllerOfInterpretingFromFile(file_path: $this->args[1]),
            },
            3 => match ($this->args[1]) {
                "-o", "--oneliner" => new ControllerOfInterpretingFromOneLineExpression(src: $this->args[2]),
                default => new ControllerOfShowingHelpMessage(),
            },
            default => new ControllerOfShowingHelpMessage(),
        };
    }
}
