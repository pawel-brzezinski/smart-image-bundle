<?php

declare(strict_types=1);

namespace PB\Bundle\SmartImageBundle\Adapter;

/**
 * Adapter for standard image storage.
 *
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
final class StorageAdapter implements AdapterInterface
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var string|null
     */
    private $pathPrefix;

    /**
     * DigitalOceanStorageAdapter constructor.
     *
     * @param string $url
     * @param string|null $pathPrefix
     */
    public function __construct(string $url, string $pathPrefix = null)
    {
        $this->url = $url;
        $this->pathPrefix = $pathPrefix;
    }

    /**
     * {@inheritDoc}
     */
    public function getUrl(string $source): string
    {
        return $this->buildStorageUrl().'/'.trim($source, '/');
    }

    /**
     * {@inheritDoc}
     */
    public function getTransformationString(array $transformation): string
    {
        return '';
    }

    /**
     * {@inheritDoc}
     */
    public function getUrlWithTransformation(string $source, array $transformation): string
    {
        return $this->getUrl($source);
    }

    /**
     * Build storage url.
     *
     * @return string
     */
    private function buildStorageUrl(): string
    {
        $url = trim($this->url, '/');

        if (null !== $this->pathPrefix) {
            $url .= '/'.trim($this->pathPrefix, '/');
        }

        return $url;
    }
}
