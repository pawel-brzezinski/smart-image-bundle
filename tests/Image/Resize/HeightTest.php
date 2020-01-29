<?php

declare(strict_types=1);

namespace PB\Bundle\SmartImageBundle\Tests\Image\Resize;

use Assert\AssertionFailedException;
use PB\Bundle\SmartImageBundle\Image\Resize\Height;
use PB\Bundle\SmartImageBundle\Tests\Library\Reflection;
use PHPUnit\Framework\TestCase;

/**
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
class HeightTest extends TestCase
{
    #####################
    # Height::fromInt() #
    #####################

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
            $this->expectExceptionMessage('Image height should have at least 1 pixel.');
        }

        // When
        $actual = Height::fromInt($value);
        $actualValue = Reflection::getPropertyValue($actual, 'value');

        // Then
        $this->assertInstanceOf(Height::class, $actual);
        $this->assertSame($value, $actualValue);
    }

    #######
    # End #
    #######

    ###################
    # Height::toInt() #
    ###################

    /**
     * @throws AssertionFailedException
     */
    public function testShouldToIntAndReturnValueObjectDumpedToInteger()
    {
        // Given
        $value = 50;

        // When
        $actual = Height::fromInt($value)->toInt();

        // Then
        $this->assertSame($value, $actual);
    }

    #######
    # End #
    #######
}
