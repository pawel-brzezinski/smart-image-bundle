<?php

declare(strict_types=1);

namespace PB\Bundle\SmartImageBundle\Tests\Image\Resize;

use PB\Bundle\SmartImageBundle\Image\Resize\PreventEnlargement;
use PB\Bundle\SmartImageBundle\Tests\Library\Reflection;
use PHPUnit\Framework\TestCase;

/**
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
class PreventEnlargementTest extends TestCase
{
    ##################################
    # PreventEnlargement::fromBool() #
    ##################################

    /**
     * @return array
     */
    public function fromBoolDataProvider(): array
    {
        return [
            [true, true],
            [false, false],
        ];
    }

    /**
     * @dataProvider fromBoolDataProvider
     *
     * @param bool $expected
     * @param bool $value
     *
     * @throws \ReflectionException
     */
    public function testShouldCreateWidthObjectFromIntegerValue(bool $expected, bool $value)
    {
        // When
        $actual = PreventEnlargement::fromBool($value);
        $actualValue = Reflection::getPropertyValue($actual, 'value');

        // Then
        $this->assertInstanceOf(PreventEnlargement::class, $actual);
        $this->assertSame($value, $actualValue);
    }

    #######
    # End #
    #######

    ################################
    # PreventEnlargement::toBool() #
    ################################

    /**
     *
     */
    public function testShouldCallIsEnabledAndReturnIsPreventEnlargementFlag()
    {
        // Given
        $flag = true;

        // When
        $actual = PreventEnlargement::fromBool($flag)->isEnabled();

        // Then
        $this->assertSame($flag, $actual);
    }

    #######
    # End #
    #######
}
