<?php
/**
 * ThenFactory
 */

declare(strict_types=1);

namespace Akimmaksimov85\TesterBundle\Data\Factories\Then;

use Akimmaksimov85\TesterBundle\Data\Factories\Then\Http\Code;
use Akimmaksimov85\TesterBundle\Data\Factories\Then\Http\Count;
use Akimmaksimov85\TesterBundle\Data\Factories\Then\Http\Has;
use Akimmaksimov85\TesterBundle\Data\Factories\Then\Http\Partial;
use Akimmaksimov85\TesterBundle\Data\Factories\Then\Http\Pattern;
use Akimmaksimov85\TesterBundle\Exceptions\TestException;

class ThenFactory
{
    protected const ASSERT_THEN_OBJECT_PATTERN = Pattern::class;
    protected const ASSERT_THEN_OBJECT_CODE    = Code::class;
    protected const ASSERT_THEN_OBJECT_PARTIAL = Partial::class;
    protected const ASSERT_THEN_OBJECT_COUNT   = Count::class;
    protected const ASSERT_THEN_OBJECT_HAS     = Has::class;

    protected const ASSERT_METHODS_MAP = [
        'HTTP' => [
            'PATTERN' => self::ASSERT_THEN_OBJECT_PATTERN,
            'CODE'    => self::ASSERT_THEN_OBJECT_CODE,
            'PARTIAL' => self::ASSERT_THEN_OBJECT_PARTIAL,
            'COUNT'   => self::ASSERT_THEN_OBJECT_COUNT,
            'HAS'     => self::ASSERT_THEN_OBJECT_HAS,
        ],
    ];

    /**
     * Returns THEN object for asserting
     *
     * @param string $suitePath  SuitePath
     * @param string $protocol   Protocol
     * @param string $assertType Asset type of THEN step
     *
     * @return ThenInterface
     * @throws TestException
     */
    public function create(string $suitePath, string $protocol, string $assertType): ThenInterface
    {
        if (isset(self::ASSERT_METHODS_MAP[$protocol][$assertType]) === false) {
            throw new TestException(sprintf('Assert "Then" class %s not allowed', $assertType));
        }

        $class      = self::ASSERT_METHODS_MAP[$protocol][$assertType];
        $thenObject = new $class($suitePath);

        if ($thenObject instanceof ThenInterface) {
            return $thenObject;
        }

        throw new TestException(
            sprintf(
                'Class %s not implemented %s',
                $class,
                ThenInterface::class
            )
        );
    }

}
