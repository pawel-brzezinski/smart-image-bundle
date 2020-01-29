<?php

declare(strict_types=1);

namespace PB\Bundle\SmartImageBundle\Tests\ObjectMother\Image\Resize;

use Assert\AssertionFailedException;
use PB\Bundle\SmartImageBundle\Image\Resize\Height;

/**
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
final class HeightMother
{
    /**
     * @param int $height
     *
     * @return Height
     *
     * @throws AssertionFailedException
     */
    public static function create(int $height): Height
    {
        return Height::fromInt($height);
    }

    /**
     * @return Height
     *
     * @throws AssertionFailedException
     */
    public static function random(): Height
    {
        return self::create(rand(1, 999));
    }
}
