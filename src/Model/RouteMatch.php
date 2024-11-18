<?php

namespace Tug\HttpCacheBundle\Model;

class RouteMatch
{
    protected Route $route;

    protected array $queryParams = [];

    protected array $routeParams = [];

    public function __construct(Route $route, array $queryParams, array $routeParams)
    {
        $this->route = $route;

        $this->queryParams = $queryParams;

        $this->routeParams = $routeParams;
    }

    public function getRoute(): Route
    {
        return $this->route;
    }

    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    public function getRouteParams(): array
    {
        return $this->routeParams;
    }


}