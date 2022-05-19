Template Factory
================

[![Build Status](https://github.com/nepada/template-factory/workflows/CI/badge.svg)](https://github.com/nepada/template-factory/actions?query=workflow%3ACI+branch%3Amaster)
[![Coverage Status](https://coveralls.io/repos/github/nepada/template-factory/badge.svg?branch=master)](https://coveralls.io/github/nepada/template-factory?branch=master)
[![Downloads this Month](https://img.shields.io/packagist/dm/nepada/template-factory.svg)](https://packagist.org/packages/nepada/template-factory)
[![Latest stable](https://img.shields.io/packagist/v/nepada/template-factory.svg)](https://packagist.org/packages/nepada/template-factory)


Package abandoned
-----------------

**This package is considered obsolete and abandoned.** Nette and Latte has evolved a lot since the inception of this package. The functionality of this package can now be replaced by a combination of Latte 3 extensions and `TemplateFactory::$onCreate` callback.


Installation
------------

Via Composer:

```sh
$ composer require nepada/template-factory
```

Register the extension in `config.neon`:

```yaml
extensions:
    templateFactory: Nepada\Bridges\TemplateFactoryDI\TemplateFactoryExtension
```


Usage
-----

### Translator autowiring

Who would want to call `setTranslator()` manually on every template? With this template factory all you need is to define `ITranslator` service in your configuration and it gets automatically injected into created templates.

### Custom Latte filters

Do you need custom Latte filters in templates? Their definition is pretty straightforward:

```yaml
templateFactory:
    filters:
        doStuff: [@someService, doStuff]
```

### Template parameters

This is the ultimate answer to the question "How do I get parameter / service from DI container into template?"

```yaml
templateFactory:
    parameters:
        foo: bar
        service: @anotherService
        containerParam: %param%
```

### Template providers

Similarly to parameters, you can also set latte providers:

```yaml
templateFactory:
    providers:
        foo: bar
        service: @anotherService
        containerParam: %param%
```

### Custom template functions

Similarly to filters, you can also define callbacks for your custom template functions:

```yaml
templateFactory:
    functions:
        doStuff: [@someService, doStuff]
```

### Configuration from another `CompilerExtension`

Some extensions may need to install a Latte filter, or inject a parameter / service into template. This can be done in `beforeCompile()` phase by customizing setup of `TemplateConfigurator`.

```php
$templateConfigurator = $containerBuilder->getByType(Nepada\TemplateFactory\TemplateConfigurator::class);
$containerBuilder->getDefinition($templateConfigurator)
    ->addSetup('addFilter', ['filterName', $callback])
    ->addSetup('addFunction', ['functionName', $callback])
    ->addSetup('addProvider', ['provider', $value])
    ->addSetup('addParameter', ['parameter', $value])
    ->addSetup('addParameter', ['parameter', '@someService']);
```
