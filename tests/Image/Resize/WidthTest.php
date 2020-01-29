<?php

declare(strict_types=1);

namespace PB\Bundle\SmartImageBundle\Tests\Image\Resize;

use Assert\AssertionFailedException;
use PB\Bundle\SmartImageBundle\Image\Resize\Width;
use PB\Bundle\SmartImageBundle\Tests\Library\Reflection;
use PHPUnit\Framework\TestCase;

/**
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
class WidthTest extends TestCase
{
    ####################
    # Width::fromInt() #
    ####################

    /**
     * @return array
     */
    public function fromIntDataProvider(): array
    {
        return [
            [1, 1],
            [2, 2],
            [100, 100],
            [null, 0],
            [null, -1],
            [null, -100],
        ];
    }

    /**
     * @dataProvider fromIntDataProvider
     *
     * @param int|null $expected
     * @param int $value
     *
     * @throws AssertionFailedException
     * @throws \ReflectionException
     */
    public function testShouldCreateWidthObjectFromIntegerValue(?int $expected, int $value)
    {
        // Expect
        if (null === $expected) {
            $this->expectException(AssertionFailedException::class);
            $this->expectExceptionMessage('Image width should have at least 1 pixel.');
        }

        // When
        $actual = Width::fromInt($value);
        $actualValue = Reflection::getPropertyValue($actual, 'value');

        // Then
        $this->assertInstanceOf(Width::class, $actual);
        $this->assertSame($value, $actualValue);
    }

    #######
    # End #
    #######

    ###################
    # Width::toInt() #
    ###################

    /**
     * @throws AssertionFailedException
     */
    public function testShouldToIntAndReturnValueObjectDumpedToInteger()
    {
        // Given
        $value = 15;

        // When
        $actual = Width::fromInt($value)->toInt();

        // Then
        $this->assertSame($value, $actual);
    }

    #######
    # End #
    #######
}
