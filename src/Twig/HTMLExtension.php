<?php

declare(strict_types=1);

namespace PB\Bundle\SmartImageBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * HTML Twig extension.
 *
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
final class HTMLExtension extends AbstractExtension
{
    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('si_attrs_string', [HTMLRuntime::class, 'attributesString']),
        ];
    }
}
