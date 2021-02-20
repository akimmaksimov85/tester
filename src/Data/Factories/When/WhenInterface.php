<?php
/**
 * ThenInterface
 */

declare(strict_types=1);

namespace Akimmaksimov85\TesterBundle\Data\Factories\Functional\When;

interface WhenInterface
{

    /**
     * Assert method
     *
     * @param array $config Config
     *
     * @return mixed
     */
    public function fetch(array $config = []);

}
