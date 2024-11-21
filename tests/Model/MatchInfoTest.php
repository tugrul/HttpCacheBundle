<?php

namespace Model;

use PHPUnit\Framework\TestCase;
use Tug\HttpCacheBundle\Model\MatchInfo;

class MatchInfoTest extends TestCase
{

    public function testEmpty(): void
    {
        $matchInfo = new MatchInfo(null, null);

        $this->assertNull($matchInfo->getETag());

        $this->assertNotNull($matchInfo->getModifiedDate());
    }
}