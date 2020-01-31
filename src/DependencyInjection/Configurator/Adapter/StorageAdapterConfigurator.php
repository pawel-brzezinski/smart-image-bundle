<?php

declare(strict_types=1);

namespace PB\Bundle\SmartImageBundle\DependencyInjection\Configurator\Adapter;

use PB\Bundle\SmartImageBundle\Adapter\StorageAdapter;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

/**
 * Adapter configurator for CloudImage service.
 *
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
final class StorageAdapterConfigurator implements AdapterConfiguratorInterface
{
    /**
     * {@inheritDoc}
     */
    public function buildConfiguration(NodeBuilder $node): void
    {
        $node
            ->scalarNode('url')
                ->isRequired()
                ->cannotBeEmpty()
                ->info('Storage base url')
            ->end()
            ->scalarNode('path_prefix')
                ->cannotBeEmpty()
                ->info('Storage base url')
            ->end()
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function getAdapterClass(): string
    {
        return StorageAdapter::class;
    }

    /**
     * {@inheritDoc}
     */
    public function buildAdapterArgsFromConfig(array $config): array
    {
        $args = [$config['url']];

        if (isset($config['path_prefix'])) {
            $args[] = $config['path_prefix'];
        }

        return $args;
    }
}
