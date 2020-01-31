<?php

declare(strict_types=1);

namespace PB\Bundle\SmartImageBundle\Tests\Adapter;

use PB\Bundle\SmartImageBundle\Adapter\StorageAdapter;
use PB\Bundle\SmartImageBundle\Tests\Library\Reflection;
use PHPUnit\Framework\TestCase;

/**
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
class StorageAdapterTest extends TestCase
{
    private const DEFAULT_URL='https://example.fra1.digitaloceanspaces.com';
    private const DEFAULT_PATH_PREFIX = '/Images';

    #################################
    # StorageAdapter::__construct() #
    #################################

    /**
     * @return array
     */
    public function createAdapterDataProvider(): array
    {
        // Dataset 1
        $url1 = 'https://example-1.fra1.digitaloceanspaces.com';
        $args1 = [$url1];

        // Dataset 2
        $url2 = 'https://example-2.fra1.digitaloceanspaces.com';
        $pathPrefix2 = '/dataset-2';
        $args2 = [$url2, $pathPrefix2];

        return [
            'default adapter args' => [$url1, null, $args1],
            'custom alias' => [$url2, $pathPrefix2, $args2],
        ];
    }

    /**
     * @dataProvider createAdapterDataProvider
     *
     * @param string $expectedToken
     * @param string|null $expectedPathPrefix
     * @param array $args
     *
     * @throws \ReflectionException
     */
    public function testShouldCreateAdapterObjectAndCheckIfPropertiesHasBeenSetCorrect(
        string $expectedToken,
        ?string $expectedPathPrefix,
        array $args
    ) {
        // When
        $objectUnderTest = new StorageAdapter(...$args);

        // Then
        $this->assertSame($expectedToken, Reflection::getPropertyValue($objectUnderTest, 'url'));
        $this->assertSame($expectedPathPrefix, Reflection::getPropertyValue($objectUnderTest, 'pathPrefix'));
    }

    #######
    # End #
    #######

    ############################
    # StorageAdapter::getUrl() #
    ############################

    /**
     * @return array
     */
    public function getUrlDataProvider(): array
    {
        return [
            ['https://example-1.fra1.digitaloceanspaces.com/path/to/image-1.jpg', 'https://example-1.fra1.digitaloceanspaces.com', null, 'path/to/image-1.jpg'],
            ['https://example-2.fra1.digitaloceanspaces.com/_storage2_/path/to/image-2.jpg', 'https://example-2.fra1.digitaloceanspaces.com/', '_storage2_', '/path/to/image-2.jpg'],
            ['https://example-3.fra1.digitaloceanspaces.com/_storage3_/path/to/image-3.jpg', 'https://example-3.fra1.digitaloceanspaces.com', '_storage3_/', 'path/to/image-3.jpg/'],
            ['https://example-4.fra1.digitaloceanspaces.com/_storage4_/path/to/image-4.jpg', 'https://example-4.fra1.digitaloceanspaces.com/', '/_storage4_', '/path/to/image-4.jpg/'],
            ['https://example-5.fra1.digitaloceanspaces.com/_storage5_/path/to/image-5.jpg', 'https://example-5.fra1.digitaloceanspaces.com', '/_storage5_/', 'path/to/image-5.jpg'],
        ];
    }

    /**
     * @dataProvider getUrlDataProvider
     *
     * @param string $expected
     * @param string $url
     * @param string|null $pathPrefix
     * @param string $source
     */
    public function testShouldCallGetUrlAndReturnServiceImageUrl(string $expected, string $url, ?string $pathPrefix, string $source)
    {
        // When
        $actual = $this->createAdapter($url, $pathPrefix)->getUrl($source);

        // Then
        $this->assertSame($expected, $actual);
    }

    #######
    # End #
    #######

    #############################################
    # StorageAdapter::getTransformationString() #
    #############################################

    /**
     *
     */
    public function testShouldCallGetTransformationStringAndReturnServiceTransformationString()
    {
        // When
        $actual = $this->createAdapter()->getTransformationString(['any' => 'options']);

        // Then
        $this->assertSame('', $actual);
    }

    #######
    # End #
    #######

    ##############################################
    # StorageAdapter::getUrlWithTransformation() #
    ##############################################

    /**
     * @return array
     */
    public function getUrlWithTransformationDataProvider(): array
    {
        // Dataset 1
        $url1 = 'https://example-1.fra1.digitaloceanspaces.com';;
        $source1 = 'path/to/image-1.jpg';
        $trans1 = [];
        $expected1 = 'https://example-1.fra1.digitaloceanspaces.com/path/to/image-1.jpg';

        // Dataset 2
        $url2 = 'https://example-1.fra1.digitaloceanspaces.com';;
        $source2 = '/path/to/image-2.jpg';
        $trans2 = ['width' => 800, 'height' => '600', 'q' => 5];
        $expected2 = 'https://example-1.fra1.digitaloceanspaces.com/path/to/image-2.jpg';

        return [
            [$expected1, $url1, $source1, $trans1],
            [$expected2, $url2, $source2, $trans2],
        ];
    }

    /**
     * @dataProvider getUrlWithTransformationDataProvider
     *
     * @param string $expected
     * @param string $url
     * @param string $source
     * @param array $transformation
     */
    public function testShouldCallGetUrlWithTransformationAndReturnServiceImageUrlWithTransformationString(
        string $expected,
        string $url,
        string $source,
        array $transformation
    ) {
        // When
        $actual = $this->createAdapter($url, null)->getUrlWithTransformation($source, $transformation);

        // Then
        $this->assertSame($expected, $actual);
    }

    #######
    # End #
    #######

    /**
     * @param string $url
     * @param string|null $pathPrefix
     *
     * @return StorageAdapter
     */
    private function createAdapter(string $url = self::DEFAULT_URL, ?string $pathPrefix = self::DEFAULT_PATH_PREFIX): StorageAdapter
    {
        return new StorageAdapter($url, $pathPrefix);
    }
}
