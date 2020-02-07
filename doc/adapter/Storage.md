# SmartImageBundle - Storage adapter
This is the basic adapter. You can use this adapter for serving images direct from storage services (like: AWS S3, DigitalOcean Storage or your local storage).

## Configuration
```
pb_smart_image:  
    default_adapter: my_storage
    adapters:
        my_storage:
            type: storage
            
            # the base url to your storage (required)
            url: https://my-storage.example.com
            # optional value where you can define prefix for each
            # path to your image
            # (ex: for "/path/to/image.jpg" the result will be "/Images/path/to/image.jpg")
            path_prefix: /Images
```

## Transformations (resizing, filters, compressions etc)
Most storage services does not support image transformations by themselves so this adapter does not give you an opportunity to create image url with paramters to transform the image.

## Additional TWIG partials
This adapter does not provide any addiotional TWIG partials.

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
        $adapter = $registry->get('my_storage'); 
        
        $source = '/path/to/image.jpg'; 
        $transformation = [];  
  
	// Result: https://my-storage.example.com/Images/path/to/image.jpg
        $url = $adapter->getUrl($source);
        
        // Adapter does not support transformations 
        // but you can still use the adapter method
        // 
        // Result: '' (empty string)
        $transformationString = $adapter->getTransformationString($transformation);

	// Adapter does not support transformations 
        // but you can still use the adapter method
        // 
        // Result: https://my-storage.example.com/Images/path/to/image.jpg
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
{% set transformation = {} %}

{% set url = si_image_url(source) %}
{% set transformation_string = si_image_transformation(transformation) %}
{% set url_transformation = si_image_url_transformation(url, transformation) %}

<p>URL: {{ url }}</p>
<p>Transformation string: {{ transformation_string }}</p>
<p>URL with transformation: {{ url_transformation }}</p>
```
the result will be:
```
URL: https://my-storage.example.com/Images/path/to/image.jpg
Transformation string:
URL with transformation: https://my-storage.example.com/Images/path/to/image.jpg
```
