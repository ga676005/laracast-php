<?php

namespace Core;

class Response
{
    public const FORBIDDEN = 403;
    public const NOT_FOUND = 404;
    public const UNAUTHORIZED = 401;
    public const OK = 200;
    public const REDIRECT = 302;

    private $content;
    private $statusCode;
    private $headers;

    public function __construct($content = '', $statusCode = 200, $headers = [])
    {
        $this->content = $content;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    public function send()
    {
        http_response_code($this->statusCode);

        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}");
        }

        if ($this->content) {
            echo $this->content;
        }
    }

    public function redirect($url, $statusCode = self::REDIRECT)
    {
        return new self('', $statusCode, ['Location' => $url]);
    }

    public function json($data, $statusCode = self::OK)
    {
        return new self(json_encode($data), $statusCode, ['Content-Type' => 'application/json']);
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getHeaders()
    {
        return $this->headers;
    }
}
