<?php

declare(strict_types=1);

namespace App\Infrastructure\ValueObject;

enum JiraApiEndpoints: string
{
    case WorkLog = "/rest/api/3/issue/{issueKey}/worklog";
    case Search = "/rest/api/3/search";
}
