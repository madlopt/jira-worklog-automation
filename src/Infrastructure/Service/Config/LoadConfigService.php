<?php

declare(strict_types=1);

namespace App\Infrastructure\Service\Config;

use App\Infrastructure\Exception\ConfigNotFoundException;

class LoadConfigService
{
    public static function call(): void
    {
        $configPath = getenv('WORKLOG_APP_BASE_PATH') . 'config/config.yaml';
        $config = yaml_parse_file($configPath);
        if (false === $config) {
            throw new ConfigNotFoundException('Config file not found: ' . $configPath);
        }

        foreach ($config as $key => $value) {
            $envKey = strtoupper($key);
            if (is_bool($value)) {
                $envValue = $value === true ? 'true' : 'false';
            } else {
                $envValue = $value;
            }

            putenv("$envKey=$envValue");
        }
    }
}
