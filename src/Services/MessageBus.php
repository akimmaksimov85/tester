<?php

declare(strict_types=1);

namespace Akimmaksimov85\TesterBundle\Services;

use Symfony\Component\Messenger\HandleTrait;

class MessageBus
{
    use HandleTrait;

    /**
     * MessageBus constructor.
     */
    public function __construct()
    {
        $this->messageBus = new \Symfony\Component\Messenger\MessageBus();
    }

    /**
     * Executing command
     *
     * @param mixed $command Command
     *
     * @return mixed The handler returned value
     */
    public function execute($command)
    {
        return $this->handle($command);
    }
}
