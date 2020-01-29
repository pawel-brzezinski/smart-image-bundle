<?php

declare(strict_types=1);

namespace PB\Bundle\SmartImageBundle\Adapter;

use PB\Bundle\SmartImageBundle\Adapter\Exception\AdapterNotExistException;

/**
 * Interface for adapter registry implementation.
 *
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
interface AdapterRegistryInterface
{
    /**
     * Returns collection of all adapters.
     *
     * @return \ArrayObject
     */
    public function all(): \ArrayObject;

    /**
     * Returns array of all available adapter keys.
     *
     * @return array
     */
    public function keys(): array;

    /**
     * Returns flag which determine if registry has defined adapter with given key.
     *
     * @param string $key
     *
     * @return bool
     */
    public function has(string $key): bool;

    /**
     * Returns adapter by key.
     *
     * @param string $key
     *
     * @return AdapterInterface
     *
     * @throws AdapterNotExistException
     */
    public function get(string $key): AdapterInterface;

    /**
     * Returns default adapter.
     *
     * @return AdapterInterface
     *
     * @throws AdapterNotExistException
     */
    public function default(): AdapterInterface;
}
