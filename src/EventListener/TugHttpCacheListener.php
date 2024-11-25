<?php

namespace Tug\HttpCacheBundle\EventListener;

use DateTimeImmutable;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\EventListener\AbstractSessionListener;
use Tug\HttpCacheBundle\Model\MatchInfo;
use Tug\HttpCacheBundle\Provider\CacheKeyInterface;
use Tug\HttpCacheBundle\Registry\RoutesInterface;

class TugHttpCacheListener implements EventSubscriberInterface
{
    protected RoutesInterface $routes;

    protected CacheKeyInterface $cacheKey;

    protected CacheItemPoolInterface $cache;

    protected bool $cacheEnabled;

    public function __construct(RoutesInterface $routes, CacheKeyInterface $cacheKey,
                                CacheItemPoolInterface $cache, bool $cacheEnabled)
    {
        $this->routes = $routes;

        $this->cacheKey = $cacheKey;

        $this->cache = $cache;

        $this->cacheEnabled = $cacheEnabled;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$this->cacheEnabled || !$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        if ($request->hasPreviousSession()) {
            return;
        }

        $routeMatch = $this->routes->getRouteMatchByRequest($request);

        if (is_null($routeMatch)) {
            return;
        }

        $cacheKey = $this->cacheKey->getCacheKey($routeMatch);

        $cacheItem = $this->cache->getItem($cacheKey);

        if (!$cacheItem->isHit()) {
            $request->attributes->set('cache_store_action', 'init');
            $request->attributes->set('cache_store_id', $cacheKey);
            return;
        }

        /**
         * @var $matchInfo MatchInfo
         */
        $matchInfo = $cacheItem->get();

        $eTag = $matchInfo->getETag();
        $modifiedDate = $matchInfo->getModifiedDate();

        // no useful information to check request's cache status
        if (is_null($eTag) && is_null($modifiedDate)) {
            $request->attributes->set('cache_store_action', 'init');
            $request->attributes->set('cache_store_id', $cacheKey);
            return;
        }

        $response = new Response();
        $response->headers->set(AbstractSessionListener::NO_AUTO_CACHE_CONTROL_HEADER, 'true');
        $response->setEtag($eTag);
        $response->setLastModified($modifiedDate);

        if ($response->isNotModified($request)) {
            $event->setResponse($response);
        } else {
            $request->attributes->set('cache_store_action', 'update');
            $request->attributes->set('cache_store_id', $cacheKey);
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$this->cacheEnabled || !$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        if ($request->hasPreviousSession()) {
            return;
        }

        $response = $event->getResponse();
        $content = $response->getContent();

        if (empty($content) || ($response->getStatusCode() !== Response::HTTP_OK)
            || !$request->attributes->has('cache_store_id')) {
            return;
        }

        $cacheKey = $request->attributes->get('cache_store_id');
        $cacheItem = $this->cache->getItem($cacheKey);
        $cacheAction = $request->attributes->get('cache_store_action', 'init');

        $dateLastModified = $response->getLastModified() ?? new DateTimeImmutable();
        $eTag = $response->getEtag() ?? md5($content);

        if ($cacheAction === 'init' || !$cacheItem->isHit()) {
            $cacheItem->set(new MatchInfo($eTag, $dateLastModified));
            $this->cache->save($cacheItem);
        } elseif ($cacheAction === 'update') {
            /**
             * @var $matchInfo MatchInfo
             */
            $matchInfo = $cacheItem->get();
            $matchInfoEtag = $matchInfo->getETag();
            $matchInfoModifiedDate = $matchInfo->getModifiedDate();

            if (!is_null($matchInfoEtag) && ($matchInfoEtag === $eTag)) {
                if (is_null($matchInfoModifiedDate)) {
                    $matchInfo->setModifiedDate($dateLastModified);
                } else {
                    $dateLastModified = $matchInfoModifiedDate;
                }
            } else {
                $matchInfo->setETag($eTag);
                $matchInfo->setModifiedDate($dateLastModified);
                $cacheItem->set($matchInfo);
            }

            $this->cache->save($cacheItem);
        }

        $response->headers->set(AbstractSessionListener::NO_AUTO_CACHE_CONTROL_HEADER, 'true');
        $response->setLastModified($dateLastModified);
        $response->setEtag($eTag);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => 'onKernelRequest',
            ResponseEvent::class => 'onKernelResponse',
        ];
    }
}
