<?php

declare(strict_types=1);

namespace Alocasia\Controller;

readonly class FileController implements IController
{
    public string $file_path;
    public function __construct(string $file_path) {
        $this->file_path = $file_path;
    }
    public function run(): void {
        echo "FileController Running...\n";
    }
}