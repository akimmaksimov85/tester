<?php
/**
 * Code
 */

declare(strict_types=1);

namespace Akimmaksimov85\TesterBundle\Data\Factories\Functional\Then\Http;

use Akimmaksimov85\TesterBundle\Data\Factories\Functional\Then\AbstractThen;
use Akimmaksimov85\TesterBundle\Data\Factories\Functional\Then\ThenInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class Code extends AbstractThen implements ThenInterface
{

    /**
     * Assert method
     *
     * @param KernelBrowser $actual   Actual
     * @param int           $expected Expected
     *
     * @return void
     */
    public function assert($actual, $expected): void
    {
        $error = $this->compareDigits(
            $actual->getResponse()->getStatusCode(),
            $expected
        );

        TestCase::assertTrue(
            empty($error) === true,
            $this->getErrorAsString([$error])
        );
    }

}
