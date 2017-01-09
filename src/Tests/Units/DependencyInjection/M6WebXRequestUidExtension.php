<?php

namespace M6Web\Bundle\XRequestUidBundle\Tests\Units\DependencyInjection;

use mageekguy\atoum;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use M6Web\Bundle\XRequestUidBundle\DependencyInjection\M6WebXRequestUidExtension as TestedClass;

class M6WebXRequestUidExtension extends atoum\test
{
    /**
     * @var ContainerBuilder
     */
    protected $container;

    protected function initContainer($resource, $debug = false)
    {
        $this->container = new ContainerBuilder();
        $this->container->registerExtension(new TestedClass());

        $this->loadConfiguration($this->container, $resource);

        $this->container->setParameter('kernel.debug', $debug);

        $requestStack = new \mock\Symfony\Component\HttpFoundation\RequestStack();
        $request      = new \mock\Symfony\Component\HttpFoundation\Request();
        $parameterBag = new \mock\Symfony\Component\HttpFoundation\ParameterBag();
        $parameterBag->getMockController()->get = 'ParentId';
        $request->attributes = $parameterBag;
        $requestStack->getMockController()->getCurrentRequest = $request;


        $this->container->set('request_stack', $requestStack);

        $this->container->compile();
    }

    /**
     * @param ContainerBuilder $container
     * @param                  $resource
     */
    protected function loadConfiguration(ContainerBuilder $container, $resource)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../../Fixtures/'));
        $loader->load($resource.'.yml');
    }

    public function testBasicConfiguration()
    {
        $this->initContainer('basic_config', true);
        $this
            ->boolean($this->container->has('test.guzzle1'))
                ->isIdenticalTo(true)
            ->object($this->container->get('test.guzzle1'))
                ->isInstanceOf('GuzzleHttp\ClientInterface')
                ->isInstanceOf('M6Web\Bundle\XRequestUidBundle\Guzzle\GuzzleProxy')
            ->object($this->container->get('test.guzzle2'))
                ->isInstanceOf('GuzzleHttp\ClientInterface')
                ->isNotInstanceOf('M6Web\Bundle\XRequestUidBundle\Guzzle\GuzzleProxy') // guzzle2 is not decorated
        ;
    }

    public function testUniqIdService()
    {
        $this->initContainer('uniqid_config', true);

        $this
            ->object($uniqid = $this->container->get('myuniqid'))
                ->isInstanceOf('M6Web\Bundle\XRequestUidBundle\UniqId\UniqIdInterface')
            ->string($uniqid->uniqid())
                ->isIdenticalTo('unique')
        ->object($uniqid = $this->container->get('uniqid.inner'))
            ->isInstanceOf('M6Web\Bundle\XRequestUidBundle\UniqId\UniqIdInterface')
            ->string($uniqid->uniqid())
                ->isIdenticalTo('unique');
    }
}
