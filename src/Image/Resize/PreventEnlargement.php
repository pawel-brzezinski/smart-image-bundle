<?php

declare(strict_types=1);

namespace PB\Bundle\SmartImageBundle\Image\Resize;

/**
 * Image resize prevent enlargement object.
 *
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
final class PreventEnlargement
{
    /**
     * @var bool
     */
    private $value;

    /**
     * PreventEnlargement constructor.
     *
     * @param bool $value
     */
    private function __construct(bool $value)
    {
        $this->value = $value;
    }

    /**
     * Creates object from boolean flag.
     *
     * @param bool $value
     *
     * @return PreventEnlargement
     */
    public static function fromBool(bool $value): PreventEnlargement
    {
        return new self($value);
    }

    /**
     * Returns flag which determine if prevent enlargement is enabled.
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->value;
    }
}
