<?php

namespace Tug\HttpCacheBundle\Twig;

use Tug\HttpCacheBundle\Provider\MatchInfoInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TugHttpCacheExtension extends AbstractExtension
{
    protected MatchInfoInterface $matchInfo;

    public function __construct(MatchInfoInterface $matchInfo)
    {
        $this->matchInfo = $matchInfo;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('tug_http_cache', [$this->matchInfo, 'getMatchInfoByRouteName'])
        ];
    }
}