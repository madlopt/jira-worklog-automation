<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

try {
    App\Kernel::run();
    App\Worklog\Command\LogWorkCommand::call();
} catch (Exception $e) {
    echo $e->getMessage();
    exit(1);
}
