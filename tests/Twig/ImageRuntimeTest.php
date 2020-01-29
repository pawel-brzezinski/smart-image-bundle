<?php

declare(strict_types=1);

namespace PB\Bundle\SmartImageBundle\Tests\Twig;

use PB\Bundle\SmartImageBundle\Adapter\{AdapterInterface, AdapterRegistryInterface};
use PB\Bundle\SmartImageBundle\Adapter\Exception\AdapterNotExistException;
use PB\Bundle\SmartImageBundle\Twig\ImageRuntime;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
class ImageRuntimeTest extends TestCase
{
    /** @var ObjectProphecy|AdapterRegistryInterface */
    private $adapterRegMock;

    /** @var ObjectProphecy|AdapterInterface */
    private $adapterMock;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->adapterRegMock = $this->prophesize(AdapterRegistryInterface::class);
        $this->adapterMock = $this->prophesize(AdapterInterface::class);
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        $this->adapterRegMock = null;
    }

    #######################
    # ImageRuntime::url() #
    #######################

    public function urlDataProvider(): array
    {
        // Dataset 1
        $source1 = '/path/to/image-1.jpg';
        $args1 = [$source1];
        $expected1 = 'http://service.example.com/path/to/image-1.jpg';

        // Dataset 2
        $source2 = '/path/to/image-2.jpg';
        $args2 = [$source2];
        $expected2 = 'http://service.example.com/path/to/image-2.jpg';

        // Dataset 3
        $source3 = '/path/to/image-3.jpg';
        $adapterKey3 = 'foobar';
        $args3 = [$source3, $adapterKey3];
        $expected3 = 'http://service.example.com/path/to/image-3.jpg';

        return [
            'adapter key is not defined' => [$expected1, $args1],
            'adapter key is defined as NULL' => [$expected2, $args2],
            'adapter key is defined as "foo"' => [$expected3, $args3],
        ];
    }

    /**
     * @dataProvider urlDataProvider
     *
     * @param string $expected
     * @param array $args
     *
     * @throws AdapterNotExistException
     */
    public function testShouldReturnServiceUrlToImage(string $expected, array $args)
    {
        // Given
        $source = $args[0];
        /** @var string|null $adapterKey */
        $adapterKey = $args[1] ?? null;

        if (null === $adapterKey) {
            // Mock AdapterRegistryInterface::default()
            $this->adapterRegMock->default()->shouldBeCalledTimes(1)->willReturn($this->adapterMock->reveal());
            // End
        } else {
            // Mock AdapterRegistryInterface::get()
            $this->adapterRegMock->get($adapterKey)->shouldBeCalledTimes(1)->willReturn($this->adapterMock->reveal());
            // End
        }

        // Mock AdapterInterface::getUrl()
        $this->adapterMock->getUrl($source)->shouldBeCalledTimes(1)->willReturn($expected);
        // End

        // When
        $actual = $this->createRuntime()->url(...$args);

        // Then
        $this->assertSame($expected, $actual);
    }

    #######
    # End #
    #######

    ########################################
    # ImageRuntime::transformationString() #
    ########################################

    public function transformationStringDataProvider(): array
    {
        // Dataset 1
        $trans1 = ['width' => 100];
        $args1 = [$trans1];
        $expected1 = 'width=100';

        // Dataset 2
        $trans2 = ['width' => 300, 'quality' => '50'];
        $args2 = [$trans2];
        $expected2 = 'width=300&quality=50';

        // Dataset 3
        $trans3 = [];
        $adapterKey3 = 'foobar';
        $args3 = [$trans3, $adapterKey3];
        $expected3 = '';

        return [
            'adapter key is not defined' => [$expected1, $args1],
            'adapter key is defined as NULL' => [$expected2, $args2],
            'adapter key is defined as "foo"' => [$expected3, $args3],
        ];
    }

    /**
     * @dataProvider transformationStringDataProvider
     *
     * @param string $expected
     * @param array $args
     *
     * @throws AdapterNotExistException
     */
    public function testShouldReturnServiceImageTransformationString(string $expected, array $args)
    {
        // Given
        $transformation = $args[0];
        /** @var string|null $adapterKey */
        $adapterKey = $args[1] ?? null;

        if (null === $adapterKey) {
            // Mock AdapterRegistryInterface::default()
            $this->adapterRegMock->default()->shouldBeCalledTimes(1)->willReturn($this->adapterMock->reveal());
            // End
        } else {
            // Mock AdapterRegistryInterface::get()
            $this->adapterRegMock->get($adapterKey)->shouldBeCalledTimes(1)->willReturn($this->adapterMock->reveal());
            // End
        }

        // Mock AdapterInterface::getUrl()
        $this->adapterMock->getTransformationString($transformation)->shouldBeCalledTimes(1)->willReturn($expected);
        // End

        // When
        $actual = $this->createRuntime()->transformationString(...$args);

        // Then
        $this->assertSame($expected, $actual);
    }

    #######
    # End #
    #######

    #######################
    # ImageRuntime::url() #
    #######################

    public function urlWithTransformationDataProvider(): array
    {
        // Dataset 1
        $source1 = '/path/to/image-1.jpg';
        $trans1 = ['width' => 100];
        $args1 = [$source1, $trans1];
        $expected1 = 'http://service.example.com/path/to/image-1.jpg?width=100';

        // Dataset 2
        $source2 = '/path/to/image-2.jpg';
        $trans2 = ['width' => 300, 'quality' => '50'];
        $args2 = [$source2, $trans2];
        $expected2 = 'http://service.example.com/path/to/image-2.jpg?width=300&quality=50';

        // Dataset 3
        $source3 = '/path/to/image-3.jpg';
        $trans3 = [];
        $adapterKey3 = 'foobar';
        $args3 = [$source3, $trans3, $adapterKey3];
        $expected3 = 'http://service.example.com/path/to/image-3.jpg';

        return [
            'adapter key is not defined' => [$expected1, $args1],
            'adapter key is defined as NULL' => [$expected2, $args2],
            'adapter key is defined as "foo"' => [$expected3, $args3],
        ];
    }

    /**
     * @dataProvider urlWithTransformationDataProvider
     *
     * @param string $expected
     * @param array $args
     *
     * @throws AdapterNotExistException
     */
    public function testShouldReturnServiceUrlToImageWithTransformationString(string $expected, array $args)
    {
        // Given
        $source = $args[0];
        $transformation = $args[1];
        /** @var string|null $adapterKey */
        $adapterKey = $args[2] ?? null;

        if (null === $adapterKey) {
            // Mock AdapterRegistryInterface::default()
            $this->adapterRegMock->default()->shouldBeCalledTimes(1)->willReturn($this->adapterMock->reveal());
            // End
        } else {
            // Mock AdapterRegistryInterface::get()
            $this->adapterRegMock->get($adapterKey)->shouldBeCalledTimes(1)->willReturn($this->adapterMock->reveal());
            // End
        }

        // Mock AdapterInterface::getUrl()
        $this->adapterMock->getUrlWithTransformation($source, $transformation)->shouldBeCalledTimes(1)->willReturn($expected);
        // End

        // When
        $actual = $this->createRuntime()->urlWithTransformation(...$args);

        // Then
        $this->assertSame($expected, $actual);
    }

    #######
    # End #
    #######

    /**
     * @return ImageRuntime
     */
    private function createRuntime(): ImageRuntime
    {
        return new ImageRuntime($this->adapterRegMock->reveal());
    }
}
