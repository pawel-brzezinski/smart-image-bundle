# SmartImageBundle  
  
This Symfony bundle provides easily integration with external image storages and proxy services serving responsive, resized, filtered and compressed images.

## Note
Current version is not stable. Errors may occur. Some basic functionalities may be missing as well, so if you would like to report error or suggest some improvements, please send an issue on GitHub.

## Requirements

 - PHP 7.2 or higher
 - Symfony Framework 3.4.x, 4.x, 5.x

For more information check composer.json file.

## Installation
Use [Composer](https://getcomposer.org/) to install the bundle. Run the following command: 
> composer require pawel-brzezinski/smart-image-bundle

### Enable the bundle
Enable the bundle in your Symfony application. For **Symfony 4.x** and **5.x** you have to edit `config/bundles.php` file. Example:

```
<?php  
  
return [
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],  
    Symfony\WebpackEncoreBundle\WebpackEncoreBundle::class => ['all' => true],  
    Symfony\Bundle\TwigBundle\TwigBundle::class => ['all' => true],
    Twig\Extra\TwigExtraBundle\TwigExtraBundle::class => ['all' => true],
    PB\Bundle\SmartImageBundle\PBSmartImageBundle::class => ['all' => true],
];
```
For **Symfony 3.4.x** you have to edit `app/AppKernel.php` file. Example:
```
<?php

use SymfonyComponentDependencyInjectionContainerBuilder;
use SymfonyComponentHttpKernelKernel;
use SymfonyComponentConfigLoaderLoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            new SymfonyBundleFrameworkBundleFrameworkBundle(),
            new SymfonyBundleSecurityBundleSecurityBundle(),
            new SymfonyBundleTwigBundleTwigBundle(),
            new SymfonyBundleMonologBundleMonologBundle(),
            new SymfonyBundleSwiftmailerBundleSwiftmailerBundle(),
            new DoctrineBundleDoctrineBundleDoctrineBundle(),
            new SensioBundleFrameworkExtraBundleSensioFrameworkExtraBundle(),
            new AppBundleAppBundle(),

            // Add SmartImageBundle
            new PBBundleSmartImageBundlePBSmartImageBundle(),
        ];

        ...

        return $bundles;
    }

    ...
}
```

## Configuration

### Common configuration
You'll need to configure *at least one* image adapter. The number of adapters is unlimited. For Storage adapter, such configuration will look like this:
```
# app/config/config.yml (for Symfony 3.4.x)
# config/packages/smartimage.yaml (for Symfony 4.x and 5.x)
pb_smart_image:  
    adapters:
        # the key "my_storage" is a custom string
        # this key is used for create adapter service 
        # and can be used to mark adapter as default 
        # (see "default_adapter" config key)
        my_storage:
            # REQUIRED
            # one of the supported adapters
            # this option is common for each adapters types 
            # and is required
            type: storage

            # these options are specific to a particular adapter
            # for "storage" adapter type it will be:
            url: https://my-storage.example.com
            path_prefix: /Images
    
    # REQUIRED
    # determine which adapter should be used as default
    default_adapter: my_storage
```

## Available adapters

Here is the list of available adapters. Check the description of each adapter to learn more.

 1. **Storage** - [[config]](doc/adapter/Storage.md) - simple adapter for basic storage services like AWS S3, DigitalOcean Storage etc.
 2. **CloudImage** - [[homepage]](https://www.cloudimage.io) [[config]](doc/adapter/CloudImage.md)

## Services
Bundle creates Symfony services based on the configuration.

### Adapter service
Each configured adapter is available as a Symfony service.  Adapter class implements interface:
```
PB\Bundle\SmartImageBundle\Adapter\AdapterInterface
```
The key of the service is created according to this pattern:
```
pb_smart_image.adapter.<adapter_key>
```
In our configuration example the key of the adapter is `my_storage`, so the service key will be:
```
pb_smart_image.adapter.my_storage
```
*Attention!* Adapter services are *NOT PUBLIC*, so you cannot get these services directly from the container.

#### AdapterInterface methods
 - `getUrl(string $source)` - Gets the pure url to the image in your storage service.
 - `getTransformationString(array $transformation)` - Gets the transformation string generated from the array of transformations specific to the adapter (if adapter support transformations).
 - `getUrlWithTransformation(string $source, array $transformation)` - Gets the url to the image in your storage service with transformation paramaters (if adapter support transformations).

### Adapter registry service
Instead of using single adapter service, you can use adapter registry service which contain all configured adapters. The registry service is available under key:
```
pb_smart_image.adapter_registry
```
and:
```
PB\Bundle\SmartImageBundle\Adapter\AdapterRegistryInterface
```
*Attention!* Adapter registry service is *NOT PUBLIC*, so you cannot get this service directly from the container.

#### AdapterRegistryInterface methods
 - `all()` - Returns collection of all adapters.
 - `keys()` - Returns array of all available adapter keys.
 - `has(string $key)` - Returns flag which determine if registry has defined adapter with given key.
 - `get(string $key)` - Returns adapter by key. If adapter with given key does not exist then the `PB\Bundle\SmartImageBundle\Adapter\Exception\AdapterNotExistException` will be thrown.
 - `default()` - Returns default adapter.
 
### Example of usage
Suppose, that we have configuration:

```
pb_smart_image:  
    adapters:
        cloudimage:
            type: cloudimage
	    token: mytoken
    default_adapter: cloudimage
```

Let's try to pass the image url, transformation string, and url with transformation from controller to Twig template.

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
        /////////////////////////////////////////////////////  
        // Scenario: 
        // 
        // Get "my_storage" adapter from registry and if not exist  then get default adapter.
        /////////////////////////////////////////////////////  

        // 
        // 1st solution
        //  
 
        // Check if "my_storage" adapter exist
        if (true === $registry->has('my_storage')) {  
            // If exist then get the adapter by "my_storage" key  
            $adapter = $registry->get('my_storage');
        } else {  
            // If not exist then get the default adapter  
            $adapter = $registry->default();  
        }  
  
        //  
        // 2nd solution
        //  
 
        // Use AdapterInterface::get() method directly and catch the AdapterNotExistException 
        // which is throw when adapter for given key does not exist.
        
        try {  
            $adapter = $registry->get('my_storage');  
        } catch (AdapterNotExistException $exception) {  
            // Get default adapter  
            $adapter = $registry->default();  
        }  
  
  
        /////////////////////////////////////////////////////  
        // Scenario: 
        // 
        // Get the url, transformation string and url with transformation for 
        // image source "/path/to/image.jpg" 
        // and transformation array("width" => 800, "height" => 600, "grey" => 1, "q" => 90) 
        // and pass these variables to the Twig template
        /////////////////////////////////////////////////////  
        
        $source = '/path/to/image.jpg';  
        $transformation = [  
            'width' => 800,  
            'height' => 600,  
            'grey' => 1,  
            'q' => 90,  
        ];  
  
        $url = $adapter->getUrl($source);  
        $transformationString = $adapter->getTransformationString($transformation);  
        $urlTransformation = $adapter->getUrlWithTransformation($source, $transformation);  
  
        return $this->render('path/to/twig/template.html.twig', [  
            'url' => $url,  
            'transformation_string' => $transformationString,  
            'url_transformation' => $urlTransformation,  
        ]);  
    }  
}
```

## TWIG functions
SmartImageBundle provides some useful TWIG functions (more detailed examples you can find in configuration document for each adapter).

### si_image_url

`{{ si_image_url(string source, string adapter = null) }}`

This function returns the pure url to the image in your storage service for given `adapter` (if `adapter` attribute is not defined then use *default* adapter).

### si_image_transformation

`{{ si_image_transformation(array transformation, string adapter = null) }}`

Gets the transformation string generated from the array of transformations specific to the `adapter` (if `adapter` attribute is not defined then use *default* adapter).

### si_image_url_transformation

`{{ si_image_url_transformation(string source, array transformation, string adapter = null) }}`

Gets the url to the image in your storage service with transformation paramaters (if adapter support transformations) specific to the `adapter` (if `adapter` attribute is not defined then use *default* adapter).

## TWIG partials
The bundle provides simple TWIG partials which you can use to display your images. Additionally, each adapter can provide their own TWIG partials (check the adapter configuration document).

### Tag `<img>`
Basic usage:
```
{% include '@PBSmartImage/tag/img.html.twig' with <options> %}
```
Where `<options>` is an object which accept such attributes:
 - `attrs` - Tag attributes (allowed: `alt`, `class`, `sizes`, `src`, `srcset` and any other defined by `extra_allowed_attrs` option.
 - `extra_allowed_attrs` - An array of custom tag attributes which can be added to tag.

Example:
```
{# You can use bundle TWIG functions to generate paths to images #}
{% set img = '/path/to/image.jpg' %}
{% set img_480w = '/path/to/480w/image.jpg' %}
{% set img_800w = '/path/to/800w/image.jpg' %}

{% include '@PBSmartImage/tag/img.html.twig' with { 
    attrs: {
        alt: 'Example image',
        class: 'responsive img',
        sizes: '(max-width: 600px) 480px,800px',
        src: img,        
        srcset: img_480w ~ ',' ~ img_800w,
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
     sizes="(max-width: 600px) 480px,800px"
     src="/path/to/image.jpg"
     srcset="/path/to/480w/image.jpg,/path/to/800w/image.jpg"
     data-foo="Foo"
     data-bar="Bar"
>
```

### Tag `<source>`
Basic usage:
```
{% include '@PBSmartImage/tag/source.html.twig' with <options> %}
```
Where `<options>` is an object which accept such attributes:
 - `attrs` - Tag attributes (allowed: `media`, `sizes`, `src`, `srcset`, `type` and any other defined by `extra_allowed_attrs` option.
 - `extra_allowed_attrs` - An array of custom tag attributes which can be added to tag.

Example:
```
{# You can use bundle TWIG functions to generate paths to images #}
{% set img_360w = '/path/to/360w/image.jpg' %}
{% set img_720w = '/path/to/720w/image.jpg' %}
{% set img_1440w = '/path/to/1440w/image.jpg' %}

{% include '@PBSmartImage/tag/source.html.twig' with { 
    attrs: {
        media: '(min-width: 800px)',
        sizes: '(min-width: 800px) 1440px, 720px',       
        srcset: img_360w ~ ' 360w,' ~ img_720w ~ ' 720w,' ~ img_1440w ~ ' 1440w',
        type: 'image/jpg',
	'data-foo': "Foo",
	'data-bar': "Bar"
    },
    extra_allowed_attrs: ['data-foo', 'data-bar']
}} only %}
```
will render `<source>` tag:
```
<source media="(min-width: 800px)"
        sizes="(min-width: 800px) 1440px, 720px"
        srcset="/path/to/360w/image.jpg 360w,/path/to/720w/image.jpg 720w,/path/to/1440w/image.jpg 1440w"
        type="image/jpg"
        data-foo="Foo"
        data-bar="Bar"
>
```

### Tag `<picture>`
Basic usage:
```
{% include '@PBSmartImage/tag/picture.html.twig' with <options> %}
```
Where `<options>` is an object which accept such attributes:
 - `img` - Tag attributes for `<img>` tag (the options are the same as for img partial).
 - `sources` - An array of sources attributes. One array element is one `<source>` tag (the options are the same as for img partial).

Example:
```
{# You can use bundle TWIG functions to generate paths to images #}
{% set img = '/path/to/image.jpg' %}
{% set img_360w = '/path/to/360w/image.jpg' %}
{% set img_720w = '/path/to/720w/image.jpg' %}
{% set img_1440w = '/path/to/1440w/image.jpg' %}

{% include '@PBSmartImage/tag/picture.html.twig' with {
    img: {
        attrs: {
            src: img,
            alt: 'Example image'
        }
    },
    sources: [
        {
            attrs: {
                media: '(min-width: 800px)',
                sizes: '(min-width: 800px) 1440px, 720px',
                srcset: img_360w ~ ' 360w,' ~ img_720w ~ ' 720w,' ~ img_1440w ~ ' 1440w',
                type: 'image/jpg'
            }
        },
        {
            attrs: {
                srcset: img_720w ~ ' 2x,' ~ img_360w ~ ' 1x',
            }
        }
]} only %}
 ```
will render `<picture>` tag:
```
<picture>
    <source media="(min-width: 800px)"
            sizes="(min-width: 800px) 1440px, 720px"
            srcset="/path/to/360w/image.jpg 360w,/path/to/720w/image.jpg 720w,/path/to/1440w/image.jpg 1440w"
            type="image/jpg"
    >
    <source srcset="/path/to/720w/image.jpg 2x,/path/to/360w/image.jpg 1x">
</picture>
```
