<?php

namespace M6Web\Bundle\XRequestUidBundle\Tests\Units\DependencyInjection;

use M6Web\Bundle\XRequestUidBundle\UniqId\UniqIdInterface;

class FakeUniqIdService implements UniqIdInterface
{

    /**
     * generate a uniqueId
     *
     * @return string
     */
    public function uniqId()
    {
        return 'unique';
    }
}
