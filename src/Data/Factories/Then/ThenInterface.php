<?php
/**
 * ThenInterface
 */

declare(strict_types=1);

namespace Akimmaksimov85\TesterBundle\Data\Factories\Then;

interface ThenInterface
{

    /**
     * Assert method
     *
     * @param mixed $actual   Actual
     * @param mixed $expected Expected
     *
     * @return void
     */
    public function assert($actual, $expected): void;

}
