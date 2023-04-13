<?php

declare(strict_types=1);

namespace App\Worklog\Service;

use App\Infrastructure\Http\Client;
use App\Worklog\Dto\RequestDto;
use DateTime;
use App\Infrastructure\ValueObject\JiraApiEndpoints;

class IssueWorklogDurationChecker
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * @param string $issueKey
     * @param string $user
     * @param string $auth
     * @param string $created
     * @param string $hoursToLog
     * @return bool
     */
    public function isItMoreThanDailyHoursLimit(string $issueKey, string $user, string $auth, string $created, string $hoursToLog): bool
    {
        $dailyHoursLimit = getenv('WORKLOG_APP_DAILY_WORKING_HOURS_LIMIT');
        $dailyHoursLimitInSeconds = intval(preg_replace("/[^0-9]/", "", $dailyHoursLimit)) * 3600;
        $hoursToLogInSeconds = intval(preg_replace("/[^0-9]/", "", $hoursToLog)) * 3600;

        $requestParametersDto = $this->prepareRequestParameters($issueKey, $user, $auth);
        $response = $this->client->sendRequest(
            $requestParametersDto->getUrl(),
            $requestParametersDto->getMethod(),
            $requestParametersDto->getAuthHeader(),
            $requestParametersDto->getPostFields()
        );

        $data = json_decode($response, true);
        // If we can't check the worklog of the issue, we assume it's more than 8 hours
        if (!empty($data['errorMessages'])) {
            return true;
        }

        foreach ($data['worklogs'] as $worklog) {
            $dateTime = DateTime::createFromFormat('Y-m-d\TH:i:s.uO', $worklog['created']);
            if (
                (
                    $worklog['timeSpent'] === '8h'
                    || $worklog['timeSpent'] === '1d'
                    || ($worklog['timeSpentSeconds'] + $hoursToLogInSeconds) >= $dailyHoursLimitInSeconds
                ) && $dateTime->format('d/m/Y') === $created
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $issueKey
     * @param string $user
     * @param string $auth
     * @return RequestDto
     */
    private function prepareRequestParameters(string $issueKey, string $user, string $auth): RequestDto
    {
        return new RequestDto(
            getenv("WORKLOG_APP_JIRA_URL") . str_replace('{issueKey}', $issueKey, JiraApiEndpoints::WorkLog->value),
            'GET',
            "Authorization: Basic " . base64_encode("$user:$auth"),
            ''
        );
    }
}
