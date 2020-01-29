<?php

declare(strict_types=1);

namespace PB\Bundle\SmartImageBundle\Image\Resize;

use Assert\{Assertion, AssertionFailedException};

/**
 * Image resize width object.
 *
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
final class Width
{
    /**
     * @var int
     */
    private $value;

    /**
     * Width constructor.
     *
     * @param int $value
     *
     * @throws AssertionFailedException
     */
    private function __construct(int $value)
    {
        Assertion::min($value, 1, 'Image width should have at least 1 pixel.');

        $this->value = $value;
    }

    /**
     * Creates object from integer.
     *
     * @param int $value
     *
     * @return Width
     *
     * @throws AssertionFailedException
     */
    public static function fromInt(int $value): Width
    {
        return new self($value);
    }

    /**
     * Dumps object to integer value.
     *
     * @return int
     */
    public function toInt(): int
    {
        return $this->value;
    }
}
