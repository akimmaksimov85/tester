<?php
/**
 * Has
 */

declare(strict_types=1);

namespace Akimmaksimov85\TesterBundle\Data\Factories\Then\Http;

use Akimmaksimov85\TesterBundle\Data\Factories\Then\AbstractThen;
use Akimmaksimov85\TesterBundle\Data\Factories\Then\ThenInterface;
use Akimmaksimov85\TesterBundle\Exceptions\TestException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use PHPUnit\Framework\TestCase;

class Has extends AbstractThen implements ThenInterface
{
    protected const COUNT_STRUCTURE_KEY_PATH = 'path';
    protected const COUNT_STRUCTURE_KEY_HAS  = 'has';

    /**
     * Assert method
     *
     * @param KernelBrowser $actual   Actual
     * @param array         $expected Expected
     *
     * @return void
     * @throws TestException
     */
    public function assert($actual, $expected): void
    {
        if (isset($expected[self::COUNT_STRUCTURE_KEY_PATH]) === false
            || isset($expected[self::COUNT_STRUCTURE_KEY_HAS]) === false
        ) {
            throw new TestException('Invalid structure of Has assert: "path" and "has" keys are required');
        }

        $actualFullData = json_decode($actual->getResponse()->getContent(), true);
        $targetKey      = $actualFullData;

        foreach (explode('.', $expected[self::COUNT_STRUCTURE_KEY_PATH]) as $key) {
            if (isset($targetKey[$key]) === false) {
                throw new TestException(
                    sprintf(
                        'Invalid path for Has assert: path %s doesn\'t exist',
                        $expected[self::COUNT_STRUCTURE_KEY_PATH]
                    )
                );
            }

            $targetKey = $targetKey[$key];
        }

        if (is_array($targetKey) === false) {
            throw new TestException(
                sprintf(
                    'Invalid actual data for matching in has path %s: must be an array Array',
                    $expected[self::COUNT_STRUCTURE_KEY_PATH]
                )
            );
        }

        $wasFound = false;

        foreach ($targetKey as $item) {
            $error = $this->compareArrays(
                $item,
                $expected[self::COUNT_STRUCTURE_KEY_HAS]
            );

            if (empty($error) === true) {
                $wasFound = true;
                break;
            }
        }

        TestCase::assertTrue(
            $wasFound === true,
            $this->getErrorAsString(
                [sprintf('there is no item in array by path \'%s\'', $expected[self::COUNT_STRUCTURE_KEY_PATH])]
            )
        );
    }

}
