<?php

declare(strict_types=1);

namespace PB\Bundle\SmartImageBundle\Tests\Twig;

use PB\Bundle\SmartImageBundle\Twig\{HTMLExtension, HTMLRuntime};
use PHPUnit\Framework\TestCase;
use Twig\TwigFunction;

/**
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
class HTMLExtensionTest extends TestCase
{
    #################################
    # HTMLExtension::getFunctions() #
    #################################

    /**
     *
     */
    public function testShouldCallGetFunctionsAndReturnAllDefinedFunctionsInExtension()
    {
        // Given
        $expected = [
            ['name' => 'si_attrs_string', 'callable' => [HTMLRuntime::class, 'attributesString']],
        ];

        // When
        $actual = $this->createExtension()->getFunctions();

        // Then
        $this->assertCount(count($expected), $actual);

        foreach ($actual as $index => $function) {
            $this->assertInstanceOf(TwigFunction::class, $function);
            $this->assertSame($expected[$index]['name'], $function->getName());
            $this->assertSame($expected[$index]['callable'], $function->getCallable());
        }
    }

    #######
    # End #
    #######

    /**
     * @return HTMLExtension
     */
    private function createExtension(): HTMLExtension
    {
        return new HTMLExtension();
    }

}
