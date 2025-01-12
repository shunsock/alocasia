<?php

declare(strict_types=1);

namespace Alocasia\Controller;

class HelpMessageController implements IController
{
    public function run(): void {
        echo "usage main.php file, main.php -i interactive mode, main.php -o oneliner mode\n";
    }
}