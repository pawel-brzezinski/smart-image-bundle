<?php

declare(strict_types=1);

namespace PB\Bundle\SmartImageBundle\Tests\DependencyInjection\Configurator\Adapter;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PB\Bundle\SmartImageBundle\Adapter\{StorageAdapter};
use PB\Bundle\SmartImageBundle\DependencyInjection\Configurator\Adapter\StorageAdapterConfigurator;
use PB\Bundle\SmartImageBundle\Tests\DependencyInjection\FakeAdapterConfiguration;
use PHPUnit\Framework\TestCase;

/**
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
class StorageAdapterConfiguratorTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * {@inheritDoc}
     */
    protected function getConfiguration(): FakeAdapterConfiguration
    {
        return new FakeAdapterConfiguration(new StorageAdapterConfigurator());
    }

    ####################################################
    # StorageAdapterConfigurator::buildConfiguration() #
    ####################################################

    /**
     * @return array
     */
    public function validConfigDataProvider(): array
    {
        // Dataset 1
        $config1 = [
            'url' => 'https://example-1.fra1.digitaloceanspaces.com',
        ];
        $breadcrumb1 = null;
        $expected1 = [
            'url' => 'https://example-1.fra1.digitaloceanspaces.com',
        ];

        // Dataset 2
        $config2 = [
            'url' => 'https://example-2.fra1.digitaloceanspaces.com',
        ];
        $breadcrumb2 = null;
        $expected2 = [
            'url' => 'https://example-2.fra1.digitaloceanspaces.com',
        ];

        // Dataset 3
        $config3 = [
            'url' => 'https://example-3.fra1.digitaloceanspaces.com',
            'path_prefix' => '/Images',
        ];
        $breadcrumb3 = null;
        $expected3 = [
            'url' => 'https://example-3.fra1.digitaloceanspaces.com',
            'path_prefix' => '/Images',
        ];

        return [
            'default config' => [$expected1, $config1, $breadcrumb1],
            'custom config with required only nodes' => [$expected2, $config2, $breadcrumb2],
            'custom config with all optional nodes' => [$expected3, $config3, $breadcrumb3],
        ];
    }

    /**
     * @dataProvider validConfigDataProvider
     *
     * @param array $expected
     * @param array $config
     * @param string|null $breadcrumb
     */
    public function testShouldCheckIfGivenConfigurationIsValid(array $expected, array $config, ?string $breadcrumb)
    {
        // Given

        // When & Then
        $this->assertProcessedConfigurationEquals([$config], $expected, $breadcrumb);
    }

    /**
     * @return array
     */
    public function invalidConfigDataProvider(): array
    {
        // Dataset 1
        $config1 = [];
        $expectedMsg1 = 'url';

        // Dataset 2
        $config2 = ['url' => ''];
        $expectedMsg2 = 'url';

        // Dataset 3
        $config3 = [
            'url' => 'https://example-3.fra1.digitaloceanspaces.com',
            'path_prefix' => '',
        ];
        $expectedMsg3 = 'path_prefix';

        return [
            'url node not defined' => [$expectedMsg1, $config1],
            'url node is empty' => [$expectedMsg2, $config2],
            'path_prefix node is empty' => [$expectedMsg3, $config3],
        ];
    }

    /**
     * @dataProvider invalidConfigDataProvider
     *
     * @param string|null $expectedMessage
     * @param array $config
     */
    public function testShouldCheckIfGivenConfigurationIsInvalid(?string $expectedMessage, array $config)
    {
        // Given

        // When & Then
        $this->assertConfigurationIsInvalid([$config], $expectedMessage);
    }

    #######
    # End #
    #######

    #################################################
    # StorageAdapterConfigurator::getAdapterClass() #
    #################################################

    /**
     *
     */
    public function testShouldCallGetAdapterClassAndReturnAdapterClassNamespace()
    {
        // Given
        $expected = StorageAdapter::class;

        // When
        $actual = $this->createConfigurator()->getAdapterClass();

        // Then
        $this->assertSame($expected, $actual);
    }

    #######
    # End #
    #######

    ############################################################
    # StorageAdapterConfigurator::buildAdapterArgsFromConfig() #
    ############################################################

    /**
     * @return array
     */
    public function buildAdapterArgsFromConfigDataProvider(): array
    {
        // Dataset 1
        $config1 = [
            'url' => 'https://example-1.fra1.digitaloceanspaces.com',
        ];
        $expected1 = ['https://example-1.fra1.digitaloceanspaces.com'];

        // Dataset 2
        $config2 = [
            'url' => 'https://example-2.fra1.digitaloceanspaces.com',
            'path_prefix' => '/Images-1',
        ];
        $expected2 = ['https://example-2.fra1.digitaloceanspaces.com', '/Images-1'];

        return [
            'config with required only items' => [$expected1, $config1],
            'config with all optional items' => [$expected2, $config2],
        ];
    }

    /**
     * @dataProvider buildAdapterArgsFromConfigDataProvider
     *
     * @param array $expected
     * @param array $config
     */
    public function testShouldCallBuildAdapterArgsFromConfigAndReturnArrayOfArgumentsForAdapter(array $expected, array $config)
    {
        // When
        $actual = $this->createConfigurator()->buildAdapterArgsFromConfig($config);

        // Then
        $this->assertSame($expected, $actual);
    }

    #######
    # End #
    #######

    /**
     * @return StorageAdapterConfigurator
     */
    private function createConfigurator(): StorageAdapterConfigurator
    {
        return new StorageAdapterConfigurator();
    }
}
