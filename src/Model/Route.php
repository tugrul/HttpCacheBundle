<?php

namespace Tug\HttpCacheBundle\Model;

class Route
{
    protected string $name;

    protected ?array $allowedQueryNames = null;

    protected ?array $allowedParamNames = null;

    protected ?array $ignoredParamNames = null;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Route
    {
        $this->name = $name;
        return $this;
    }

    public function getAllowedQueryNames(): ?array
    {
        return $this->allowedQueryNames;
    }

    public function setAllowedQueryNames(?array $allowedQueryNames): Route
    {
        $this->allowedQueryNames = $allowedQueryNames;
        return $this;
    }

    public function getAllowedParamNames(): ?array
    {
        return $this->allowedParamNames;
    }

    public function setAllowedParamNames(?array $allowedParamNames): Route
    {
        $this->allowedParamNames = $allowedParamNames;
        return $this;
    }

    public function getIgnoredParamNames(): ?array
    {
        return $this->ignoredParamNames;
    }

    public function setIgnoredParamNames(?array $ignoredParamNames): Route
    {
        $this->ignoredParamNames = $ignoredParamNames;
        return $this;
    }


}