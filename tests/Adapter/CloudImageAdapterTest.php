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
     * @throws \ReflectionException
     */
    public function testShouldCreateAdapterObjectAndCheckIfPropertiesHasBeenSetCorrect()
    {
        // When
        $objectUnderTest = $this->createAdapter();

        // Then
        $this->assertSame(self::DEFAULT_API_URL, Reflection::getPropertyValue($objectUnderTest, 'apiUrl'));
        $this->assertSame(self::DEFAULT_API_VERSION, Reflection::getPropertyValue($objectUnderTest, 'apiVersion'));
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
            ['https://example.cloudimg.io/v7/path/to/image-1.jpg', 'https://example.cloudimg.io', 'path/to/image-1.jpg'],
            ['https://example.cloudimg.io/v7/path/to/image-2.jpg', 'https://example.cloudimg.io/', '/path/to/image-2.jpg'],
            ['https://example.cloudimg.io/v7/path/to/image-3.jpg', 'https://example.cloudimg.io', 'path/to/image-3.jpg/'],
            ['https://example.cloudimg.io/v7/path/to/image-4.jpg', 'https://example.cloudimg.io/', '/path/to/image-4.jpg/'],
        ];
    }

    /**
     * @dataProvider getUrlDataProvider
     *
     * @param string $expected
     * @param string $apiUrl
     * @param string $source
     */
    public function testShouldCallGetUrlAndReturnServiceImageUrl(string $expected, string $apiUrl, string $source)
    {
        // When
        $actual = $this->createAdapter($apiUrl)->getUrl($source);

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
     *
     * @return CloudImageAdapter
     */
    private function createAdapter(string $apiUrl = self::DEFAULT_API_URL): CloudImageAdapter
    {
        return new CloudImageAdapter($apiUrl, self::DEFAULT_API_VERSION);
    }
}
