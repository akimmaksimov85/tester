<?php
/**
 * Pattern
 */

declare(strict_types=1);

namespace Akimmaksimov85\TesterBundle\Data\Factories\Functional\Then\Http;

use Akimmaksimov85\TesterBundle\Data\Factories\Functional\Then\AbstractThen;
use Akimmaksimov85\TesterBundle\Data\Factories\Functional\Then\ThenInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use PHPUnit\Framework\TestCase;

class Pattern extends AbstractThen implements ThenInterface
{

    /**
     * Assert method
     *
     * @param KernelBrowser $actual   Actual
     * @param mixed         $expected Expected
     *
     * @return void
     */
    public function assert($actual, $expected): void
    {
        $actualFullData = json_decode($actual->getResponse()->getContent(), true);
        $errors         = $this->compareArrays(
            $actualFullData,
            $expected,
            true
        );

        TestCase::assertTrue(
            empty($errors) === true,
            $this->getErrorAsString($errors)
        );
    }

}
