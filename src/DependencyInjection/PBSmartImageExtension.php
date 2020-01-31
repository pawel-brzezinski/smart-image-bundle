<?php

declare(strict_types=1);

namespace PB\Bundle\SmartImageBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\{NodeDefinition, TreeBuilder};
use PB\Bundle\SmartImageBundle\DependencyInjection\Exception\{
    AdapterNotSupportedException,
    DefaultAdapterNotExistException,
    MissingAdapterTypeException
};
use PB\Bundle\SmartImageBundle\DependencyInjection\Configurator\Adapter\{AdapterConfiguratorInterface, CloudImageAdapterConfigurator};
use PB\Bundle\SmartImageBundle\Adapter\AdapterRegistryInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\{ContainerBuilder, Definition, Reference};
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
final class PBSmartImageExtension extends Extension
{
    private const ADAPTER_SERVICE_KEY_PATTERN = 'pb_smart_image.adapter.%s';

    private const SUPPORTED_ADAPTERS = [
        'cloudimage' => CloudImageAdapterConfigurator::class,
    ];

    /**
     * @var AdapterConfiguratorInterface[]
     */
    private $configurators = [];

    /**
     * @var array
     */
    private $adapterServiceKeys = [];

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        // Step 1. Check if adapter defined as default exist in adapters configuration. If not then throw an exception.
        $defaultAdapter = $config['default_adapter'];
        $adapters = $config['adapters'];

        if (false === in_array($defaultAdapter, array_keys($adapters))) {
            throw new DefaultAdapterNotExistException($defaultAdapter);
        }

        // Step 2. Iterate between all of defined adapters and create adapter service.
        foreach ($adapters as $key => $adapterConfig) {
            // Step 2.1. Get the adapter type and remove type key from the adapter config.
            /** @var string|null $type */
            $type = $adapterConfig['type'] ?? null;
            unset($adapterConfig['type']);

            // Step 2.2. Check if type has been defined in config. If not then throw an exception.
            if (null === $type) {
                throw new MissingAdapterTypeException($key);
            }

            // Step 2.3. Check if type defined in config is supported by bundle.
            if (false === in_array($type, array_keys(self::SUPPORTED_ADAPTERS))) {
                throw new AdapterNotSupportedException($type, array_keys(self::SUPPORTED_ADAPTERS));
            }

            // Step 2.4. Build and process the configuration typical for a given adapter type.
            $tree = new TreeBuilder('pb_smart_image/adapters/'.$key);
            $node = $tree->getRootNode();
            $this->buildAdapterNodeConfiguration($node, $type);

            $processor = new Processor();
            $processor->process($tree->buildTree(), [$adapterConfig]);

            // Step 2.5. Register adapter service.
            $this->registerAdapterService($container, $key, $type, $adapterConfig);
        }

        // Step 3. Change arguments in adapter registry service.
        $this->changeAdapterRegistryArgs($container, $defaultAdapter);
    }

    /**
     * Gets adapter configurator.
     *
     * @param string $type
     *
     * @return AdapterConfiguratorInterface
     */
    private function getAdapterConfigurator(string $type): AdapterConfiguratorInterface
    {
        if (false === isset($this->configurators[$type])) {
            $class = self::SUPPORTED_ADAPTERS[$type];
            $this->configurators[$type] = new $class();
        }

        return $this->configurators[$type];
    }

    /**
     * Build adapter node configuration.
     *
     * @param NodeDefinition $node
     * @param string $type
     */
    private function buildAdapterNodeConfiguration(NodeDefinition $node, string $type): void
    {
        $optionsNode = $node->children();
        $this->getAdapterConfigurator($type)->buildConfiguration($optionsNode);
    }

    /**
     * Register adapter service.
     *
     * @param ContainerBuilder $container
     * @param string $key
     * @param string $type
     * @param array $adapterConfig
     */
    private function registerAdapterService(ContainerBuilder $container, string $key, string $type, array $adapterConfig): void
    {
        $configurator = $this->getAdapterConfigurator($type);

        $adapterClass = $configurator->getAdapterClass();
        $adapterArgs = $configurator->buildAdapterArgsFromConfig($adapterConfig);
        $adapterDef = new Definition($adapterClass, $adapterArgs);

        $serviceKey = sprintf(self::ADAPTER_SERVICE_KEY_PATTERN, $key);
        $container->setDefinition($serviceKey, $adapterDef);

        $this->adapterServiceKeys[$key] = $serviceKey;
    }

    /**
     * Change arguments in adapter registry service.
     *
     * @param ContainerBuilder $container
     * @param string $default
     */
    private function changeAdapterRegistryArgs(ContainerBuilder $container, string $default): void
    {
        $adapterRefs = [];

        foreach ($this->adapterServiceKeys as $adapterKey => $adapterServiceKey) {
            $adapterRefs[$adapterKey] = new Reference($adapterServiceKey);
        }

        $registryDef = $container->findDefinition(AdapterRegistryInterface::class);

        $registryDef->setArgument(0, $adapterRefs);
        $registryDef->setArgument(1, $default);
    }
}
