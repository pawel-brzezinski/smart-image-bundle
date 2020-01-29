<?php

declare(strict_types=1);

namespace PB\Bundle\SmartImageBundle\Tests\Adapter\Exception;

use PB\Bundle\SmartImageBundle\Adapter\Exception\AdapterNotExistException;
use PHPUnit\Framework\TestCase;

/**
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
class AdapterNotExistExceptionTest extends TestCase
{
    #########################################
    # AdapterNotExistException::construct() #
    #########################################

    public function exceptionDataProvider(): array
    {
        // Dataset 1
        $adapterName1 = 'foobar';
        $availableAdapters1 = ['lorem', 'ipsum'];
        $expectedMsg1 = 'Image service adapter "foobar" does not exist. Available adapters: lorem, ipsum.';

        // Dataset 2
        $adapterName2 = 'lorem';
        $availableAdapters2 = ['foo', 'bar'];
        $expectedMsg2 = 'Image service adapter "lorem" does not exist. Available adapters: foo, bar.';


        return [
            [$expectedMsg1, $adapterName1, $availableAdapters1],
            [$expectedMsg2, $adapterName2, $availableAdapters2],
        ];
    }

    /**
     * @dataProvider exceptionDataProvider
     *
     * @param string $expectedMsg
     * @param string $adapterName
     * @param array $availableAdapters
     *
     * @throws AdapterNotExistException
     */
    public function testShouldThrowTagGeneratorNotExistException(string $expectedMsg, string $adapterName, array $availableAdapters)
    {
        // Expect
        $this->expectException(AdapterNotExistException::class);
        $this->expectExceptionMessage($expectedMsg);

        // When
        throw new AdapterNotExistException($adapterName, $availableAdapters);
    }

    #######
    # End #
    #######
}

