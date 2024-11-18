<?php

namespace Tug\HttpCacheBundle\Registry;

use Symfony\Component\HttpFoundation\Request;
use Tug\HttpCacheBundle\Model\RouteMatch;

interface RoutesInterface
{
    public function setRoutes(array $routes);

    public function setDefaultAllowedParamNames(array $paramNames);

    public function setDefaultIgnoredParamNames(array $paramNames);

    public function getRouteMatch(Request $request): ?RouteMatch;
}