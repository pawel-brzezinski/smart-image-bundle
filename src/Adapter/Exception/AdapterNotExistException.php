<?php

declare(strict_types=1);

namespace PB\Bundle\SmartImageBundle\Adapter\Exception;

/**
 * Service adapter not exist exception.
 *
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
final class AdapterNotExistException extends \Exception
{
    /**
     * AdapterNotExistException constructor.
     *
     * @param string $adapterName
     * @param array $availableAdapters
     */
    public function __construct(string $adapterName, array $availableAdapters)
    {
        $msg = sprintf(
            'Image service adapter "%s" does not exist. Available adapters: %s.',
            $adapterName,
            implode(', ', $availableAdapters)
        );

        parent::__construct($msg);
    }
}
