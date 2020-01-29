<?php

declare(strict_types=1);

namespace PB\Bundle\SmartImageBundle\Adapter;

/**
 * Interface for adapter implementation.
 *
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
interface AdapterInterface
{
    /**
     * Gets image url.
     *
     * @param string $source
     *
     * @return string
     */
    public function getUrl(string $source): string;

    /**
     * Gets transformation string.
     *
     * @param array $transformation
     *
     * @return string
     */
    public function getTransformationString(array $transformation): string;

    /**
     * Gets image url with attached transformation string.
     *
     * @param string $source
     * @param array $transformation
     *
     * @return string
     */
    public function getUrlWithTransformation(string $source, array $transformation): string;
}
