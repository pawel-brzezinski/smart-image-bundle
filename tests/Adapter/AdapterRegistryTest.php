<?php

declare(strict_types=1);

namespace PB\Bundle\SmartImageBundle\Tests\Adapter;

use PB\Bundle\SmartImageBundle\Adapter\AdapterInterface;
use PB\Bundle\SmartImageBundle\Adapter\AdapterRegistry;
use PB\Bundle\SmartImageBundle\Adapter\Exception\AdapterNotExistException;
use PB\Bundle\SmartImageBundle\Tests\Library\Reflection;
use PHPUnit\Framework\TestCase;

/**
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
class AdapterRegistryTest extends TestCase
{
    private const DEFAULT_ADAPTER = 'adapter_2';

    /** @var AdapterInterface[] */
    private $adapters = [];

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->adapters['adapter_1'] = FakeAdapter::create();
        $this->adapters['adapter_2'] = FakeAdapter::create();
        $this->adapters['adapter_3'] = FakeAdapter::create();
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        $this->adapters = [];
    }

    ##################################
    # AdapterRegistry::__construct() #
    ##################################

    /**
     * @throws \ReflectionException
     */
    public function testShouldCreateRegistryInstance()
    {
        // Given
        $registryUnderTest = $this->createRegistry();

        // When
        $actualAdapters = Reflection::getPropertyValue($registryUnderTest, 'adapters');
        $actualDefaultAdapter = Reflection::getPropertyValue($registryUnderTest, 'defaultAdapter');

        // Then
        $this->assertInstanceOf(\ArrayObject::class, $actualAdapters);
        $this->assertSame($this->adapters, $actualAdapters->getArrayCopy());
        $this->assertSame(self::DEFAULT_ADAPTER, $actualDefaultAdapter);
    }

    #######
    # End #
    #######

    ##########################
    # AdapterRegistry::all() #
    ##########################

    /**
     *
     */
    public function testShouldGetAllDefinedAdapters()
    {
        // Given
        $registryUnderTest = $this->createRegistry();

        // When
        $actual = $registryUnderTest->all();

        // Then
        $this->assertInstanceOf(\ArrayObject::class, $actual);
        $this->assertSame($this->adapters, $actual->getArrayCopy());
    }

    #######
    # End #
    #######

    ###########################
    # AdapterRegistry::keys() #
    ###########################

    /**
     *
     */
    public function testShouldGetAllDefinedAdapterKeys()
    {
        // Given
        $registryUnderTest = $this->createRegistry();
        $expected = ['adapter_1', 'adapter_2', 'adapter_3'];

        // When
        $actual = $registryUnderTest->keys();

        // Then
        $this->assertSame($expected, $actual);
    }

    #######
    # End #
    #######

    ##########################
    # AdapterRegistry::has() #
    ##########################

    /**
     * @return array
     */
    public function hasDataProvider(): array
    {
        return [
            [true, 'adapter_1'],
            [true, 'adapter_2'],
            [true, 'adapter_3'],
            [false, 'adapter_X'],
        ];
    }

    /**
     * @dataProvider hasDataProvider
     *
     * @param bool $expected
     * @param string $key
     */
    public function testShouldReturnFlagWhichDetermineIfAdapterWithGivenKeyExist(bool $expected, string $key)
    {
        // Given
        $registryUnderTest = $this->createRegistry();

        // When
        $actual = $registryUnderTest->has($key);

        // Then
        $this->assertSame($expected, $actual);
    }

    #######
    # End #
    #######

    ##########################
    # AdapterRegistry::get() #
    ##########################

    public function getDataProvider(): array
    {
        return [
            ['adapter_1'],
            ['adapter_2'],
            ['adapter_3'],
            ['adapter_X'],
        ];
    }

    /**
     * @dataProvider getDataProvider
     *
     * @param string $key
     *
     * @throws AdapterNotExistException
     */
    public function testShouldGetAdapterByKeyOrThrowAdapterNotExistException(string $key)
    {
        // Expect
        if (false === isset($this->adapters[$key])) {
            $this->expectException(AdapterNotExistException::class);
            $this->expectExceptionMessage('Image service adapter "'.$key.'" does not exist. Available adapters: adapter_1, adapter_2, adapter_3.');
        }

        // Given
        $registryUnderTest = $this->createRegistry();

        // When
        $actual = $registryUnderTest->get($key);

        // Then
        if (true === isset($this->adapters[$key])) {
            $this->assertSame($this->adapters[$key], $actual);
        }
    }

    #######
    # End #
    #######

    ##########################
    # AdapterRegistry::get() #
    ##########################

    /**
     * @throws AdapterNotExistException
     */
    public function testShouldGetDefaultAdapter()
    {
        // Given
        $expected = $this->adapters[self::DEFAULT_ADAPTER];

        // When
        $actual = $this->createRegistry()->default();

        // Then
        $this->assertSame($expected, $actual);
    }

    #######
    # End #
    #######

    /**
     * @return AdapterRegistry
     */
    private function createRegistry(): AdapterRegistry
    {
        return new AdapterRegistry($this->adapters, self::DEFAULT_ADAPTER);
    }

}
