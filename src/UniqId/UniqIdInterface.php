<?php

namespace M6Web\Bundle\XRequestUidBundle\UniqId;

/**
 * Interface UniqIdInterface
 * minimum required to implement the service
 */
interface UniqIdInterface
{
    /**
     * generate a uniqueId
     *
     * @return string
     */
    public function uniqId();
}
