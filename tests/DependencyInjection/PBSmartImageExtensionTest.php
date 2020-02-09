<?php

declare(strict_types=1);

namespace PB\Bundle\SmartImageBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use PB\Bundle\SmartImageBundle\Adapter\{AdapterRegistry, AdapterRegistryInterface, CloudImageAdapter, StorageAdapter};
use PB\Bundle\SmartImageBundle\DependencyInjection\Exception\{AdapterNotSupportedException,
    DefaultAdapterNotExistException,
    MissingAdapterTypeException};
use PB\Bundle\SmartImageBundle\DependencyInjection\PBSmartImageExtension;
use PB\Bundle\SmartImageBundle\Twig\{HTMLExtension, HTMLRuntime, ImageExtension, ImageRuntime};
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
class PBSmartImageExtensionTest extends AbstractExtensionTestCase
{
    private const DEFAULT_CONFIG = [
        'default_adapter' => 'cloudimage_1',
        'adapters' => [
            'cloudimage_1' => [
                'type' => 'cloudimage',
                'token' => 'token-1',
                'version' => 'v7',
            ],
            'storage_1' => [
                'type' => 'storage',
                'url' => 'https://example-1.fra1.digitaloceanspaces.com',
                'path_prefix' => 'images',
            ],
            'cloudimage_2' => [
                'type' => 'cloudimage',
                'token' => 'token-2',
                'version' => 'v6',
                'alias' => '_storage_',
            ],
        ],
    ];

    /**
     * {@inheritDoc}
     */
    protected function getContainerExtensions(): array
    {
        return [
            new PBSmartImageExtension(),
        ];
    }

    #################################
    # PBSmartImageExtension::load() #
    #################################

    /**
     *
     */
    public function testShouldCheckIfContainerBuilderHasAllServicesDefined()
    {
        // When
        $this->load(self::DEFAULT_CONFIG);

        // Then

        // Twig HTML extension
        $this->assertContainerBuilderHasService('pb_smart_image.html.twig_extension', HTMLExtension::class);
        $this->assertContainerBuilderHasServiceDefinitionWithTag('pb_smart_image.html.twig_extension', 'twig.extension');
        $this->assertContainerBuilderHasService('pb_smart_image.html.twig_runtime', HTMLRuntime::class);
        $this->assertContainerBuilderHasServiceDefinitionWithTag('pb_smart_image.html.twig_runtime', 'twig.runtime');

        // Twig Image extension
        $this->assertContainerBuilderHasService('pb_smart_image.image.twig_extension', ImageExtension::class);
        $this->assertContainerBuilderHasServiceDefinitionWithTag('pb_smart_image.image.twig_extension', 'twig.extension');
        $this->assertContainerBuilderHasService('pb_smart_image.image.twig_runtime', ImageRuntime::class);
        $this->assertContainerBuilderHasServiceDefinitionWithTag('pb_smart_image.image.twig_runtime', 'twig.runtime');

        // Service for 'cloudimage_1' adapter
        $this->assertContainerBuilderHasService('pb_smart_image.adapter.cloudimage_1', CloudImageAdapter::class);
        $this->assertContainerBuilderHasServiceDefinitionWithArgument('pb_smart_image.adapter.cloudimage_1', 0, 'token-1');
        $this->assertContainerBuilderHasServiceDefinitionWithArgument('pb_smart_image.adapter.cloudimage_1', 1, 'v7');

        // Service for 'storage_1' adapter
        $this->assertContainerBuilderHasService('pb_smart_image.adapter.storage_1', StorageAdapter::class);
        $this->assertContainerBuilderHasServiceDefinitionWithArgument('pb_smart_image.adapter.storage_1', 0, 'https://example-1.fra1.digitaloceanspaces.com');
        $this->assertContainerBuilderHasServiceDefinitionWithArgument('pb_smart_image.adapter.storage_1', 1, 'images');

        // Service for 'cloudimage_2' adapter
        $this->assertContainerBuilderHasService('pb_smart_image.adapter.cloudimage_2', CloudImageAdapter::class);
        $this->assertContainerBuilderHasServiceDefinitionWithArgument('pb_smart_image.adapter.cloudimage_2', 0, 'token-2');
        $this->assertContainerBuilderHasServiceDefinitionWithArgument('pb_smart_image.adapter.cloudimage_2', 1, 'v6');
        $this->assertContainerBuilderHasServiceDefinitionWithArgument('pb_smart_image.adapter.cloudimage_2', 2, '_storage_');

        // Adapter registry
        $adapterRefs = [
            'cloudimage_1' => new Reference('pb_smart_image.adapter.cloudimage_1'),
            'storage_1' => new Reference('pb_smart_image.adapter.storage_1'),
            'cloudimage_2' => new Reference('pb_smart_image.adapter.cloudimage_2'),
        ];

        $this->assertContainerBuilderHasService('pb_smart_image.adapter_registry', AdapterRegistry::class);
        $this->assertContainerBuilderHasServiceDefinitionWithArgument('pb_smart_image.adapter_registry', 0, $adapterRefs);
        $this->assertContainerBuilderHasServiceDefinitionWithArgument('pb_smart_image.adapter_registry', 1, 'cloudimage_1');
        $this->assertContainerBuilderHasAlias(AdapterRegistryInterface::class, 'pb_smart_image.adapter_registry');
    }

