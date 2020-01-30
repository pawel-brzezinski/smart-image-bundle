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
            'token' => 'token-1',
        ];
        $breadcrumb1 = null;
        $expected1 = [
            'token' => 'token-1',
            'version' => 'v7',
        ];

        // Dataset 2
        $config2 = [
            'token' => 'token-2',
            'version' => 'v6',
        ];
        $breadcrumb2 = null;
        $expected2 = [
            'token' => 'token-2',
            'version' => 'v6',
        ];

        // Dataset 3
        $config3 = [
            'token' => 'token-3',
            'version' => 'v7',
            'alias' => '_storage_',
        ];
        $breadcrumb3 = null;
        $expected3 = [
            'token' => 'token-3',
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
        $expectedMsg1 = 'token';

        // Dataset 2
        $config2 = ['token' => ''];
        $expectedMsg2 = 'token';

        // Dataset 3
        $config3 = [
            'token' => 'token',
            'version' => '',
        ];
        $expectedMsg3 = 'version';

        // Dataset 4
        $config4 = [
            'token' => 'token',
            'version' => 'v7',
            'alias' => '',
        ];
        $expectedMsg4 = 'alias';

        return [
            'token node not defined' => [$expectedMsg1, $config1],
            'token node is empty' => [$expectedMsg2, $config2],
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
            'token' => 'token-1',
            'version' => 'v7',
        ];
        $expected1 = ['token-1', 'v7'];

        // Dataset 2
        $config2 = [
            'token' => 'token-2',
            'version' => 'v7',
            'alias' => '_storage_',
        ];
        $expected2 = ['token-2', 'v7', '_storage_'];

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
