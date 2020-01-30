<?php

declare(strict_types=1);

namespace PB\Bundle\SmartImageBundle\Tests\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PB\Bundle\SmartImageBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;

/**
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * {@inheritDoc}
     */
    protected function getConfiguration(): Configuration
    {
        return new Configuration();
    }

    #########################################
    # Configuration::getConfigTreeBuilder() #
    #########################################

    /**
     * @return array
     */
    public function validConfigDataProvider(): array
    {
        // Dataset 1
        $config1 = [
            'default_adapter' => 'foo',
            'adapters' => [
                'adapter-1' => [
                    'type' => 'cloudimage',
                ],
            ],
        ];
        $breadcrumb1 = null;
        $expected1 = [
            'default_adapter' => 'foo',
            'adapters' => [
                'adapter-1' => [
                    'type' => 'cloudimage',
                ],
            ],
        ];

        // Dataset 2
        $config2 = [
            'default_adapter' => 'adapter-1',
            'adapters' => [
                'adapter-1' => [
                    'type' => 'cloudimage',
                ],
                'adapter-2' => [
                    'type' => 'cloudimage',
                ],
            ],
        ];
        $breadcrumb2 = null;
        $expected2 = [
            'default_adapter' => 'adapter-1',
            'adapters' => [
                'adapter-1' => [
                    'type' => 'cloudimage',
                ],
                'adapter-2' => [
                    'type' => 'cloudimage',
                ],
            ],
        ];

        return [
            'default config' => [$expected1, $config1, $breadcrumb1],
            'multiple clients config' => [$expected2, $config2, $breadcrumb2],
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
        $expectedMsg1 = 'default_adapter';

        // Dataset 2
        $config2 = ['default_adapter' => ''];
        $expectedMsg2 = 'default_adapter';

        // Dataset 3
        $config3 = ['default_adapter' => 'adapter-1'];
        $expectedMsg3 = 'adapters';

        // Dataset 3
        $config4 = ['default_adapter' => 'adapter-1', 'adapters' => []];
        $expectedMsg4 = 'adapters';

        return [
            'default_adapter node is not defined' => [$expectedMsg1, $config1],
            'default_adapter is empty' => [$expectedMsg2, $config2],
            'adapters node is not defined' => [$expectedMsg3, $config3],
            'adapters node is empty' => [$expectedMsg4, $config4],
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
}
