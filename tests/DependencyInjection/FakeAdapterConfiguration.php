<?php

declare(strict_types=1);

namespace PB\Bundle\SmartImageBundle\Tests\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\{NodeBuilder, TreeBuilder};
use PB\Bundle\SmartImageBundle\DependencyInjection\Configurator\Adapter\AdapterConfiguratorInterface;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
final class FakeAdapterConfiguration implements ConfigurationInterface
{
    /**
     * @var AdapterConfiguratorInterface
     */
    private $configurator;

    /**
     * FakeAdapterConfiguration constructor.
     *
     * @param AdapterConfiguratorInterface $configurator
     */
    public function __construct(AdapterConfiguratorInterface $configurator)
    {
        $this->configurator = $configurator;
    }

    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('testable');
        $rootNode = $treeBuilder->getRootNode();

        $node = $rootNode->children();
        $this->appendAdapterConfiguration($node);
        $node->end();

        return $treeBuilder;
    }

    /**
     * @param NodeBuilder $node
     */
    private function appendAdapterConfiguration(NodeBuilder $node): void
    {
        $this->configurator->buildConfiguration($node);
    }
}
