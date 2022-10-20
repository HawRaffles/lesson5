<?php

namespace Study\UrlCompressor\Helpers;

class CheckUrl
{
    private int $timeout;
    private array $statusCodes;
    private string $userAgent;
    public string $checkedUrl;
    public bool $urlType;

    public function __construct(int $timeout, array $statusCodes, string $userAgent)
    {
        $this->timeout = $timeout;
        $this->statusCodes = $statusCodes;
        $this->userAgent = $userAgent;
        $this->urlType = false;
        $this->checkedUrl = $this->CheckInput();
        $responseCode = $this->GetRequest();
        $this->ValidateResponse($responseCode);
    }

    private function CheckInput(): string
    {
        do {
            $input = readline('Введіть корректний URL: ');
        } while (!filter_var($input, FILTER_VALIDATE_URL));
        return $input;
    }

    private function GetRequest(): int
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->checkedUrl);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $httpCode;
    }

    private function ValidateResponse(int $code): void
    {
        if (isset($this->statusCodes[$code]))
            $this->urlType = true;
    }
}
