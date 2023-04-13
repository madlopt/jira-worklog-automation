<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

class Client
{
    public const DEFAULT_HEADERS = [
        "Content-Type:application/json",
        "X-Atlassian-Token:no-check",
    ];

    public function sendRequest(string $url, string $method, string $authHeader = null, string $postFields = null): string
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($curl, CURLOPT_USERPWD, $authHeader);
        curl_setopt($curl, CURLOPT_HTTPHEADER, self::DEFAULT_HEADERS);
        $response = curl_exec($curl);
        curl_close($curl);

        return is_string($response) ? $response : '';
    }
}
