<?php

declare(strict_types=1);

namespace PB\Bundle\SmartImageBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use PB\Bundle\SmartImageBundle\Adapter\{AdapterRegistry, AdapterRegistryInterface};
use PB\Bundle\SmartImageBundle\DependencyInjection\Exception\{
    AdapterNotSupportedException,
    DefaultAdapterNotExistException,
    MissingAdapterTypeException
};
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
                'version' => 'v7'
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
        $this->assertContainerBuilderHasServiceDefinitionWithTag(HTMLExtension::class, 'twig.extension');
        $this->assertContainerBuilderHasServiceDefinitionWithTag(HTMLRuntime::class, 'twig.runtime');

        // Twig Image extension
        $this->assertContainerBuilderHasServiceDefinitionWithTag(ImageExtension::class, 'twig.extension');
        $this->assertContainerBuilderHasServiceDefinitionWithTag(ImageRuntime::class, 'twig.runtime');

        // Service for 'cloudimage_1' adapter
        $this->assertContainerBuilderHasServiceDefinitionWithArgument('pb_smart_image.adapter.cloudimage_1', 0, 'token-1');
        $this->assertContainerBuilderHasServiceDefinitionWithArgument('pb_smart_image.adapter.cloudimage_1', 1, 'v7');

        // Service for 'cloudimage_2' adapter
        $this->assertContainerBuilderHasServiceDefinitionWithArgument('pb_smart_image.adapter.cloudimage_2', 0, 'token-2');
        $this->assertContainerBuilderHasServiceDefinitionWithArgument('pb_smart_image.adapter.cloudimage_2', 1, 'v6');
        $this->assertContainerBuilderHasServiceDefinitionWithArgument('pb_smart_image.adapter.cloudimage_2', 2, '_storage_');

        // Adapter registry
        $adapterRefs = [
            'cloudimage_1' => new Reference('pb_smart_image.adapter.cloudimage_1'),
            'cloudimage_2' => new Reference('pb_smart_image.adapter.cloudimage_2'),
        ];

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(AdapterRegistry::class, 0, $adapterRefs);
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(AdapterRegistry::class, 1, 'cloudimage_1');
        $this->assertContainerBuilderHasAlias(AdapterRegistryInterface::class, AdapterRegistry::class);
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
        $this->expectExceptionMessage('Your "pb_smart_image" config "type" key "foo" is not supported. Supported types: cloudimage.');

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
