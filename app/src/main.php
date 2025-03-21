<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Alocasia\Cli;
use Alocasia\Router;

$router = new Router(
    /** @var int $argc */
    numberOfArgs: $argc,
    /** @var string[] $argv */
    args: $argv
);

$cli = new Cli(router: $router);
$cli->run();
