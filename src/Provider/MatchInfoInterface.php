<?php

namespace Tug\HttpCacheBundle\Provider;

use Tug\HttpCacheBundle\Model\MatchInfo;

interface MatchInfoInterface
{
    public function getMatchInfoByRouteName(string $routeName, array $routeParams = [],
                                            array $queryParams = []): ?MatchInfo;
}