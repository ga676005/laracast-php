<?php

namespace Core;

abstract class Middleware
{
    protected $next;

    public function setNext(Middleware $middleware)
    {
        $this->next = $middleware;

        return $middleware;
    }

    public function handle($request = null): Response
    {
        $response = $this->process($request);

        // If current middleware returned an error/redirect, stop the chain
        if ($response->getStatusCode() !== Response::OK) {
            return $response;
        }

        // Only continue to next middleware if current one succeeded
        if ($this->next) {
            return $this->next->handle($request);
        }

        return $response;
    }

    abstract protected function process($request = null): Response;
}
