# GremoBuzzBundle [![Build Status](https://secure.travis-ci.org/gremo/GremoBuzzBundle.png)](http://travis-ci.org/gremo/GremoBuzzBundle)

Symfony 2 Bundle for using the lightweight Buzz HTTP client.

- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Planned features](#planned-features)

## Installation

Add the following to your `deps` file (for Symfony 2.0.*):

```
[buzz]
    git=https://github.com/kriswallsmith/Buzz.git

[GremoBuzzBundle]
    git=https://github.com/gremo/GremoBuzzBundle.git
    target=bundles/Gremo/BuzzBundle
```

Then register the namespaces with the autoloader (`app/autoload.php`):

```php
$loader->registerNamespaces(array(
    // ...
    'Buzz'  => __DIR__.'/../vendor/buzz/lib',
    'Gremo' => __DIR__.'/../vendor/bundles',
    // ...
));
```

Or, if you are using Composer and Symfony 2.1.*, add to `composer.json` file:

```javascript
{
    "require": {
        "gremo/buzz-bundle": "*"
    }
}
```

Finally register the bundle with your kernel in `app/appKernel.php`:

```php
public function registerBundles()
{
    $bundles = array(
        // ...
        new Gremo\BuzzBundle\GremoBuzzBundle(),
        // ...
    );

    // ...
}
```

## Configuration

All options are optional. Reference, along with default values:

```
gremo_buzz:
    client: native # Allowed values: "curl", "multi_curl" or "native"
    options:
        ignore_errors: true
        max_redirects: 5
        timeout: 5
        verify_peer: true
```

## Usage

Get `gremo_buzz` service from the service container and start using the browser:

```php
/** @var $browser \Buzz\Browser */
$browser = $this->get('gremo_buzz');
```

Refer to [Kris Wallsmith Buzz library](https://github.com/kriswallsmith/Buzz) for sending HTTP requests.

## Planned features
- Add a tag for easily define services as listeners
- Use the built-in listeners through the configuration
