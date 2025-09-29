<?php

namespace Core;

class Container
{
    protected $bindings = [];

    public function bind($key, $resolver)
    {
        if (!is_callable($resolver)) {
            throw new \Exception("Resolver for {$key} must be callable");
        }

        $this->bindings[$key] = $resolver;
    }

    public function resolve($key)
    {
        if (!isset($this->bindings[$key])) {
            throw new \Exception("No binding found for {$key}");
        }

        $resolver = $this->bindings[$key];

        return $resolver();
    }
}
