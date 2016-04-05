<?php

namespace M6Web\Bundle\XRequestUidBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * M6WebStatsdBundle
 */
class M6WebXRequestUidBundle extends Bundle
{
    /**
     * trick allowing bypassing the Bundle::getContainerExtension check on getAlias
     * not very clean, to investigate
     *
     * @return Object DependencyInjection\M6WebStatsdExtension
     */
    public function getContainerExtension()
    {
        return new DependencyInjection\M6WebXRequestUiExtension();
    }
}
