<?php

declare(strict_types=1);

namespace App;

use App\Infrastructure\Service\Config\LoadConfigService;

class Kernel
{
    public static function run(): void
    {
        putenv("WORKLOG_APP_BASE_PATH=" . __DIR__ . "/../");
        LoadConfigService::call();
    }
}
