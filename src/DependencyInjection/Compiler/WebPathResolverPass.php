<?php

namespace App\DependencyInjection\Compiler;

use App\Decorator\CDNResolverDecorator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class WebPathResolverPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $builder): void
    {
        if (!$builder->hasDefinition('liip_imagine.cache.resolver.prototype.web_path')) {
            return;
        }

        $definition = $builder->getDefinition('liip_imagine.cache.resolver.prototype.web_path');
        $definition->setClass(CDNResolverDecorator::class);
        $definition->setArgument(4, $builder->getParameter('env(CDN_BASE_URL)'));
        $definition->setArgument(5, $builder->getParameter('kernel.debug'));
    }
}