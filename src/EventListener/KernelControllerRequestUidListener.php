<?php

namespace M6Web\Bundle\XRequestUidBundle\EventListener;

use M6Web\Bundle\XRequestUidBundle\UniqId\UniqIdInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * Class KernelControllerRequestUidListener
 */
class KernelControllerRequestUidListener
{
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
     * @param UniqIdInterface $uniqId
     * @param string          $headerName
     * @param string          $headerParentName
     */
    public function __construct(UniqIdInterface $uniqId, $headerName, $headerParentName)
    {
        $this->uniqId           = $uniqId;
        $this->headerName       = $headerName;
        $this->headerParentName = $headerParentName;
    }

    /**
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $request = $event->getRequest();
        $uniqid = $this->uniqId->uniqid();

        // set the Parent-Uid in the request attributes with the X-Request-Id if exist
        $request->attributes->set(
            $this->headerParentName,
            $request->headers->get($this->headerName, $uniqid)
        );

        // set the Request-Uid with a random uid
        $request->attributes->set(
            $this->headerName,
            $uniqid
        );
    }
}
