<?php

declare(strict_types=1);

namespace PB\Bundle\SmartImageBundle\Tests\DependencyInjection\Exception;

use PB\Bundle\SmartImageBundle\DependencyInjection\Exception\AdapterNotSupportedException;
use PHPUnit\Framework\TestCase;

/**
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
class AdapterNotSupportedExceptionTest extends TestCase
{
    #############################################
    # AdapterNotSupportedException::construct() #
    #############################################

    public function exceptionDataProvider(): array
    {
        // Dataset 1
        $type1 = 'foo';
        $supportedTypes1 = ['type-1', 'type-2', 'type-3'];
        $expectedMsg1 = 'Your "pb_smart_image" config "type" key "foo" is not supported. Supported types: type-1, type-2, type-3.';

        // Dataset 2
        $type2 = 'bar';
        $supportedTypes2 = ['type-4', 'type-5'];
        $expectedMsg2 = 'Your "pb_smart_image" config "type" key "bar" is not supported. Supported types: type-4, type-5.';


        return [
            [$expectedMsg1, $type1, $supportedTypes1],
            [$expectedMsg2, $type2, $supportedTypes2],
        ];
    }

    /**
     * @dataProvider exceptionDataProvider
     *
     * @param string $expectedMsg
     * @param string $type
     * @param array $supportedTypes
     *
     * @throws AdapterNotSupportedException
     */
    public function testShouldThrowAdapterNotSupportedException(string $expectedMsg, string $type, array $supportedTypes)
    {
        // Expect
        $this->expectException(AdapterNotSupportedException::class);
        $this->expectExceptionMessage($expectedMsg);

        // When
        throw new AdapterNotSupportedException($type, $supportedTypes);
    }

    #######
    # End #
    #######
}
