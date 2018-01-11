<?php

namespace App\Decorator;

use App\DependencyInjection\Compiler\WebPathResolverPass;
use Liip\ImagineBundle\Imagine\Cache\Resolver\WebPathResolver;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Routing\RequestContext;

/**
 * @see WebPathResolverPass
 */
class CDNResolverDecorator extends WebPathResolver
{
    private $cdnHost;
    /**
     * @var bool
     */
    private $isDebug;

    public function __construct(Filesystem $filesystem, RequestContext $requestContext, $webRootDir, $cachePrefix = 'media/cache', string $cdnBaseUrl = '', bool $isDebug)
    {
        parent::__construct($filesystem, $requestContext, $webRootDir, $cachePrefix);
        $this->cdnHost = $cdnBaseUrl;
        $this->isDebug = $isDebug;
    }

    protected function getBaseUrl(): string
    {
        return $this->isDebug ? '' : $this->cdnHost;
    }
}
