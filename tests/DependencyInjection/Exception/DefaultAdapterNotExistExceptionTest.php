<?php

declare(strict_types=1);

namespace PB\Bundle\SmartImageBundle\Tests\DependencyInjection\Exception;

use PB\Bundle\SmartImageBundle\DependencyInjection\Exception\DefaultAdapterNotExistException;
use PHPUnit\Framework\TestCase;

/**
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
class DefaultAdapterNotExistExceptionTest extends TestCase
{
    ################################################
    # DefaultAdapterNotExistException::construct() #
    ################################################

    public function exceptionDataProvider(): array
    {
        // Dataset 1
        $key1 = 'foo';
        $expectedMsg1 = 'The adapter "foo" marked as default adapter is not defined.';

        // Dataset 2
        $key2 = 'bar';
        $expectedMsg2 = 'The adapter "bar" marked as default adapter is not defined.';


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
     * @throws DefaultAdapterNotExistException
     */
    public function testShouldThrowMissingAdapterTypeException(string $expectedMsg, string $key)
    {
        // Expect
        $this->expectException(DefaultAdapterNotExistException::class);
        $this->expectExceptionMessage($expectedMsg);

        // When
        throw new DefaultAdapterNotExistException($key);
    }

    #######
    # End #
    #######
}
