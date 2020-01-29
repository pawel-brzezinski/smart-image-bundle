<?php

declare(strict_types=1);

namespace PB\Bundle\SmartImageBundle\Tests\Adapter;

use PB\Bundle\SmartImageBundle\Adapter\AdapterInterface;

/**
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
final class FakeAdapter implements AdapterInterface
{
    /**
     * @return FakeAdapter
     */
    public static function create(): FakeAdapter
    {
        return new self();
    }


    /**
     * {@inheritDoc}
     *
     * @throws \Exception
     */
    public function getUrl(string $source): string
    {
        throw new \Exception('Not supported! Fake adapter only for tests purposes.');
    }

    /**
     * {@inheritDoc}
     *
     * @throws \Exception
     */
    public function getTransformationString(array $transformation): string
    {
        throw new \Exception('Not supported! Fake adapter only for tests purposes.');
    }

    /**
     * {@inheritDoc}
     *
     * @throws \Exception
     */
    public function getUrlWithTransformation(string $source, array $transformation): string
    {
        throw new \Exception('Not supported! Fake adapter only for tests purposes.');
    }
}
