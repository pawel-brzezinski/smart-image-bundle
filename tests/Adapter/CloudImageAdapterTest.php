<?php

declare(strict_types=1);

namespace PB\Bundle\SmartImageBundle\Tests\Adapter;

use PB\Bundle\SmartImageBundle\Adapter\CloudImageAdapter;
use PB\Bundle\SmartImageBundle\Tests\Library\Reflection;
use PHPUnit\Framework\TestCase;

/**
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
class CloudImageAdapterTest extends TestCase
{
    private const DEFAULT_TOKEN = 'example';
    private const DEFAULT_VERSION = 'v7';

    ####################################
    # CloudImageAdapter::__construct() #
    ####################################

    /**
     * @return array
     */
    public function createAdapterDataProvider(): array
    {
        // Dataset 1
        $token1 = 'example-1';
        $version1 = 'v7';
        $args1 = [$token1, $version1];

        // Dataset 2
        $token2 = 'example-2';
        $version2 = 'v6';
        $alias2 = '_storage_';
        $args2 = [$token2, $version2, $alias2];

        return [
            'default adapter args' => [$token1, $version1, null, $args1],
            'custom alias' => [$token2, $version2, $alias2, $args2],
        ];
    }

    /**
     * @dataProvider createAdapterDataProvider
     *
     * @param string $expectedToken
     * @param string $expectedVersion
     * @param string|null $expectedAlias
     * @param array $args
     *
     * @throws \ReflectionException
     */
    public function testShouldCreateAdapterObjectAndCheckIfPropertiesHasBeenSetCorrect(
        string $expectedToken,
        string $expectedVersion,
        ?string $expectedAlias,
        array $args
    ) {
        // When
        $objectUnderTest = new CloudImageAdapter(...$args);

        // Then
        $this->assertSame($expectedToken, Reflection::getPropertyValue($objectUnderTest, 'token'));
        $this->assertSame($expectedVersion, Reflection::getPropertyValue($objectUnderTest, 'version'));
        $this->assertSame($expectedAlias, Reflection::getPropertyValue($objectUnderTest, 'alias'));
    }

    #######
    # End #
    #######

    ###############################
    # CloudImageAdapter::getUrl() #
    ###############################

    /**
     * @return array
     */
    public function getUrlDataProvider(): array
    {
        return [
            ['https://example-1.cloudimg.io/v7/path/to/image-1.jpg', 'example-1', 'v7', null, 'path/to/image-1.jpg'],
            ['https://example-2.cloudimg.io/v7/_storage2_/path/to/image-2.jpg', 'example-2', 'v7', '_storage2_', '/path/to/image-2.jpg'],
            ['https://example-3.cloudimg.io/v7/_storage3_/path/to/image-3.jpg', 'example-3', 'v7', '_storage3_/', 'path/to/image-3.jpg/'],
            ['https://example-4.cloudimg.io/v7/_storage4_/path/to/image-4.jpg', 'example-4', 'v7', '/_storage4_', '/path/to/image-4.jpg/'],
            ['https://example-5.cloudimg.io/v6/_storage5_/path/to/image-5.jpg', 'example-5', 'v6', '/_storage5_/', 'path/to/image-5.jpg'],
            ['https://example-6.cloudimg.io/v6/https://storage.example.com/path/to/image-5.jpg', 'example-6', 'v6', 'https://storage.example.com', 'path/to/image-5.jpg'],
        ];
    }

    /**
     * @dataProvider getUrlDataProvider
     *
     * @param string $expected
     * @param string $token
     * @param string $version
     * @param string $alias
     * @param string $source
     */
    public function testShouldCallGetUrlAndReturnServiceImageUrl(string $expected, string $token, string $version, ?string $alias, string $source)
    {
        // When
        $actual = $this->createAdapter($token, $version, $alias)->getUrl($source);

        // Then
        $this->assertSame($expected, $actual);
    }

    #######
    # End #
    #######

    ################################################
    # CloudImageAdapter::getTransformationString() #
    ################################################

    /**
     * @return array
     */
    public function getTransformationStringDataProvider(): array
    {
        return [
            ['', []],
            ['width=400', ['width' => 400]],
            ['width=200&wat=1&grey=1', ['width' => 200, 'wat' => 1, 'grey' => 1]],
        ];
    }

    /**
     * @dataProvider getTransformationStringDataProvider
     *
     * @param string $expected
     * @param array $transformation
     */
    public function testShouldCallGetTransformationStringAndReturnServiceTransformationString(string $expected, array $transformation)
    {
        // When
        $actual = $this->createAdapter()->getTransformationString($transformation);

        // Then
        $this->assertSame($expected, $actual);
    }

    #######
    # End #
    #######

    #################################################
    # CloudImageAdapter::getUrlWithTransformation() #
    #################################################

    /**
     * @return array
     */
    public function getUrlWithTransformationDataProvider(): array
    {
        // Dataset 1
        $token1 = 'example-1';
        $source1 = 'path/to/image-1.jpg';
        $trans1 = [];
        $expected1 = 'https://example-1.cloudimg.io/v7/path/to/image-1.jpg';

        // Dataset 2
        $token2 = 'example-2';
        $source2 = '/path/to/image-2.jpg';
        $trans2 = ['width' => 800, 'height' => '600', 'q' => 5];
        $expected2 = 'https://example-2.cloudimg.io/v7/path/to/image-2.jpg?width=800&height=600&q=5';

        return [
            [$expected1, $token1, $source1, $trans1],
            [$expected2, $token2, $source2, $trans2],
        ];
    }

    /**
     * @dataProvider getUrlWithTransformationDataProvider
     *
     * @param string $expected
     * @param string $apiUrl
     * @param string $source
     * @param array $transformation
     */
    public function testShouldCallGetUrlWithTransformationAndReturnServiceImageUrlWithTransformationString(
        string $expected,
        string $apiUrl,
        string $source,
        array $transformation
    ) {
        // When
        $actual = $this->createAdapter($apiUrl)->getUrlWithTransformation($source, $transformation);

        // Then
        $this->assertSame($expected, $actual);
    }

    #######
    # End #
    #######

    /**
     * @param string $token
     * @param string $version
     * @param string|null $alias
     *
     * @return CloudImageAdapter
     */
    private function createAdapter(
        string $token = self::DEFAULT_TOKEN,
        string $version = self::DEFAULT_VERSION,
        string $alias = null
    ): CloudImageAdapter {
        return new CloudImageAdapter($token, $version, $alias);
    }
}
