<?php

declare(strict_types=1);

namespace PB\Bundle\SmartImageBundle\Tests\Twig;

use PB\Bundle\SmartImageBundle\Twig\{ImageExtension, ImageRuntime};
use PHPUnit\Framework\TestCase;
use Twig\TwigFunction;

/**
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
class ImageExtensionTest extends TestCase
{
    ##################################
    # ImageExtension::getFunctions() #
    ##################################

    /**
     *
     */
    public function testShouldCallGetFunctionsAndReturnAllDefinedFunctionsInExtension()
    {
        // Given
        $expected = [
            ['name' => 'si_image_url', 'callable' => [ImageRuntime::class, 'url']],
            ['name' => 'si_image_transformation', 'callable' => [ImageRuntime::class, 'transformationString']],
            ['name' => 'si_image_url_transformation', 'callable' => [ImageRuntime::class, 'urlWithTransformation']],
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
     * @return ImageExtension
     */
    private function createExtension(): ImageExtension
    {
        return new ImageExtension();
    }

}
