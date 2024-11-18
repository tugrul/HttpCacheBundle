<?php

namespace Tug\HttpCacheBundle\DependencyInjection;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Tug\HttpCacheBundle\Registry\RoutesInterface;

class TugHttpCacheExtension extends Extension
{

    /**
     * @param array $configs
     * @param ContainerBuilder $container
     * @return void
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
        $loader->load('services.yaml');

        if ($container->has(RoutesInterface::class)) {
            $routes = $container->findDefinition(RoutesInterface::class);

            $routes->addMethodCall('setRoutes', [ $config['routes'] ]);
            $routes->addMethodCall('setDefaultIgnoredParamNames', [ $config['ignored_param_names'] ?? [] ]);
            $routes->addMethodCall('setDefaultAllowedParamNames', [ $config['allowed_param_names'] ?? [] ]);
        }
    }
}