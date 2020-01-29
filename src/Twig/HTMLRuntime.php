<?php

namespace PB\Bundle\SmartImageBundle\Twig;

/**
 * Runtime for HTML Twig extension.
 *
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
final class HTMLRuntime
{
    /**
     * Create html tag attributes string.
     *
     * @param array $attributes
     * @param array|null $allowed
     *
     * @return string
     */
    public function attributesString(array $attributes, array $allowed = null): string
    {
        $parts = [];

        foreach ($attributes as $attribute => $value) {
            if (null !== $allowed && false === in_array($attribute, $allowed)) {
                continue;
            }

            $parts[] = $attribute.'="'.$value.'"';
        }

        return implode(' ', $parts);
    }
}
