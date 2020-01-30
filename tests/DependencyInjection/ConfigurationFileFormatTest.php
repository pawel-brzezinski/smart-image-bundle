<?php

declare(strict_types=1);

namespace PB\Bundle\SmartImageBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionConfigurationTestCase;
use PB\Bundle\SmartImageBundle\DependencyInjection\{Configuration, PBSmartImageExtension};
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
class ConfigurationFileFormatTest extends AbstractExtensionConfigurationTestCase
{
    /**
     * {@inheritDoc}
     */
    protected function getContainerExtension(): ExtensionInterface
    {
        return new PBSmartImageExtension();
    }

    /**
     * {@inheritDoc}
     */
    protected function getConfiguration(): ConfigurationInterface
    {
        return new Configuration();
    }

    /**
     *
     */
    public function testShouldCheckIfDifferentConfigFileFormatsAreSupported()
    {
        // Given
        $sources = [
            __DIR__.'/Fixtures/config_1.yaml',
        ];
        $expected = [
            'default_adapter' => 'cloudimage-1',
            'adapters' => [
                'cloudimage-1' => [
                    'type' => 'cloudimage',
                    'url' => 'https://example-1.cloudimage.io',
                    'version' => 'v7',
                ],
                'cloudimage-2' => [
                    'type' => 'cloudimage',
                    'url' => 'https://example-2.cloudimage.io',
                    'version' => 'v7',
                ],
            ],
        ];


        // When & Then
        $this->assertProcessedConfigurationEquals($expected, $sources);
    }
}
