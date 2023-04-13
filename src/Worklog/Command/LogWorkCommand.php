<?php

declare(strict_types=1);

namespace App\Worklog\Command;

use App\Worklog\Model\LogWork;
use Exception;

class LogWorkCommand
{
    /**
     * @return void
     */
    public static function call(): void
    {
        $currentDayOnly = false;
        $userEmail = '';
        $token = '';

        // Parse command line options
        $options = getopt("", ["email:", "token:", "current-day-only", "use-env-only", "help"]);

        // Check if help option is present
        if (isset($options['help'])) {
            echo "Usage: php bin/app.php [OPTIONS]\n";
            echo "Options:\n";
            echo "  --email=<user email>      Jira user email\n";
            echo "  --token=<API token>       Jira API token\n";
            echo "  --current-day-only        Log worklog for the current day only\n";
            echo "  --help                    Display this help message\n";
            exit(0);
        }

        // Check if email option is present
        if (isset($options['email'])) {
            $userEmail = $options['email'];
        }

        // Check if token option is present
        if (isset($options['token'])) {
            $token = $options['token'];
        }

        // Check if current-day-only option is present
        if (isset($options['current-day-only'])) {
            $currentDayOnly = $options['current-day-only'] === 'true';
        }

        // Don't prompt anything if use-env-only option is present, use only ENV variables
        if (isset($options['use-env-only'])) {
            $userEmail = getenv('WORKLOG_APP_JIRA_USER_EMAIL');
            $token = getenv('WORKLOG_APP_JIRA_API_TOKEN');
            $currentDayOnly = getenv('WORKLOG_APP_CURRENT_DAY_ONLY') === 'true';
        }

        // If email, token and current-day-only options were not passed as command-line arguments, prompt for them
        if (empty($userEmail)) {
            echo "Enter Jira user email (leave empty then the data from ENV variables will be used): ";
            $userEmail = trim(fgets(STDIN) ?: '');
            if (empty($userEmail)) {
                $userEmail = getenv('WORKLOG_APP_JIRA_USER_EMAIL');
            }
        }

        if (empty($token)) {
            echo "Enter Jira API token (leave empty then the data from ENV variables will be used): ";
            $token = trim(fgets(STDIN) ?: '');
            if (empty($token)) {
                $token = getenv('WORKLOG_APP_JIRA_API_TOKEN');
            }
        }

        if (empty($currentDayOnly) && !isset($options['current-day-only']) && !isset($options['use-env-only'])) {
            echo "Do you want to log worklog for the current day only? (y/n) ";
            $answer = strtolower(trim(fgets(STDIN) ?: ''));
            $currentDayOnly = ($answer === 'y');
        }

        if (empty($userEmail) || empty($token)) {
            echo "Missing required parameters. Put it in ENV variables or use it from console. Console usage: php bin/app.php --email=<user email> --token=<API token> --current-day-only=<true/false> [--use-env-only]\n";
            exit(1);
        }

        $responseDto = (new LogWork())->logFromCsv($userEmail, $token, $currentDayOnly);
        echo $responseDto->getMessage();
        if ($responseDto->isError()) {
            exit(1);
        }

        exit(0);
    }
}
