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
    private const DEFAULT_API_URL = 'https://example.cloudimg.io';
    private const DEFAULT_API_VERSION = 'v7';

    ####################################
    # CloudImageAdapter::__construct() #
    ####################################

    /**
     * @return array
     */
    public function createAdapterDataProvider(): array
    {
        // Dataset 1
        $apiUrl1 = 'https://example-1.cloudimg.io';
        $apiVersion1 = 'v7';
        $args1 = [$apiUrl1, $apiVersion1];

        // Dataset 2
        $apiUrl2 = 'https://example-2.cloudimg.io';
        $apiVersion2 = 'v6';
        $alias2 = '_storage_';
        $args2 = [$apiUrl2, $apiVersion2, $alias2];

        return [
            'default adapter args' => [$apiUrl1, $apiVersion1, null, $args1],
            'custom alias' => [$apiUrl2, $apiVersion2, $alias2, $args2],
        ];
    }

    /**
     * @dataProvider createAdapterDataProvider
     *
     * @param string $expectedApiUrl
     * @param string $expectedApiVersion
     * @param string|null $expectedAlias
     * @param array $args
     *
     * @throws \ReflectionException
     */
    public function testShouldCreateAdapterObjectAndCheckIfPropertiesHasBeenSetCorrect(
        string $expectedApiUrl,
        string $expectedApiVersion,
        ?string $expectedAlias,
        array $args
    ) {
        // When
        $objectUnderTest = new CloudImageAdapter(...$args);

        // Then
        $this->assertSame($expectedApiUrl, Reflection::getPropertyValue($objectUnderTest, 'apiUrl'));
        $this->assertSame($expectedApiVersion, Reflection::getPropertyValue($objectUnderTest, 'apiVersion'));
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
            ['https://example.cloudimg.io/v7/path/to/image-1.jpg', 'https://example.cloudimg.io', null, 'path/to/image-1.jpg'],
            ['https://example.cloudimg.io/v7/_storage2_/path/to/image-2.jpg', 'https://example.cloudimg.io/', '_storage2_', '/path/to/image-2.jpg'],
            ['https://example.cloudimg.io/v7/_storage3_/path/to/image-3.jpg', 'https://example.cloudimg.io', '_storage3_/', 'path/to/image-3.jpg/'],
            ['https://example.cloudimg.io/v7/_storage4_/path/to/image-4.jpg', 'https://example.cloudimg.io/', '/_storage4_', '/path/to/image-4.jpg/'],
            ['https://example.cloudimg.io/v7/_storage5_/path/to/image-5.jpg', 'https://example.cloudimg.io', '/_storage5_/', 'path/to/image-5.jpg'],
            ['https://example.cloudimg.io/v7/https://storage.example.com/path/to/image-5.jpg', 'https://example.cloudimg.io', 'https://storage.example.com', 'path/to/image-5.jpg'],
        ];
    }

    /**
     * @dataProvider getUrlDataProvider
     *
     * @param string $expected
     * @param string $apiUrl
     * @param string $alias
     * @param string $source
     */
    public function testShouldCallGetUrlAndReturnServiceImageUrl(string $expected, string $apiUrl, ?string $alias, string $source)
    {
        // When
        $actual = $this->createAdapter($apiUrl, self::DEFAULT_API_VERSION, $alias)->getUrl($source);

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
        $apiUrl1 = 'https://example.cloudimg.io';
        $source1 = 'path/to/image-1.jpg';
        $trans1 = [];
        $expected1 = 'https://example.cloudimg.io/v7/path/to/image-1.jpg';

        // Dataset 2
        $apiUrl2 = 'https://example.cloudimg.io/';
        $source2 = '/path/to/image-2.jpg';
        $trans2 = ['width' => 800, 'height' => '600', 'q' => 5];
        $expected2 = 'https://example.cloudimg.io/v7/path/to/image-2.jpg?width=800&height=600&q=5';

        return [
            [$expected1, $apiUrl1, $source1, $trans1],
            [$expected2, $apiUrl2, $source2, $trans2],
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
     * @param string $apiUrl
     * @param string $apiVersion
     * @param string|null $alias
     *
     * @return CloudImageAdapter
     */
    private function createAdapter(
        string $apiUrl = self::DEFAULT_API_URL,
        string $apiVersion = self::DEFAULT_API_VERSION,
        string $alias = null
    ): CloudImageAdapter {
        return new CloudImageAdapter($apiUrl, $apiVersion, $alias);
    }
}
