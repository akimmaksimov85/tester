<?php
/**
 * AbstractTest
 */

declare(strict_types=1);

namespace Akimmaksimov85\TesterBundle;

use Akimmaksimov85\TesterBundle\Constants\GWTConstants;
use Akimmaksimov85\TesterBundle\Data\Factories\Then\ThenFactory;
use Akimmaksimov85\TesterBundle\Data\Factories\When\WhenFactory;
use Akimmaksimov85\TesterBundle\Data\Seeders\SeederFactory;
use Akimmaksimov85\TesterBundle\Data\TemplateEngines\TemplateEngine;
use Akimmaksimov85\TesterBundle\Data\TemplateEngines\TemplateEngineInterface;
use Akimmaksimov85\TesterBundle\Exceptions\TestException;
use Akimmaksimov85\TesterBundle\Helpers\Logger;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

abstract class AbstractTest extends WebTestCase
{
    /**
     * Template engine
     *
     * @var TemplateEngineInterface
     */
    protected TemplateEngineInterface $templateEngine;

    /**
     * Then factory
     *
     * @var ThenFactory
     */
    protected ThenFactory $thenFactory;

    /**
     * When factory
     *
     * @var WhenFactory
     */
    protected WhenFactory $whenFactory;

    /**
     * Web client
     *
     * @var KernelBrowser
     */
    protected KernelBrowser $webClient;

    /**
     * Logger
     *
     * @var Logger
     */
    protected Logger $logger;

    /**
     * Cached suite data
     *
     * @var array
     */
    protected array $suiteData = [];

    /**
     * Set up
     *
     * @return void
     * @throws \RuntimeException
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->webClient = self::createClient();

        $servicesParams = self::$container->get(ParameterBagInterface::class);

        if ($servicesParams instanceof ParameterBagInterface) {
            $this->templateEngine = new TemplateEngine($servicesParams);
        } else {
            throw new \RuntimeException('ParameterBagInterface is required for functional tests');
        }

        $this->thenFactory = new ThenFactory();
        $this->whenFactory = new WhenFactory();
        $this->logger      = new Logger();
    }

    /**
     * Tear down
     *
     * @return void
     */
    public function tearDown(): void
    {
        $this->suiteData = [];
    }

    /**
     * Run test
     *
     * @param string $suitePath Suite path
     *
     * @return void
     * @throws \ReflectionException
     * @throws \Throwable
     */
    protected function runTestBySuite(string $suitePath): void
    {
        $suiteData = $this->getDataFromSuite($suitePath);

        try {
            $this->given($suiteData[GWTConstants::TEST_BEHAVIOUR_GIVEN]);

            foreach ($suiteData[GWTConstants::DATA_TEST_STRUCTURE_KEY] as $protocol => $whenThenCases) {
                foreach ($whenThenCases as $whenThenCase) {
                    $this->logger->logStep($this->getName(), $suitePath, $whenThenCase);
                    $actual = $this->when($protocol, $whenThenCase[GWTConstants::TEST_BEHAVIOUR_WHEN]);
                    $this->then(
                        $suitePath,
                        $protocol,
                        $actual,
                        $whenThenCase[GWTConstants::TEST_BEHAVIOUR_THEN]
                    );
                }
            }
        } catch (TestException $exception) {
            throw $this->logger->logException($suitePath, $exception);
        }
    }

    /**
     * Given step
     *
     * @param array $data Test data
     *
     * @return void
     * @throws \ReflectionException
     * @throws \Throwable
     */
    private function given(array $data): void
    {
        $seederFactory = new SeederFactory($this->getSeedersPathTemplate());

        foreach ($data[GWTConstants::GIVEN_STRUCTURE_SEEDERS] as $seederName => $methods) {
            $seeder = $seederFactory->create($seederName);

            foreach ($methods as $method => $data) {
                $seeder->run($method, $data);
            }
        }
    }

    /**
     * When step
     *
     * @param string $protocol Transport protocol
     * @param array  $data     Test data
     *
     * @return mixed
     * @throws TestException
     */
    private function when(string $protocol, array $data)
    {
        /*
         * TODO fix in future: client should created in when object
         */

        return $this->whenFactory->create($protocol, $this->webClient)->fetch($data);
    }

    /**
     * Then step
     *
     * @param string $suitePath SuitePath
     * @param string $protocol  Transport protocol
     * @param mixed  $actual    Actual data
     * @param array  $thenCases THEN cases
     *
     * @return void
     * @throws TestException
     */
    private function then(string $suitePath, string $protocol, $actual, array $thenCases): void
    {
        foreach ($thenCases as $assetType => $thenCase) {
            $this->thenFactory->create($suitePath, $protocol, $assetType)->assert($actual, $thenCase);
        }
    }

    /**
     * Returns data from suite
     *
     * @param string $suitePath SuitePath
     *
     * @return array
     * @throws \ReflectionException
     * @throws \RuntimeException
     */
    private function getDataFromSuite(string $suitePath): array
    {
        if (isset($this->suiteData[$suitePath]) === true) {
            return $this->suiteData[$suitePath];
        }

        $reflection     = new \ReflectionClass(static::class);
        $testsPathParts = explode('/', $reflection->getFileName());
        array_pop($testsPathParts);
        $testsPath = implode('/', $testsPathParts);

        $this->suiteData[$suitePath] = json_decode(
            file_get_contents($testsPath . '/' . $suitePath),
            true
        );

        if (empty($this->suiteData[$suitePath]) === true) {
            throw new \RuntimeException(
                sprintf(
                    'Invalid json structure in %s',
                    $suitePath
                )
            );
        }

        $vars = (
            $this->suiteData[$suitePath][GWTConstants::TEST_BEHAVIOUR_GIVEN][GWTConstants::GIVEN_STRUCTURE_VARS] ?? []
        );

        if (empty($vars) === false) {
            unset(
                $this->suiteData[$suitePath][GWTConstants::TEST_BEHAVIOUR_GIVEN][GWTConstants::GIVEN_STRUCTURE_VARS]
            );
            $this->suiteData[$suitePath] = $this->templateEngine->fillTemplate(
                $this->suiteData[$suitePath],
                $vars
            );
        }

        return $this->suiteData[$suitePath];
    }


    /**
     * Returns seeders path
     *
     * @return string
     */
    abstract protected function getSeedersPathTemplate(): string;
}
