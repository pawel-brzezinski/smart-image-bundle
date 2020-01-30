<?php

declare(strict_types=1);

namespace PB\Bundle\SmartImageBundle\Tests\DependencyInjection\Exception;

use PB\Bundle\SmartImageBundle\DependencyInjection\Exception\MissingAdapterTypeException;
use PHPUnit\Framework\TestCase;

/**
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
class MissingAdapterTypeExceptionTest extends TestCase
{
    ############################################
    # MissingAdapterTypeException::construct() #
    ############################################

    public function exceptionDataProvider(): array
    {
        // Dataset 1
        $key1 = 'foo';
        $expectedMsg1 = 'Your "pb_smart_image.adapters.foo" config entry do not contain the "type" key.';

        // Dataset 2
        $key2 = 'bar';
        $expectedMsg2 = 'Your "pb_smart_image.adapters.bar" config entry do not contain the "type" key.';


        return [
            [$expectedMsg1, $key1],
            [$expectedMsg2, $key2],
        ];
    }

    /**
     * @dataProvider exceptionDataProvider
     *
     * @param string $expectedMsg
     * @param string $key
     *
     * @throws MissingAdapterTypeException
     */
    public function testShouldThrowMissingAdapterTypeException(string $expectedMsg, string $key)
    {
        // Expect
        $this->expectException(MissingAdapterTypeException::class);
        $this->expectExceptionMessage($expectedMsg);

        // When
        throw new MissingAdapterTypeException($key);
    }

    #######
    # End #
    #######
}
