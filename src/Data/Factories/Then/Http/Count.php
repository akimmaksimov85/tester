<?php
/**
 * Count
 */

declare(strict_types=1);

namespace Akimmaksimov85\TesterBundle\Data\Factories\Then\Http;

use Akimmaksimov85\TesterBundle\Data\Factories\Then\AbstractThen;
use Akimmaksimov85\TesterBundle\Data\Factories\Then\ThenInterface;
use Akimmaksimov85\TesterBundle\Exceptions\TestException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use PHPUnit\Framework\TestCase;

class Count extends AbstractThen implements ThenInterface
{
    protected const COUNT_STRUCTURE_KEY_PATH  = 'path';
    protected const COUNT_STRUCTURE_KEY_COUNT = 'count';

    /**
     * Assert method
     *
     * @param KernelBrowser $actual   Actual
     * @param array         $expected Expected
     *
     * @return void
     * @throws \Exception
     */
    public function assert($actual, $expected): void
    {
        if (isset($expected[self::COUNT_STRUCTURE_KEY_PATH]) === false
            || isset($expected[self::COUNT_STRUCTURE_KEY_COUNT]) === false
        ) {
            throw new TestException('Invalid structure of Count assert: "path" and "count" keys are required');
        }

        $actualFullData = json_decode($actual->getResponse()->getContent(), true);
        $targetKey      = $actualFullData;

        foreach (explode('.', $expected[self::COUNT_STRUCTURE_KEY_PATH]) as $key) {
            if (isset($targetKey[$key]) === false) {
                throw new TestException(sprintf('Invalid path %s', $expected[self::COUNT_STRUCTURE_KEY_PATH]));
            }

            $targetKey = $targetKey[$key];
        }

        $error = $this->compareDigits(
            count($targetKey),
            $expected[self::COUNT_STRUCTURE_KEY_COUNT]
        );

        TestCase::assertTrue(
            empty($error) === true,
            $this->getErrorAsString([$error])
        );
    }

}
