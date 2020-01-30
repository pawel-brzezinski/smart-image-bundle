<?php

declare(strict_types=1);

namespace PB\Bundle\SmartImageBundle\DependencyInjection\Exception;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * Missing service adapter type exception.
 *
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
final class MissingAdapterTypeException extends InvalidConfigurationException
{
    /**
     * MissingAdapterTypeException constructor.
     *
     * @param string $key
     */
    public function __construct(string $key)
    {
        $message = sprintf(
            'Your "pb_smart_image.adapters.%s" config entry do not contain the "type" key.',
            $key
        );

        parent::__construct($message);
    }
}
