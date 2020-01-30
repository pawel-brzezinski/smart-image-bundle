<?php

declare(strict_types=1);

namespace PB\Bundle\SmartImageBundle\DependencyInjection\Configurator\Adapter;

use PB\Bundle\SmartImageBundle\Adapter\CloudImageAdapter;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

/**
 * Adapter configurator for CloudImage service.
 *
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
final class CloudImageAdapterConfigurator implements AdapterConfiguratorInterface
{
    /**
     * {@inheritDoc}
     */
    public function buildConfiguration(NodeBuilder $node): void
    {
        $node
            ->scalarNode('token')
                ->isRequired()
                ->cannotBeEmpty()
                ->info('CloudImage.io token')
            ->end()
            ->scalarNode('version')
                ->cannotBeEmpty()
                ->defaultValue('v7')
                ->info('CloudImage.io api version')
            ->end()
            ->scalarNode('alias')
                ->cannotBeEmpty()
                ->info('CloudImage.io storage alias (the storage url can be used as well)')
            ->end()
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function getAdapterClass(): string
    {
        return CloudImageAdapter::class;
    }

    /**
     * {@inheritDoc}
     */
    public function buildAdapterArgsFromConfig(array $config): array
    {
        $args = [$config['token'], $config['version']];

        if (isset($config['alias'])) {
            $args[] = $config['alias'];
        }

        return $args;
    }
}
