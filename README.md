Template Factory
================

[![Build Status](https://travis-ci.org/nepada/template-factory.svg?branch=master)](https://travis-ci.org/nepada/template-factory)
[![Coverage Status](https://coveralls.io/repos/github/nepada/template-factory/badge.svg?branch=master)](https://coveralls.io/github/nepada/template-factory?branch=master)
[![Downloads this Month](https://img.shields.io/packagist/dm/nepada/template-factory.svg)](https://packagist.org/packages/nepada/template-factory)
[![Latest stable](https://img.shields.io/packagist/v/nepada/template-factory.svg)](https://packagist.org/packages/nepada/template-factory)


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

Latte 2.4 has improved support for global/system parameters in templates - they are called "providers". You can set them similarly to parameters:

```yaml
templateFactory:
    providers:
        foo: bar
        service: @anotherService
        containerParam: %param%
```

### Advanced template configuration

If you need to do something a bit more complex with every created template, make use of `onCreateTemplate` event of `TemplateFactory`:

```yaml
services:
    templateFactory.templateFactory:
        setup:
            - '?->onCreateTemplate[] = ?'(@self, [@customConfigurator, callback])
```

### Configuration from another `CompilerExtension`

Some extensions may need to install a Latte filter, or inject a parameter / service into template. This can be done in `beforeCompile()` phase by customizing setup of `TemplateConfigurator`.

```php
$templateConfigurator = $containerBuilder->getByType(Nepada\TemplateFactory\TemplateConfigurator::class);
$containerBuilder->getDefinition($templateConfigurator)
    ->addSetup('addFilter', ['filterName', $callback])
    ->addSetup('addProvider', ['provider', $value])
    ->addSetup('addParameter', ['parameter', $value])
    ->addSetup('addParameter', ['parameter', '@someService']);
```
