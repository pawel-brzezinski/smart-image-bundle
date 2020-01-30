<?php

declare(strict_types=1);

namespace PB\Bundle\SmartImageBundle\DependencyInjection\Configurator\Adapter;

use PB\Bundle\SmartImageBundle\Adapter\AdapterInterface;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

/**
 * Interface for adapter configurator implementation.
 *
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
interface AdapterConfiguratorInterface
{
    /**
     * Builds configuration tree for options specific for given adapter.
     *
     * @param NodeBuilder $node
     */
    public function buildConfiguration(NodeBuilder $node): void;

    /**
     * Gets adapter class.
     *
     * @return string
     */
    public function getAdapterClass(): string;

    /**
     * Builds adapter array of arguments from configuration.
     *
     * @param array $config
     *
     * @return array
     */
    public function buildAdapterArgsFromConfig(array $config): array;
}
