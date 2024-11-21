<?php

namespace Tug\HttpCacheBundle\Provider;

use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use Tug\HttpCacheBundle\Model\MatchInfo as MatchInfoModel;
use Tug\HttpCacheBundle\Registry\RoutesInterface;

class MatchInfo implements MatchInfoInterface
{
    protected RoutesInterface $routes;

    protected CacheKeyInterface $cacheKey;

    protected CacheItemPoolInterface $cache;

    public function __construct(RoutesInterface $routes, CacheKeyInterface $cacheKey, CacheItemPoolInterface $cache)
    {
        $this->routes = $routes;

        $this->cacheKey = $cacheKey;

        $this->cache = $cache;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getMatchInfoByRouteName(string $routeName, array $routeParams = [], array $queryParams = []): ?MatchInfoModel
    {
        $routeMatch = $this->routes->getRouteMatch($routeName, $routeParams, $queryParams);

        if (is_null($routeMatch)) {
            return null;
        }

        $cacheKey = $this->cacheKey->getCacheKey($routeMatch);

        $cache = $this->cache->getItem($cacheKey);

        return $cache->isHit() ? $cache->get() : null;
    }

}