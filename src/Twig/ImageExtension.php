<?php

declare(strict_types=1);

namespace PB\Bundle\SmartImageBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Image Twig extension.
 *
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
final class ImageExtension extends AbstractExtension
{
    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('si_image_url', [ImageRuntime::class, 'url']),
            new TwigFunction('si_image_transformation', [ImageRuntime::class, 'transformationString']),
            new TwigFunction('si_image_url_transformation', [ImageRuntime::class, 'urlWithTransformation']),
        ];
    }
}
