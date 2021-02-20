<?php
/**
 * Class AbstractSeeder
 */
declare(strict_types=1);

namespace Akimmaksimov85\TesterBundle\Data\Seeders;

use Akimmaksimov85\TesterBundle\Exceptions\TestException;
use Faker\Factory;

abstract class AbstractSeeder implements SeederInterface
{
    protected const COMMAND_ADD    = 'add';
    protected const COMMAND_UPDATE = 'update';
    protected const COMMAND_DELETE = 'delete';

    /**
     * Data for changing redis state
     *
     * @var array
     */
    protected array $data;

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
    public function run(string $method, array $entityData): void
    {
        foreach ($entityData as $item) {
            if (in_array($method, $this->getAllowedMethods()) === false) {
                throw new TestException(sprintf('Method %s not allowed for seeding', $method));
            }

            $command = $this->getCommand($method);
            $command = $this->hydrateCommand($command, $item);

            $this->execute($command);
        }
    }

    /**
     * List of allowed methods for seeding
     *
     * @return array
     */
    protected function getAllowedMethods(): array
    {
        return [
            self::COMMAND_ADD,
            self::COMMAND_UPDATE,
            self::COMMAND_DELETE,
        ];
    }

    /**
     * Hydrate command
     *
     * @param mixed $command    Command
     * @param array           $entityData Data
     *
     * @return mixed
     * @throws \ReflectionException
     */
    protected function hydrateCommand($command, array $entityData)
    {
        $reflection = new \ReflectionClass($command);
        foreach ($reflection->getProperties() as $propertyAttrs) {
            $property = $propertyAttrs->getName();

            if (isset($entityData[$property]) === false) {
                $command->$property = $this->generatePropertyValue($propertyAttrs);
                continue;
            }

            $command->$property = $entityData[$property];
        }

        return $command;
    }

    /**
     * Generate property value
     *
     * @param \ReflectionProperty $reflectionProperty Property value
     *
     * @return mixed|null
     */
    protected function generatePropertyValue(\ReflectionProperty $reflectionProperty)
    {
        $docAttrs = array_filter(
            array_map(
                'trim',
                explode('*', $reflectionProperty->getDocComment())
            )
        );

        foreach ($docAttrs as $docAttr) {
            if (strpos($docAttr, '@Assert\Type') !== false) {
                return $this->getFakeValueByType($docAttr);
            }
        }

        return null;
    }

    /**
     * Generate value by type
     *
     * @param string $type Type in doc
     *
     * @return mixed|null
     */
    protected function getFakeValueByType(string $type)
    {
        if (isset($this->getCommandTypesFakerFunctionsMap()[$type]) === true) {
            return Factory::create()->{$this->getCommandTypesFakerFunctionsMap()[$type]};
        }

        return null;
    }

    /**
     * Get command
     *
     * @param string $action Command
     *
     * @return mixed
     */
    protected function getCommand(string $action)
    {
        $class = $this->getUseCasePath() . '\\' . ucfirst($action) . '\\Command';

        if (class_exists($class) === false) {
            throw new \RuntimeException(sprintf('Class %s doesn\'t exist', $class));
        }

        return new $class();
    }

    /**
     * Returns command types => faker functions map
     *
     * @return array
     */
    protected function getCommandTypesFakerFunctionsMap(): array
    {
        return [
            '@Assert\Type("string")'  => 'word',
            '@Assert\Type("int")'     => 'randomDigitNotNull',
            '@Assert\Type("integer")' => 'randomDigitNotNull',
            '@Assert\Type("bool")'    => 'boolean',
            '@Assert\Type("boolean")' => 'boolean',
        ];
    }

    /**
     * Get useCase path
     *
     * @return string
     */
    abstract protected function getUseCasePath(): string;

    /**
     * Get useCase path
     *
     * @param mixed $command Command
     *
     * @return string
     */
    abstract protected function execute($command): string;
}
