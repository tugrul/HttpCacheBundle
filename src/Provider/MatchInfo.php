<?php

namespace Tug\HttpCacheBundle\Provider;

use DateTimeImmutable;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use Tug\HttpCacheBundle\Model\MatchInfo as MatchInfoModel;
use Tug\HttpCacheBundle\Registry\RoutesInterface;

class MatchInfo implements MatchInfoInterface
{
    protected RoutesInterface $routes;

    protected CacheKeyInterface $cacheKey;

    protected CacheItemPoolInterface $cache;

    protected array $preparedMatchInfos = [];

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

        if (isset($this->preparedMatchInfos[$cacheKey])) {
            return $this->preparedMatchInfos[$cacheKey];
        }

        $cache = $this->cache->getItem($cacheKey);

        return $this->preparedMatchInfos[$cacheKey] = ($cache->isHit() ? $cache->get() : null);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function setMatchInfoByRouteName(MatchInfoModel $matchInfo, string $routeName, array $routeParams = [],
                                            array $queryParams = []): bool
    {
        $matchInfoEtag = $matchInfo->getETag();

        if (is_null($matchInfoEtag)) {
            return false;
        }

        $routeMatch = $this->routes->getRouteMatch($routeName, $routeParams, $queryParams);

        if (is_null($routeMatch)) {
            return false;
        }

        $cacheKey = $this->cacheKey->getCacheKey($routeMatch);

        $cache = $this->cache->getItem($cacheKey);

        $matchInfoModifiedDate = $matchInfo->getModifiedDate();

        if (is_null($matchInfoModifiedDate)) {
            $matchInfo->setModifiedDate(new DateTimeImmutable());
        }

        if (!$cache->isHit()) {
            return $this->cache->save($cache->set($matchInfo));
        }

        /**
         * @var $prevMatchInfo MatchInfoModel
         */
        $prevMatchInfo = $cache->get();

        if ($prevMatchInfo->getETag() !== $matchInfoEtag) {
            return $this->cache->save($cache->set($matchInfo));
        }

        return false;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function prepareMatchInfos(array $items): void
    {
        $cacheKeys = [];

        foreach ($items as $item) {
            if (empty($item['routeName'])) {
                continue;
            }

            $routeMatch = $this->routes->getRouteMatch($item['routeName'],
                $item['routeParams'] ?? [], $item['queryParams'] ?? []);

            if (is_null($routeMatch)) {
                continue;
            }

            $cacheKeys[] = $this->cacheKey->getCacheKey($routeMatch);
        }

        $items = $this->cache->getItems($cacheKeys);

        foreach ($items as $key => $matchInfo) {
            $this->preparedMatchInfos[$key] = $matchInfo->isHit() ? $matchInfo->get() : null;
        }
    }

}