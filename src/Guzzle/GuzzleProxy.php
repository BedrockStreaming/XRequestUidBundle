<?php

namespace M6Web\Bundle\XRequestUidBundle\Guzzle;

use GuzzleHttp\ClientInterface;
use M6Web\Bundle\XRequestUidBundle\UniqId\UniqIdInterface;
use GuzzleHttp\Client;
use Psr\Http\Message\RequestInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class GuzzleProxy
 * proxy for guzzle, adding the X-Request-Uid
 * The proxy extends the guzzle client to pass all interfaces
 *
 */
class GuzzleProxy implements ClientInterface
{

    /**
     * @var Client
     */
    protected $guzzleClient;

    protected $allowedMethods = [
        'get',
        'head',
        'put',
        'post',
        'patch',
        'delete',
        'getAsync',
        'headAsync',
        'putAsync',
        'postAsync',
        'patchAsync',
        'deleteAsync',
    ];

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var UniqIdInterface
     */
    protected $uniqId;

    /**
     * @var string
     */
    protected $headerName;

    /**
     * @var string
     */
    protected $headerParentName;

    /**
     * @param Client          $guzzleClient
     * @param RequestStack    $requestStack
     * @param UniqIdInterface $uniqId
     * @param string          $headerName
     * @param string          $headerParentName
     */
    public function __construct(Client $guzzleClient, RequestStack $requestStack, UniqIdInterface $uniqId, $headerName, $headerParentName)
    {
        $this->guzzleClient     = $guzzleClient;
        $this->requestStack     = $requestStack;
        $this->uniqId           = $uniqId;
        $this->headerName       = $headerName;
        $this->headerParentName = $headerParentName;
    }

    /**
     * 12 methods used with guzzle
     *
     * @param string $method
     * @param array  $args
     *
     * @method ResponseInterface get($uri, array $options = [])
     * @method ResponseInterface head($uri, array $options = [])
     * @method ResponseInterface put($uri, array $options = [])
     * @method ResponseInterface post($uri, array $options = [])
     * @method ResponseInterface patch($uri, array $options = [])
     * @method ResponseInterface delete($uri, array $options = [])
     * @method Promise\PromiseInterface getAsync($uri, array $options = [])
     * @method Promise\PromiseInterface headAsync($uri, array $options = [])
     * @method Promise\PromiseInterface putAsync($uri, array $options = [])
     * @method Promise\PromiseInterface postAsync($uri, array $options = [])
     * @method Promise\PromiseInterface patchAsync($uri, array $options = [])
     * @method Promise\PromiseInterface deleteAsync($uri, array $options = [])
     *
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        $options = array_merge($args[1] ?? [], $this->getOptions($method));
        $args[1] = $options;

        // forward the call to the client
        return call_user_func_array([$this->guzzleClient, $method], $args);
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array  $options
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function request($method, $uri = '', array $options = [])
    {
        $options = array_merge($options, $this->getOptions($method));

        return $this->guzzleClient->request($method, $uri, $options);
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array  $options
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function requestAsync($method, $uri = '', array $options = [])
    {
        $options = array_merge($options, $this->getOptions($method));

        return $this->guzzleClient->requestAsync($method, $uri, $options);
    }

    /**
     * @param RequestInterface $request
     * @param array            $options
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function sendAsync(RequestInterface $request, array $options = [])
    {
        $options = array_merge($options, $this->getOptions($request->getMethod()));

        return $this->guzzleClient->sendAsync($request, $options);
    }

    /**
     * @param RequestInterface $request
     * @param array            $options
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function send(RequestInterface $request, array $options = [])
    {
        $options = array_merge($options, $this->getOptions($request->getMethod()));

        return $this->guzzleClient->send($request, $options);
    }

    /**
     * @param Client $guzzle
     *
     * @return $this
     */
    public function setClient(Client $guzzle)
    {
        $this->guzzleClient = $guzzle;

        return $this;
    }

    /**
     * @param null $option
     *
     * @return array|mixed|null
     */
    public function getConfig($option = null)
    {
        return $this->guzzleClient->getConfig($option);
    }

    /**
     * @param string $method
     *
     * @return array
     */
    protected function getOptions(string $method): array
    {
        if (in_array(strtolower($method), $this->allowedMethods)) {
            // add headers to guzzle client
            $request =  $this->requestStack->getCurrentRequest();
            if ($request) {
                return [
                    'headers' => [
                        // generate a new uniqid for the request
                        $this->headerName => $this->uniqId->uniqId(),
                        // switch the id to the parent
                        $this->headerParentName => $request->attributes->get($this->headerParentName)
                    ]
                ];
            }
        }

        return [];
    }
}
