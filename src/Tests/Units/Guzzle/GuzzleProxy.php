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
            ->object($c)
                ->isInstanceOf('GuzzleHttp\ClientInterface');
    }

    /**
     * @param string $method
     */
    public function testMethod($method, $options = [])
    {
        list($guzzleClient, $requestStack, $uniqId) = $this->getMocks();

        $guzzleClient->getMockController()->{$method} = null;
        $options = array_merge($options, ['headers' => ['X-Request-Id' => '1234', 'X-RequestParentId' => 'ParentId']]);

        $this
            ->if($c = new TestedClass(
                $guzzleClient,
                $requestStack,
                $uniqId,
                'X-Request-Id',
                'X-RequestParentId'
            ))
            ->and($c->get($url = 'http://6play.fr'))
            ->mock($guzzleClient)
                ->call('get')
                    ->withArguments($url, $options)
                        ->once()
        ;
    }

    /**
     * @return array
     */
    protected function testMethodDataProvider()
    {
        return [
            [
                'get'
            ],
            [
                'send'
            ],
            [
                'request'
            ],
            [
                'get',
                [
                    'headers' => [
                        'super-header' => 'youpi'
                    ]
                ]
            ]
        ];
    }

    protected function getMocks()
    {
        $request      = new \Symfony\Component\HttpFoundation\Request([], [], ['X-RequestParentId' => 'ParentId']);
        $requestStack = new \mock\Symfony\Component\HttpFoundation\RequestStack();
        $requestStack->getMockController()->getCurrentRequest = $request;

        $uniqId = new \mock\M6Web\Bundle\XRequestUidBundle\UniqId\UniqId();
        $uniqId->getMockController()->uniqId = '1234';

        return [
            new \mock\GuzzleHttp\Client(),
            $requestStack,
            $uniqId
        ];
    }



}
