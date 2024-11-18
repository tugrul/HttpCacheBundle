<?php

namespace Tug\HttpCacheBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;


class TugHttpCacheBundle extends Bundle
{
    public function getPath(): string
    {
        return __DIR__;
    }
}
