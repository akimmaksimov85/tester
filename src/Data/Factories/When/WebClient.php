<?php
/**
 * WebClient
 */

declare(strict_types=1);

namespace Akimmaksimov85\TesterBundle\Data\Factories\When;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class WebClient implements WhenInterface
{
    /**
     * Web client
     *
     * @var KernelBrowser
     */
    protected KernelBrowser $webClient;

    /**
     * WebClient constructorL
     *
     * @param KernelBrowser $webClient WebClient
     */
    public function __construct(KernelBrowser $webClient)
    {
        $this->webClient = $webClient;
    }

    /**
     * Assert method
     *
     * @param array $config Config
     *
     * @return mixed
     */
    public function fetch(array $config = [])
    {
        $method = ($config['method'] ?? '');
        $url    = ($config['url'] ?? '');
        $params = ($config['params'] ?? []);

        $this->webClient->request(
            $method,
            $url,
            $params
        );

        return $this->webClient;
    }

}
