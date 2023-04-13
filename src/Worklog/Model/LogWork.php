<?php

declare(strict_types=1);

namespace App\Worklog\Model;

use App\Infrastructure\Http\Client;
use App\Infrastructure\Service\Storage\GetFileService;
use App\Infrastructure\ValueObject\JiraApiEndpoints;
use App\Worklog\Dto\RequestDto;
use App\Worklog\Dto\ResponseDto;
use App\Worklog\Service\CurrentDayChecker;
use App\Worklog\Service\IssueWorklogDurationChecker;

class LogWork
{
    private Client $client;
    private CurrentDayChecker $currentDayChecker;
    private IssueWorklogDurationChecker $worklogDurationChecker;
    private GetFileService $getFileService;

    public function __construct()
    {
        $this->client = new Client();
        $this->currentDayChecker = new CurrentDayChecker();
        $this->worklogDurationChecker = new IssueWorklogDurationChecker();
        $this->getFileService = new GetFileService();
    }

    /**
     * @param string $user
     * @param string $auth
     * @param bool $currentDayOnly
     * @return ResponseDto
     */
    public function logFromCsv(string $user, string $auth, bool $currentDayOnly = false): ResponseDto
    {
        $file = $this->getFileService->call('worklog.csv');
        $lines = explode(PHP_EOL, $file);
        $responseDto = new ResponseDto();

        foreach ($lines as $line) {
            $values = explode(',', $line);
            $issueKey = $values[0];
            $worklog = $values[1];
            $date = $values[2];

            if ($currentDayOnly && $this->currentDayChecker->call($date)) {
                $responseDto->appendMessage("Skipping $issueKey because it's not the current day" . PHP_EOL);
                continue;
            }

            if ($this->worklogDurationChecker->isItMoreThanDailyHoursLimit($issueKey, $user, $auth, $date, $worklog)) {
                $responseDto->appendMessage("Skipping $issueKey because it will be more than ". getenv('WORKLOG_APP_DAILY_WORKING_HOURS_LIMIT')." hours" . PHP_EOL);
                continue;
            }

            $requestParametersDto = $this->prepareRequestParameters($date, $issueKey, $worklog, $user, $auth);

            $response = $this->client->sendRequest(
                $requestParametersDto->getUrl(),
                $requestParametersDto->getMethod(),
                $requestParametersDto->getAuthHeader(),
                $requestParametersDto->getPostFields()
            );

            if (empty($response)) {
                $responseDto->appendMessage("Error: empty response from JIRA" . PHP_EOL);
                $responseDto->setIsError(true);

                return $responseDto;
            }

            $data = json_decode($response, true);

            if (!empty($data['errorMessages'])) {
                $responseDto->appendMessage("Error: " . $data['errorMessages'][0] . PHP_EOL);
                $responseDto->setIsError(true);

                return $responseDto;
            }

            $responseDto->appendMessage("Successfully logged $worklog to $issueKey with the date $date" . PHP_EOL);
        }

        $responseDto->appendMessage("Done! Check your JIRA." . PHP_EOL);

        return $responseDto;
    }

    /**
     * @param string $date
     * @param string $issueKey
     * @param string $worklog
     * @param string $user
     * @param string $auth
     * @return RequestDto
     */
    private function prepareRequestParameters(string $date, string $issueKey, string $worklog, string $user, string $auth): RequestDto
    {
        $day = explode('/', $date)[0];
        $month = explode('/', $date)[1];
        $year = explode('/', $date)[2];

        $postFields = json_encode([
            "timeSpent" => $worklog,
            "started" => $year . "-" . $month . "-0" . $day . "T18:00:00.751+0000",
        ]);
        return new RequestDto(
            getenv("WORKLOG_APP_JIRA_URL") . str_replace('{issueKey}', $issueKey, JiraApiEndpoints::WorkLog->value),
            'POST',
            "Authorization: Basic " . base64_encode("$user:$auth"),
            $postFields ?: '',
        );
    }
}
