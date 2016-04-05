<?php

namespace M6Web\Bundle\XRequestUidBundle\DependencyInjection;


use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Definition;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class M6WebXRequestUidExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        // set parameters
        $container->setParameter('request_uid_header_name', $config['request_uid_header_name']);
        $container->setParameter('request_parent_uid_header_name', $config['request_parent_uid_header_name']);

        // get the uniqIdService
        if (array_key_exists('uniqId_service', $config)) {
            // set the uniqid.inner as an alias of uniqId service
            $container->setAlias(
                'uniqid.inner',
                $config['uniqId_service']
            );
        } else {
            $container->register('uniqid.base', 'M6Web\Bundle\XRequestUidBundle\UniqId\UniqId');
            // use UniqId\UniqId
            $container->setAlias(
                'uniqid.inner',
                'uniqid.base'
            );
        }

        // decorate services
        foreach ($config['services'] as $service) {
            $container->register($service.'xrequestUid', 'M6Web\Bundle\XRequestUidBundle\Guzzle\GuzzleProxy')
                ->setDecoratedService($service, $service.'_orig')
                ->addArgument(new Reference($service.'_orig'))           // original service
                ->addArgument(new Reference('request_stack'))            // sf request stack
                ->addArgument(new Reference('uniqid.inner'))             // uniqid service
                ->addArgument($config['request_uid_header_name'])        // header name
                ->addArgument($config['request_parent_uid_header_name']) // parent header name
                ->setPublic(false);
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }

}
