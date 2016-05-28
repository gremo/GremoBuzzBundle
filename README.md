# GremoBuzzBundle
[![Latest stable](https://img.shields.io/packagist/v/gremo/buzz-bundle.svg?style=flat-square)](https://packagist.org/packages/gremo/buzz-bundle) [![Downloads total](https://img.shields.io/packagist/dt/gremo/buzz-bundle.svg?style=flat-square)](https://packagist.org/packages/gremo/buzz-bundle) [![GitHub issues](https://img.shields.io/github/issues/gremo/GremoBuzzBundle.svg?style=flat-square)](https://github.com/gremo/GremoBuzzBundle/issues)

Symfony Bundle for using the lightweight Buzz HTTP client.

## Installation
Add the bundle in your `composer.json` file:

```js
{
    "require": {
        "gremo/buzz-bundle": "~1.0"
    }
}
```

Then run `composer update` and register the bundle with your kernel in `app/appKernel.php`:

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Gremo\BuzzBundle\GremoBuzzBundle(),
        // ...
    );
}
```

###  Legacy Symfony (2.0.*)
Add the following to your `deps` file:

```
[buzz]
    git=https://github.com/kriswallsmith/Buzz.git

[GremoBuzzBundle]
    git=https://github.com/gremo/GremoBuzzBundle.git
    target=bundles/Gremo/BuzzBundle
```

Then run `php bin/vendors update` and register the namespaces with the autoloader (`app/autoload.php`):

```php
<?php
// app/autoload.php

$loader->registerNamespaces(array(
    // ...
    'Buzz'  => __DIR__.'/../vendor/buzz/lib',
    'Gremo' => __DIR__.'/../vendor/bundles',
    // ...
));
```

Finally register the bundle with your kernel in `app/appKernel.php`:

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Gremo\BuzzBundle\GremoBuzzBundle(),
        // ...
    );
}
```

## Configuration
Configuration is not needed. Available options and types (for the default values see [`Buzz\Client\AbstractClient`](https://github.com/kriswallsmith/Buzz/blob/master/lib/Buzz/Client/AbstractClient.php)):
```yml
# GremoBuzzBundle Configuration
gremo_buzz:
    client: "native" # allowed "curl", "multi_curl" or "native"
    options:
        ignore_errors: ~ # boolean
        max_redirects: ~ # integer
        proxy:         ~ # string
        timeout:       ~ # integer
        verify_host:   ~ # integer
        verify_peer:   ~ # boolean
```

## Usage
Get the `gremo_buzz` service from the service container:

```php
/** @var $browser \Buzz\Browser */
$browser = $this->get('gremo_buzz');
```

Refer to [Kris Wallsmith Buzz library](https://github.com/kriswallsmith/Buzz) for sending HTTP requests.

## Dependency Injection Tags
You can register a listener creating a service that implements `Buzz\Listener\ListenerInterface` and tagging it as `gremo_buzz.listener` (optionally defining a `priority` attribute). Higher priority means that the corresponding listener is executed first.

Example listener that logs outgoing requests:

```php
<?php

use Buzz\Listener\ListenerInterface;
use Buzz\Message\MessageInterface;
use Buzz\Message\RequestInterface;
use JMS\DiExtraBundle\Annotation as DI;
use Psr\Log\LoggerInterface;

/**
 * @DI\Service("buzz.listener.logger")
 * @DI\Tag("gremo_buzz.listener", attributes={"priority"=10})
 */
class BuzzLoggerListener implements ListenerInterface
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var float
     */
    private $startTime;

    /**
     * @DI\InjectParams({"logger" = @DI\Inject("logger")})
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function preSend(RequestInterface $request)
    {
        $this->startTime = microtime(true);
    }

    /**
     * {@inheritdoc}
     */
    public function postSend(RequestInterface $request, MessageInterface $response)
    {
        $this->logger->info(sprintf(
            'Sent "%s %s%s" in %dms',
            $request->getMethod(),
            $request->getHost(),
            $request->getResource(),
            round((microtime(true) - $this->startTime) * 1000)
        ));
    }
}
```

Note that this example uses the new `Psr\Log\LoggerInterface` and may not work for old versions of Symfony.
