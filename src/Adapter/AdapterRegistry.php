<?php

declare(strict_types=1);

namespace PB\Bundle\SmartImageBundle\Adapter;

use PB\Bundle\SmartImageBundle\Adapter\Exception\AdapterNotExistException;

/**
 * Adapter registry.
 *
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
final class AdapterRegistry implements AdapterRegistryInterface
{
    /**
     * @var CloudImageAdapter
     */
    private $defaultAdapter;

    /**
     * @var \ArrayObject
     */
    private $adapters;

    /**
     * AdapterRegistry constructor.
     *
     * @param array $adapters
     * @param string $defaultAdapter
     */
    public function __construct(array $adapters, string $defaultAdapter)
    {
        $this->adapters = new \ArrayObject($adapters);
        $this->defaultAdapter = $defaultAdapter;
    }

    /**
     * {@inheritDoc}
     */
    public function all(): \ArrayObject
    {
        return $this->adapters;
    }

    /**
     * {@inheritDoc}
     */
    public function keys(): array
    {
        return array_keys($this->adapters->getArrayCopy());
    }

    /**
     * {@inheritDoc}
     */
    public function has(string $key): bool
    {
        return $this->adapters->offsetExists($key);
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $key): AdapterInterface
    {
        if (false === $this->has($key)) {
            throw new AdapterNotExistException($key, $this->keys());
        }

        return $this->adapters->offsetGet($key);
    }

    /**
     * {@inheritDoc}
     */
    public function default(): AdapterInterface
    {
        return $this->get($this->defaultAdapter);
    }
}
