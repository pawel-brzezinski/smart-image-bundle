<?php

declare(strict_types=1);

namespace PB\Bundle\SmartImageBundle\Tests\ObjectMother\Image\Resize;

use Assert\AssertionFailedException;
use PB\Bundle\SmartImageBundle\Image\Resize\Width;

/**
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
final class WidthMother
{
    /**
     * @param int $width
     *
     * @return Width
     *
     * @throws AssertionFailedException
     */
    public static function create(int $width): Width
    {
        return Width::fromInt($width);
    }

    /**
     * @return Width
     *
     * @throws AssertionFailedException
     */
    public static function random(): Width
    {
        return self::create(rand(1, 999));
    }
}
