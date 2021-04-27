<?php
/**
 * Class SeederFactory
 */

declare(strict_types=1);

namespace Akimmaksimov85\TesterBundle\Data\Seeders;

use Akimmaksimov85\TesterBundle\Services\MessageBus;

class SeederFactory
{
    protected const SEEDER_NAME_POSTFIX = 'Seeder';

    /**
     * Seeder path template
     *
     * @var string
     */
    protected string $seedersPathTemplate;

    /**
     * SeederFactory constructor.
     *
     * @param string $seedersPathTemplate Seeder path template
     */
    public function __construct(string $seedersPathTemplate)
    {
        $this->seedersPathTemplate = $seedersPathTemplate;
    }

    /**
     * Create seeder
     *
     * @param string $entityName Seeder name
     *
     * @return AbstractSeeder
     */
    public function create(string $entityName): AbstractSeeder
    {
        $seeder = $this->seedersPathTemplate . $entityName . self::SEEDER_NAME_POSTFIX;

        return new $seeder(new MessageBus());
    }
}
