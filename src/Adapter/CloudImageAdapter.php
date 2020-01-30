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
    const URL_PATTERN = 'https://%s.cloudimg.io';

    /**
     * @var string
     */
    private $token;

    /**
     * @var string
     */
    private $version;

    /**
     * @var string|null
     */
    private $alias;

    /**
     * CloudImageAdapter constructor.
     *
     * @param string $token
     * @param string $version
     * @param string|null $alias
     */
    public function __construct(string $token, string $version, string $alias = null)
    {
        $this->token = $token;
        $this->version = $version;
        $this->alias = $alias;
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
        $url = sprintf(self::URL_PATTERN, $this->token).'/'.$this->version;

        if (null !== $this->alias) {
            $url .= '/'.trim($this->alias, '/');
        }

        return $url;
    }
}
