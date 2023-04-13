<?php

declare(strict_types=1);

namespace App\Worklog\Dto;

class ResponseDto
{
    private string $message = '';
    private bool $isError = false;

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return ResponseDto
     */
    public function setMessage(string $message): ResponseDto
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @param string $message
     * @return ResponseDto
     */
    public function appendMessage(string $message): ResponseDto
    {
        $this->message .= $message;

        return $this;
    }

    /**
     * @return bool
     */
    public function isError(): bool
    {
        return $this->isError;
    }

    /**
     * @param bool $isError
     * @return ResponseDto
     */
    public function setIsError(bool $isError): ResponseDto
    {
        $this->isError = $isError;
        return $this;
    }
}
