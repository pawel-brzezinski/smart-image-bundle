<?php

declare(strict_types=1);

namespace PB\Bundle\SmartImageBundle\Tests\DependencyInjection\Configurator\Adapter;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PB\Bundle\SmartImageBundle\Adapter\{CloudImageAdapter};
use PB\Bundle\SmartImageBundle\DependencyInjection\Configurator\Adapter\CloudImageAdapterConfigurator;
use PB\Bundle\SmartImageBundle\Tests\DependencyInjection\FakeAdapterConfiguration;
use PHPUnit\Framework\TestCase;

/**
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
class CloudImageAdapterConfiguratorTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * {@inheritDoc}
     */
    protected function getConfiguration(): FakeAdapterConfiguration
    {
        return new FakeAdapterConfiguration(new CloudImageAdapterConfigurator());
    }

    #######################################################
    # CloudImageAdapterConfigurator::buildConfiguration() #
    #######################################################

    /**
     * @return array
     */
    public function validConfigDataProvider(): array
    {
        // Dataset 1
        $config1 = [
            'url' => 'http://token1.cloudimage.io',
        ];
        $breadcrumb1 = null;
        $expected1 = [
            'url' => 'http://token1.cloudimage.io',
            'version' => 'v7',
        ];

        // Dataset 2
        $config2 = [
            'url' => 'http://token2.cloudimage.io',
            'version' => 'v6',
        ];
        $breadcrumb2 = null;
        $expected2 = [
            'url' => 'http://token2.cloudimage.io',
            'version' => 'v6',
        ];

        // Dataset 3
        $config3 = [
            'url' => 'http://token3.cloudimage.io',
            'version' => 'v7',
            'alias' => '_storage_',
        ];
        $breadcrumb3 = null;
        $expected3 = [
            'url' => 'http://token3.cloudimage.io',
            'version' => 'v7',
            'alias' => '_storage_',
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
            'url' => 'http://token.cloudimage.io',
            'version' => '',
        ];
        $expectedMsg3 = 'version';

        // Dataset 4
        $config4 = [
            'url' => 'http://token.cloudimage.io',
            'version' => 'v7',
            'alias' => '',
        ];
        $expectedMsg4 = 'alias';

        return [
            'url node not defined' => [$expectedMsg1, $config1],
            'url node is empty' => [$expectedMsg2, $config2],
            'version node is empty' => [$expectedMsg3, $config3],
            'alias node is empty' => [$expectedMsg4, $config4],
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

    ####################################################
    # CloudImageAdapterConfigurator::getAdapterClass() #
    ####################################################

    /**
     *
     */
    public function testShouldCallGetAdapterClassAndReturnAdapterClassNamespace()
    {
        // Given
        $expected = CloudImageAdapter::class;

        // When
        $actual = $this->createConfigurator()->getAdapterClass();

        // Then
        $this->assertSame($expected, $actual);
    }

    #######
    # End #
    #######

    ###############################################################
    # CloudImageAdapterConfigurator::buildAdapterArgsFromConfig() #
    ###############################################################

    /**
     * @return array
     */
    public function buildAdapterArgsFromConfigDataProvider(): array
    {
        // Dataset 1
        $config1 = [
            'url' => 'https://example.cloudimage.io',
            'version' => 'v7',
        ];
        $expected1 = ['https://example.cloudimage.io', 'v7'];

        // Dataset 2
        $config2 = [
            'url' => 'https://example.cloudimage.io',
            'version' => 'v7',
            'alias' => '_storage_',
        ];
        $expected2 = ['https://example.cloudimage.io', 'v7', '_storage_'];

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
     * @return CloudImageAdapterConfigurator
     */
    private function createConfigurator(): CloudImageAdapterConfigurator
    {
        return new CloudImageAdapterConfigurator();
    }
}
