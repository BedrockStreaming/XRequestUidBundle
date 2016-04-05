<?php

namespace M6Web\Bundle\XRequestUidBundle\Tests\Units\Guzzle;

use M6Web\Bundle\XRequestUidBundle\UniqId\UniqId;
use mageekguy\atoum;
use M6Web\Bundle\XRequestUidBundle\Guzzle\GuzzleProxy as TestedClass;

class GuzzleProxy extends atoum\test
{

    public function testGuzzle()
    {
        list($guzzleClient, $requestStack, $uniqId) = $this->getMocks();

        $this
            ->if($c = new TestedClass(
                $guzzleClient,
                $requestStack,
                $uniqId,
                'X-Request-Id',
                'X-Request-Parent-Id'
            ))
                ->class('GuzzleHttp\Client');
    }

    public function testMethod()
    {
        list($guzzleClient, $requestStack, $uniqId) = $this->getMocks();

        $guzzleClient->getMockController()->get = null;

        $this
            ->if($c = new TestedClass(
                $guzzleClient,
                $requestStack,
                $uniqId,
                'X-Request-Id',
                'X-RequestParentId'
            ))
            ->and($c->get('http://raoul.com'))
            ->mock($guzzleClient)
                ->call('get')
                    ->once()
        ;
    }


    protected function getMocks()
    {
        $requestStack = new \mock\Symfony\Component\HttpFoundation\RequestStack();
        $request      = new \mock\Symfony\Component\HttpFoundation\Request();
        $parameterBag = new \mock\Symfony\Component\HttpFoundation\ParameterBag();
        $parameterBag->getMockController()->get = 'ParentId';
        $request->attributes = $parameterBag;
        $requestStack->getMockController()->getCurrentRequest = $request;

        return [
            new \mock\GuzzleHttp\Client(),
            new \mock\Symfony\Component\HttpFoundation\RequestStack(),
            new UniqId()
        ];
    }



}
