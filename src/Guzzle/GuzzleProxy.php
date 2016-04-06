<?php

namespace M6Web\Bundle\XRequestUidBundle\Guzzle;

use GuzzleHttp\Client;
use M6Web\Bundle\XRequestUidBundle\UniqId\UniqIdInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class GuzzleProxy
 * proxy for guzzle, adding the X-Request-Uid
 * The proxy extends the guzzle client to pass all interfaces
 *
 */
class GuzzleProxy extends Client
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
        if (in_array($method, $this->allowedMethods)) {
            // add headers to guzzle client
            $request =  $this->requestStack->getCurrentRequest();
            if ($request) {
                // generate a new uniqid for the request
                $args[1]['headers'][$this->headerName] = $this->uniqId->uniqId();
                // switch the id to the parent
                $args[1]['headers'][$this->headerParentName] = $request->attributes->get($this->headerParentName);
            }
        }

        // forward the call to the client
        return call_user_func_array([$this->guzzleClient, $method], $args);
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
}
