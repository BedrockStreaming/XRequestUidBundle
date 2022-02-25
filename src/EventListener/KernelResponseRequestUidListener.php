<?php

namespace M6Web\Bundle\XRequestUidBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

/**
 * Class KernelResponseRequestUidListener
 * set X-Request-Uid and X-Request-Parent-Uid in the response headers
 */
class KernelResponseRequestUidListener
{
    /** @var string */
    protected $headerName;

    /** @var string */
    protected $headerParentName;

    /**
     * @param string $headerName
     * @param string $headerParentName
     */
    public function __construct($headerName, $headerParentName)
    {
        $this->headerName = $headerName;
        $this->headerParentName = $headerParentName;
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        $request = $event->getRequest();
        $response = $event->getResponse();

        if ($request->attributes->has($this->headerName)) {
            $response->headers->set($this->headerName, $request->attributes->get($this->headerName));
        }

        if ($request->attributes->has($this->headerParentName)) {
            $response->headers->set($this->headerParentName, $request->attributes->get($this->headerParentName));
        }
    }
}
