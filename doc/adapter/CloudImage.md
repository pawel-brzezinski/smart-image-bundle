# SmartImageBundle - CloudImage adapter
Adapter for [CloudImage](CloudImage.io) service. Check the offical [documentation](https://docs.cloudimage.io/go/cloudimage-documentation-v7) to see what possibilities gives you this service.

## Configuration
```
pb_smart_image:  
    default_adapter: my_cloudimage
    adapters:
        my_cloudimage:
            type: cloudimage
            
            # CloudImage token (required)
            token: my-token
            # CloudImage API version (optional, default "v7")
            version: v7
            # Alias defined in CloudImage to yur original storage
            alias: _orgstorage_
```

## Transformations (resizing, filters, compressions etc)
CloudImage gives you a lot of useful transformations which are handled by themselves. You can use a lot of filters, resizing options, watermarking, compressions and more.

To check possible transformations, please take a look to the official [documentation](https://docs.cloudimage.io/go/cloudimage-documentation-v7).

## Additional TWIG partials
###  Responsive images
CloudImage gives you possibility to produce responsive images. It means that they serve image with dimenstions automatically. You don't have to define the image dimensions in code.
The dimensions will match to the image container element and user screen size (for ex. for mobile screen CloudImage will return smaller image than for desktop screen).

To use the Responsive feature, you have to implement and initialize JS script. Please take a look to the [documentation](https://docs.cloudimage.io/go/cloudimage-documentation-v7/en/responsive-images) to check how to do this.

Basic usage:
```
{% include '@PBSmartImage/cloudimage/responsive_img.html.twig' with <options> %}
```
Where `<options>` is an object which accept such attributes:
 - `attrs` - Tag attributes (allowed all attributes for standard `img` partial plus: `ci-align`, `ci-fill`, `ci-not-lazy`, `ci-params`, `ci-sizes`, `ci-src`, `ci-ratio` and any other defined by `extra_allowed_attrs` option.
 - `extra_allowed_attrs` - An array of custom tag attributes which can be added to tag.

Example
```
{% set source = '/path/to/image.jpg' %}

{# Returns: https://my-token.cloudimg.io/v7/_orgstorage_/path/to/image.jpg?q=5 #}
{% set img_low_q = si_image_url_transformation(source, {q: 5}) %}

{# Returns: https://my-token.cloudimg.io/v7/_orgstorage_/path/to/image.jpg #}
{% set img = si_image_url(source) %}

{# The attribute "src" is not required but give you possibility to load the low quality image on page load #}
{% include '@PBSmartImage/cloudimage/responsive_img.html.twig' with { 
    attrs: {
        alt: 'Example image',
        class: 'responsive img',
        src: img_low_q,        
        'ci-ratio': 100,
        'ci-src': img,
	'data-foo': "Foo",
	'data-bar': "Bar"
    },
    extra_allowed_attrs: ['data-foo', 'data-bar']
}} only %}
```
will render `<img>` tag:
```
<img alt="Example image"
     class="responsive img"
     src="https://my-token.cloudimg.io/v7/_orgstorage_/path/to/image.jpg?q=5"
     ci-ratio="100"
     ci-src="https://my-token.cloudimg.io/v7/_orgstorage_/path/to/image.jpg"
     data-foo="Foo"
     data-bar="Bar"
>
```


## Examples
### Adapter usage
```
<?php  
  
declare(strict_types=1);  
  
namespace App\Controller\Develop;  
  
use PB\Bundle\SmartImageBundle\Adapter\AdapterRegistryInterface;  
use PB\Bundle\SmartImageBundle\Adapter\Exception\AdapterNotExistException;  
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;  
use Symfony\Component\HttpFoundation\Response;  
  
class ExampleController extends AbstractController  
{  
    public function __invoke(AdapterRegistryInterface $registry): Response  
    {
        $adapter = $registry->get('my_cloudimage'); 
        
        $source = '/path/to/image.jpg'; 
        $transformation = [
            'width' => 800,
            'height' => 600,
            'grey' => 1,
            'q' => 8
        ];  
  
	// Result: https://my-token.cloudimg.io/v7/_orgstorage_/path/to/image.jpg
        $url = $adapter->getUrl($source);
        
        // Result: 'width=800&height=600&grey=1&q=8' (empty string)
        $transformationString = $adapter->getTransformationString($transformation);

	// Result: https://my-token.cloudimg.io/v7/_orgstorage_/path/to/image.jpg?width=800&height=600&grey=1&q=8
        $urlTransformation = $adapter->getUrlWithTransformation($source, $transformation);  
  
        return $this->render('path/to/twig/template.html.twig', [  
            'url' => $url,  
            'transformation_string' => $transformationString,  
            'url_transformation' => $urlTransformation,  
        ]);  
    }  
}
```

### TWIG functions usage
```
{% set source = '/path/to/image.jpg' %}
{% set transformation = {
    width: 800,
    height: 600,
    grey: 1,
    q: 8
} %}

{% set url = si_image_url(source) %}
{% set transformation_string = si_image_transformation(transformation) %}
{% set url_transformation = si_image_url_transformation(url, transformation) %}

<p>URL: {{ url }}</p>
<p>Transformation string: {{ transformation_string }}</p>
<p>URL with transformation: {{ url_transformation }}</p>
```
the result will be:
```
URL: https://my-token.cloudimg.io/v7/_orgstorage_/path/to/image.jpg
Transformation string: width=800&height=600&grey=1&q=8
URL with transformation: https://my-token.cloudimg.io/v7/_orgstorage_/path/to/image.jpg?width=800&height=600&grey=1&q=8
```
