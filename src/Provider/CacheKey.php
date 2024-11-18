<?php

namespace Tug\HttpCacheBundle\Provider;

use Tug\HttpCacheBundle\Model\RouteMatch;

class CacheKey implements CacheKeyInterface
{
    public function getCacheKey(RouteMatch $routeMatch): string
    {
        $routeParams = $routeMatch->getRouteParams();
        $queryParams = $routeMatch->getQueryParams();

        ksort($queryParams);
        ksort($routeParams);

        return $routeMatch->getRoute()->getName() . ':' .
            md5( http_build_query($routeParams) . '$' . http_build_query($queryParams));
    }

}