<?php

namespace Akimmaksimov85\TesterBundle\DependencyInjection;

use Akimmaksimov85\TesterBundle\TesterBundle;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        return new TreeBuilder(TesterBundle::CONFIG_BUNDLE_NAMESPACE);
    }
}