    /**
     *
     */
    public function testShouldThrowDefaultAdapterNotExistExceptionWhenAdapterMarkedAsDefaultDoesNotExist()
    {
        // Expect
        $this->expectException(DefaultAdapterNotExistException::class);
        $this->expectExceptionMessage('The adapter "foo" marked as default adapter is not defined.');

        // Given
        $config = [
            'default_adapter' => 'foo',
            'adapters' => [
                'adapter_1' => [
                    'type' => 'cloudimage',
                    'token' => 'token-1',
                    'version' => 'v7'
                ],
                'adapter_2' => [
                    'token' => 'token-2',
                    'version' => 'v7'
                ],
                'adapter_3' => [
                    'type' => 'cloudimage',
                    'token' => 'token-3',
                    'version' => 'v7'
                ],
            ],
        ];

        // When
        $this->load($config);
    }

    /**
     *
     */
    public function testShouldThrowMissingAdapterTypeExceptionWhenOneOfTheAdapterConfigDoNotContainTheType()
    {
        // Expect
        $this->expectException(MissingAdapterTypeException::class);
        $this->expectExceptionMessage('Your "pb_smart_image.adapters.adapter_2" config entry do not contain the "type" key.');

        // Given
        $config = [
            'default_adapter' => 'adapter_1',
            'adapters' => [
                'adapter_1' => [
                    'type' => 'cloudimage',
                    'token' => 'token-1',
                    'version' => 'v7'
                ],
                'adapter_2' => [
                    'token' => 'token-2',
                    'version' => 'v7'
                ],
                'adapter_3' => [
                    'type' => 'cloudimage',
                    'token' => 'token-3',
                    'version' => 'v7'
                ],
            ],
        ];

        // When
        $this->load($config);
    }

    /**
     *
     */
    public function testShouldThrowAdapterNotSupportedExceptionWhenOneOfTheTypesIsNotSupported()
    {
        // Expect
        $this->expectException(AdapterNotSupportedException::class);
        $this->expectExceptionMessage('Your "pb_smart_image" config "type" key "foo" is not supported. Supported types: cloudimage, storage.');

        // Given
        $config = [
            'default_adapter' => 'adapter_1',
            'adapters' => [
                'adapter_1' => [
                    'type' => 'cloudimage',
                    'token' => 'token-1',
                    'version' => 'v7'
                ],
                'adapter_2' => [
                    'type' => 'foo',
                ],
                'adapter_3' => [
                    'type' => 'cloudimage',
                    'token' => 'token-3',
                    'version' => 'v7'
                ],
            ],
        ];

        // When
        $this->load($config)

        ;
    }

    /**
     *
     */
    public function testShouldThrowInvalidConfigurationExceptionWhenOneOfTheAdapterConfigurationIsNotCorrect()
    {
        // Expect
        $this->expectException(InvalidConfigurationException::class);

        // Given
        $config = [
            'default_adapter' => 'adapter_1',
            'adapters' => [
                'adapter_1' => [
                    'type' => 'cloudimage',
                    'token' => 'token-1',
                    'version' => 'v7'
                ],
                'adapter_2' => [
                    'type' => 'cloudimage',
                    'version' => 'v7'
                ],
                'adapter_3' => [
                    'type' => 'cloudimage',
                    'token' => 'token-3',
                    'version' => 'v7'
                ],
            ],
        ];

        // When
        $this->load($config);
    }

    #######
    # End #
    #######

}
