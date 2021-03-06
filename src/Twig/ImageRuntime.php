<?php

declare(strict_types=1);

namespace PB\Bundle\SmartImageBundle\Twig;

use PB\Bundle\SmartImageBundle\Adapter\AdapterInterface;
use PB\Bundle\SmartImageBundle\Adapter\AdapterRegistryInterface;
use PB\Bundle\SmartImageBundle\Adapter\Exception\AdapterNotExistException;
use Twig\Extension\RuntimeExtensionInterface;

/**
 * Runtime for HTML Twig extension.
 *
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
final class ImageRuntime implements RuntimeExtensionInterface
{
    /**
     * @var AdapterRegistryInterface
     */
    private $adapterRegistry;

    /**
     * ImageRuntime constructor.
     *
     * @param AdapterRegistryInterface $adapterRegistry
     */
    public function __construct(AdapterRegistryInterface $adapterRegistry)
    {
        $this->adapterRegistry = $adapterRegistry;
    }

    /**
     * Gets image url generated by service adapter.
     *
     * @param string $source
     * @param string|null $adapterKey
     *
     * @return string
     *
     * @throws AdapterNotExistException
     */
    public function url(string $source, string $adapterKey = null): string
    {
        return $this->getAdapter($adapterKey)->getUrl($source);
    }

    /**
     * Gets image transformation string by service adapter.
     *
     * @param array $transformation
     * @param string|null $adapterKey
     *
     * @return string
     *
     * @throws AdapterNotExistException
     */
    public function transformationString(array $transformation, string $adapterKey = null): string
    {
        return $this->getAdapter($adapterKey)->getTransformationString($transformation);
    }

    /**
     * Gets image url with transformation string generated by service adapter.
     *
     * @param string $source
     * @param array $transformation
     * @param string|null $adapterKey
     *
     * @return string
     *
     * @throws AdapterNotExistException
     */
    public function urlWithTransformation(string $source, array $transformation, string $adapterKey = null): string
    {
        return $this->getAdapter($adapterKey)->getUrlWithTransformation($source, $transformation);
    }

    /**
     * Gets adapter by key.
     *
     * @param string|null $key
     *
     * @return AdapterInterface
     *
     * @throws AdapterNotExistException
     */
    private function getAdapter(string $key = null): AdapterInterface
    {
        return null === $key ? $this->adapterRegistry->default() : $this->adapterRegistry->get($key);
    }
}
