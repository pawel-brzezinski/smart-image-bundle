<?php

declare(strict_types=1);

namespace PB\Bundle\SmartImageBundle\Tests\Twig;

use PB\Bundle\SmartImageBundle\Twig\HTMLRuntime;
use PHPUnit\Framework\TestCase;

/**
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
class HTMLRuntimeTest extends TestCase
{
    ##################################
    # HTMLRuntime::attributeString() #
    ##################################

    public function attributeStringDataProvider(): array
    {
        // Dataset 1
        $attrs1 = [];
        $args1 = [$attrs1];
        $expected1 = '';

        // Dataset 2
        $attrs2 = ['class' => 'foobar lorem', 'id' => 100];
        $args2 = [$attrs2];
        $expected2 = 'class="foobar lorem" id="100"';

        // Dataset 3
        $attrs3 = ['data-id' => '123', 'foo' => 'bar', 'class' => 'ipsum'];
        $allowed3 = ['data-id', 'class'];
        $args3 = [$attrs3, $allowed3];
        $expected3 = 'data-id="123" class="ipsum"';

        return [
            'empty attributes array and allowed tags are not defined' => [$expected1, $args1],
            'two attributes array and allowed tags are not defined' => [$expected2, $args2],
            'three attributes array and allowed tags are defined' => [$expected3, $args3],
        ];
    }

    /**
     * @dataProvider attributeStringDataProvider
     *
     * @param string $expected
     * @param array $args
     */
    public function testShouldCallAttributesStringAndReturnGeneratedStringWithHTMLTagAttributes(string $expected, array $args)
    {
        // When
        $actual = $this->createRuntime()->attributesString(...$args);

        // Then
        $this->assertSame($expected, $actual);
    }

    #######
    # End #
    #######

    /**
     * @return HTMLRuntime
     */
    private function createRuntime(): HTMLRuntime
    {
        return new HTMLRuntime();
    }
}
