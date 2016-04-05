<?php

namespace M6Web\Bundle\XRequestUidBundle\UniqId;

/**
 * Class UniqId
 * simple but working exemple
 */
class UniqId implements UniqIdInterface
{
    /**
     * @return mixed
     */
    public function uniqId()
    {
        return uniqid();
    }
}
