<?php

namespace M6Web\Bundle\XRequestUidBundle\Tests\Units\Guzzle;

use M6Web\Bundle\XRequestUidBundle\Guzzle\GuzzleProxy as TestedClass;
use atoum\atoum;

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
    public function testMethod($method, $options = [], $expectedOptions = [])
    {
        list($guzzleClient, $requestStack, $uniqId) = $this->getMocks();

        $guzzleClient->getMockController()->{$method} = null;

        $this
            ->if($c = new TestedClass(
                $guzzleClient,
                $requestStack,
                $uniqId,
                'X-Request-Id',
                'X-RequestParentId'
            ))
            ->and($c->get($url = 'http://6play.fr', $options))
            ->mock($guzzleClient)
                ->call('get')
                    ->withArguments($url, $expectedOptions)
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
                'get',
                [],
                [
                    'headers' => [
                        'X-Request-Id' => '1234',
                        'X-RequestParentId' => 'ParentId',
                    ],
                ],
            ],
            [
                'send',
                [],
                [
                    'headers' => [
                        'X-Request-Id' => '1234',
                        'X-RequestParentId' => 'ParentId',
                    ],
                ],
            ],
            [
                'send',
                [
                    'headers' => [
                        'super-header' => 'youpi',
                    ],
                ],
                [
                    'headers' => [
                        'super-header' => 'youpi',
                        'X-Request-Id' => '1234',
                        'X-RequestParentId' => 'ParentId',
                    ],
                ],
            ],
            [
                'request',
                [],
                [
                    'headers' => [
                        'X-Request-Id' => '1234',
                        'X-RequestParentId' => 'ParentId',
                    ],
                ],
            ],
            [
                'request',
                [
                    'headers' => [
                        'super-header' => 'youpi',
                    ],
                ],
                [
                    'headers' => [
                        'super-header' => 'youpi',
                        'X-Request-Id' => '1234',
                        'X-RequestParentId' => 'ParentId',
                    ],
                ],
            ],
            [
                'get',
                [
                    'headers' => [
                        'super-header' => 'youpi',
                    ],
                ],
                [
                    'headers' => [
                        'super-header' => 'youpi',
                        'X-Request-Id' => '1234',
                        'X-RequestParentId' => 'ParentId',
                    ],
                ],
            ],
        ];
    }

    protected function getMocks()
    {
        $request = new \Symfony\Component\HttpFoundation\Request([], [], ['X-RequestParentId' => 'ParentId']);
        $requestStack = new \mock\Symfony\Component\HttpFoundation\RequestStack();
        $requestStack->getMockController()->getCurrentRequest = $request;

        $uniqId = new \mock\M6Web\Bundle\XRequestUidBundle\UniqId\UniqId();
        $uniqId->getMockController()->uniqId = '1234';

        return [
            new \mock\GuzzleHttp\Client(),
            $requestStack,
            $uniqId,
        ];
    }
}
