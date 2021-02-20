<?php
/**
 * Logger
 */

declare(strict_types=1);

namespace Akimmaksimov85\TesterBundle\Helpers;

use Akimmaksimov85\TesterBundle\Constants\GWTConstants;
use Akimmaksimov85\TesterBundle\Exceptions\TestException;

class Logger
{

    /**
     * Log test process
     *
     * @param string $testName     Test name
     * @param string $suitePath    Suite path
     * @param array  $whenThenCase TestCase data
     *
     * @return void
     */
    public function logStep(string $testName, string $suitePath, array $whenThenCase): void
    {
        $message = implode(
            "\n",
            [
                '-------------------------------------------------------',
                sprintf('Test %s is started', $testName),
                sprintf('TestBySuite - %s', $suitePath),
                sprintf(
                    "WHEN: \n %s",
                    json_encode($whenThenCase[GWTConstants::TEST_BEHAVIOUR_WHEN], JSON_PRETTY_PRINT)
                ),
                sprintf(
                    "THEN: \n %s",
                    json_encode($whenThenCase[GWTConstants::TEST_BEHAVIOUR_THEN], JSON_PRETTY_PRINT)
                ),
            ]
        );

        echo "\n" . $message . "\n";
    }

    /**
     * Log exception
     *
     * @param string     $suitePath Suite path
     * @param \Throwable $exception Exception
     *
     * @return \Throwable
     */
    public function logException(string $suitePath, \Throwable $exception): \Throwable
    {
        $errorMessage = sprintf(
            "\e[1;31mTestSuite %s, %s\e[0m",
            $suitePath,
            $exception->getMessage()
        );

        return new TestException($errorMessage);
    }

}
