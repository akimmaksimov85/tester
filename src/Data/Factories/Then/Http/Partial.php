<?php
/**
 * Partial
 */

declare(strict_types=1);

namespace Akimmaksimov85\TesterBundle\Data\Factories\Functional\Then\Http;

use Akimmaksimov85\TesterBundle\Data\Factories\Functional\Then\AbstractThen;
use Akimmaksimov85\TesterBundle\Data\Factories\Functional\Then\ThenInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class Partial extends AbstractThen implements ThenInterface
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
        $actual         = $this->arrayFilterRecursive($actualFullData, $expected);

        $errors = $this->compareArrays(
            $actual,
            $expected
        );

        TestCase::assertTrue(
            empty($errors) === true,
            $this->getErrorAsString($errors)
        );
    }

    /**
     * Filter full array by partial pattern
     *
     * @param array $fullArray    Full array
     * @param array $partialArray Partial pattern array
     *
     * @return array
     */
    protected function arrayFilterRecursive(array $fullArray, array $partialArray): array
    {
        foreach ($fullArray as $key => $value) {
            if (isset($partialArray[$key]) === false) {
                unset($fullArray[$key]);
                continue;
            }

            if (is_array($value) === true) {
                $fullArray[$key] = $this->arrayFilterRecursive($fullArray[$key], $partialArray[$key]);
                continue;
            }
        }

        return $fullArray;
    }

}
