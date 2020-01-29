<?php

declare(strict_types=1);

namespace PB\Bundle\SmartImageBundle\Image\Resize;

use Assert\{Assertion, AssertionFailedException};

/**
 * Image resize height object.
 *
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
final class Height
{
    /**
     * @var int
     */
    private $value;

    /**
     * Height constructor.
     *
     * @param int $value
     *
     * @throws AssertionFailedException
     */
    private function __construct(int $value)
    {
        Assertion::min($value, 1, 'Image height should have at least 1 pixel.');

        $this->value = $value;
    }

    /**
     * Creates object from integer.
     *
     * @param int $value
     *
     * @return Height
     *
     * @throws AssertionFailedException
     */
    public static function fromInt(int $value): Height
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
