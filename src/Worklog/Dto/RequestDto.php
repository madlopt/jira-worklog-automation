<?php

declare(strict_types=1);

namespace App\Worklog\Dto;

class RequestDto
{
    private string $url;
    private string $method;
    private string $authHeader;
    private string $postFields;

    public function __construct(string $url, string $method, string $authHeader, string $postFields)
    {
        $this->url = $url;
        $this->method = $method;
        $this->authHeader = $authHeader;
        $this->postFields = $postFields;

    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return RequestDto
     */
    public function setUrl(string $url): RequestDto
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param string $method
     * @return RequestDto
     */
    public function setMethod(string $method): RequestDto
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @return string
     */
    public function getAuthHeader(): string
    {
        return $this->authHeader;
    }

    /**
     * @param string $authHeader
     * @return RequestDto
     */
    public function setAuthHeader(string $authHeader): RequestDto
    {
        $this->authHeader = $authHeader;
        return $this;
    }

    /**
     * @return string
     */
    public function getPostFields(): string
    {
        return $this->postFields;
    }

    /**
     * @param string $postFields
     * @return RequestDto
     */
    public function setPostFields(string $postFields): RequestDto
    {
        $this->postFields = $postFields;
        return $this;
    }
}
