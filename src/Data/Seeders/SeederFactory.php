<?php
/**
 * Class SeederFactory
 */

declare(strict_types=1);

namespace Akimmaksimov85\TesterBundle\Data\Seeders;

class SeederFactory
{
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
        $seeder = sprintf($this->seedersPathTemplate, $entityName);

        return new $seeder();
    }
}
