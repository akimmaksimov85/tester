<?php
/**
 * Class SeederInterface
 */
declare(strict_types=1);

namespace Akimmaksimov85\TesterBundle\Data\Seeders;

use Akimmaksimov85\TesterBundle\Exceptions\TestException;

interface SeederInterface
{
    /**
     * Run action
     *
     * @param string $method     UseCase method name
     * @param array  $entityData Data
     *
     * @return void
     * @throws \ReflectionException
     * @throws TestException
     */
    public function run(string $method, array $entityData): void;

}
