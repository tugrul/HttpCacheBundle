<?php

namespace Tug\HttpCacheBundle\Provider;

use Tug\HttpCacheBundle\Model\MatchInfo;

interface MatchInfoInterface
{
    public function getMatchInfoByRouteName(string $routeName, array $routeParams = [],
                                            array $queryParams = []): ?MatchInfo;

    public function setMatchInfoByRouteName(MatchInfo $matchInfo, string $routeName, array $routeParams = [],
                                            array $queryParams = []): bool;

    public function prepareMatchInfos(array $items): void;
}