<?php

declare(strict_types=1);

namespace PB\Bundle\SmartImageBundle\Adapter;

/**
 * Adapter for CloudImage.io service.
 *
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
final class CloudImageAdapter implements AdapterInterface
{
    /**
     * @var string
     */
    private $apiUrl;

    /**
     * @var string
     */
    private $apiVersion;

    /**
     * CloudImageAdapter constructor.
     *
     * @param string $apiUrl
     * @param string $apiVersion
     */
    public function __construct(string $apiUrl, string $apiVersion)
    {
        $this->apiUrl = $apiUrl;
        $this->apiVersion = $apiVersion;
    }

    /**
     * {@inheritDoc}
     */
    public function getUrl(string $source): string
    {
        return $this->buildServiceUrl().'/'.trim($source, '/');
    }

    /**
     * {@inheritDoc}
     */
    public function getTransformationString(array $transformation): string
    {
        $queryParts = [];

        foreach ($transformation as $key => $value) {
            $queryParts[] = $key.'='.$value;
        }

        return implode('&', $queryParts);
    }

    /**
     * {@inheritDoc}
     */
    public function getUrlWithTransformation(string $source, array $transformation): string
    {
        $url = $this->getUrl($source);

        if ($transformationString = $this->getTransformationString($transformation)) {
            $url .= '?'.$transformationString;
        }

        return $url;
    }

    /**
     * Build CloudImage service main url.
     *
     * @return string
     */
    private function buildServiceUrl(): string
    {
        return trim($this->apiUrl, '/').'/'.$this->apiVersion;
    }
}
