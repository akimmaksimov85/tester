<?php
/**
 * Class SeederException
 */

declare(strict_types=1);

namespace Akimmaksimov85\TesterBundle\Exceptions;

use Throwable;

class SeederException extends TestException
{

    /**
     * SeederException constructor.
     *
     * @param string         $seederName Seeder name
     * @param array          $errors     Errors
     * @param string         $message    Exception message
     * @param int            $code       Exception code
     * @param Throwable|null $previous   Previous exception
     */
    public function __construct(
        string $seederName,
        array $errors,
        $message = "",
        $code = 0,
        Throwable $previous = null
    ) {
        $errorMessage = sprintf(
            '%s: %s',
            $seederName,
            json_encode(
                $errors,
                (JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION | JSON_PRETTY_PRINT)
            )
        );

        parent::__construct($errorMessage, $code, $previous);
    }

}
