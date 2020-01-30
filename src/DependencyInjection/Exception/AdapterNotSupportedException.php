<?php

declare(strict_types=1);

namespace PB\Bundle\SmartImageBundle\DependencyInjection\Exception;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * Service adapter not supported exception.
 *
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
final class AdapterNotSupportedException extends InvalidConfigurationException
{
    /**
     * AdapterNotSupportedException constructor.
     *
     * @param string $type
     * @param array $supportedTypes
     */
    public function __construct(string $type, array $supportedTypes)
    {
        $message = sprintf(
            'Your "pb_smart_image" config "type" key "%s" is not supported. Supported types: %s.',
            $type,
            implode(', ', $supportedTypes),
        );

        parent::__construct($message);
    }
}
