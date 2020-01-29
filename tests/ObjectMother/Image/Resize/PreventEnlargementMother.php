<?php

declare(strict_types=1);

namespace PB\Bundle\SmartImageBundle\Tests\ObjectMother\Image\Resize;

use PB\Bundle\SmartImageBundle\Image\Resize\PreventEnlargement;

/**
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
final class PreventEnlargementMother
{
    /**
     * @param bool $enabled
     *
     * @return PreventEnlargement
     */
    public static function create(bool $enabled): PreventEnlargement
    {
        return PreventEnlargement::fromBool($enabled);
    }

    /**
     * @return PreventEnlargement
     */
    public static function random(): PreventEnlargement
    {
        $flags = [true, false];
        $rand = rand(0, 1);

        return self::create($flags[$rand]);
    }
}
