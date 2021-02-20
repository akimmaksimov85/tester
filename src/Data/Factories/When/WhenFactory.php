<?php
/**
 * ThenFactory
 */

declare(strict_types=1);

namespace Akimmaksimov85\TesterBundle\Data\Factories\Functional\When;

use Akimmaksimov85\TesterBundle\Exceptions\TestException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class WhenFactory
{
    protected const WHEN_CLIENT_HTTP = WebClient::class;
    protected const WHEN_CLIENTS_MAP = ['HTTP' => self::WHEN_CLIENT_HTTP];

    /**
     * Returns WHEN client
     *
     * @param string              $protocol Protocol
     * @param KernelBrowser|mixed $client   Client
     *
     * @return WhenInterface
     * @throws TestException
     */
    public function create(string $protocol, $client): WhenInterface
    {
        if (isset(self::WHEN_CLIENTS_MAP[$protocol]) === false) {
            throw new TestException(sprintf('Client "When" class %s not allowed', $protocol));
        }

        $class      = self::WHEN_CLIENTS_MAP[$protocol];
        $whenObject = new $class($client);

        if ($whenObject instanceof WhenInterface) {
            return $whenObject;
        }

        throw new \Exception(
            sprintf(
                'Class %s not implemented %s',
                $class,
                WhenInterface::class
            )
        );
    }

}
