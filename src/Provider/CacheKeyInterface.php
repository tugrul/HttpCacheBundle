<?php

namespace Tug\HttpCacheBundle\Provider;

use Tug\HttpCacheBundle\Model\RouteMatch;

interface CacheKeyInterface
{
    public function getCacheKey(RouteMatch $routeMatch): string;
}
