<?php

declare(strict_types=1);

namespace PB\Bundle\SmartImageBundle\DependencyInjection\Exception;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * Default adapter not exist exception.
 *
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
final class DefaultAdapterNotExistException extends InvalidConfigurationException
{
    /**
     * DefaultAdapterNotExistException constructor.
     *
     * @param string $key
     */
    public function __construct(string $key)
    {
        $message = sprintf(
            'The adapter "%s" marked as default adapter is not defined.',
            $key
        );

        parent::__construct($message);
    }
}
