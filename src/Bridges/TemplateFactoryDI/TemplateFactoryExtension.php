<?php
/**
 * This file is part of the nepada/template-factory.
 * Copyright (c) 2016 Petr Morávek (petr@pada.cz)
 */

namespace Nepada\Bridges\TemplateFactoryDI;

use Nepada\TemplateFactory;
use Nette;


class TemplateFactoryExtension extends Nette\DI\CompilerExtension
{

    /** @var array */
    public $defaults = [
        'parameters' => [],
        'filters' => [],
    ];


    public function loadConfiguration()
    {
        $config = $this->validateConfig($this->defaults);
        $container = $this->getContainerBuilder();

        $templateFactory = $this->getNetteTemplateFactory();
        $templateFactory->setAutowired(false);

        $container->addDefinition($this->prefix('templateConfigurator'))
            ->setClass(TemplateFactory\TemplateConfigurator::class, $config);

        $container->addDefinition($this->prefix('templateFactory'))
            ->setClass(Nette\Application\UI\ITemplateFactory::class)
            ->setFactory(TemplateFactory\TemplateFactory::class, [$templateFactory])
            ->addSetup(
                '?->onCreateTemplate[] = function ($template) {?->configure($template);}',
                ['@self', $this->prefix('@templateConfigurator')]
            );
    }

    /**
     * Make sure that LatteExtension is loaded before us and return its TemplateFactory definition.
     *
     * @return Nette\DI\ServiceDefinition
     */
    public function getNetteTemplateFactory()
    {
        $latteExtension = $this->compiler->getExtensions(Nette\Bridges\ApplicationDI\LatteExtension::class);
        if (!$latteExtension) {
            throw new Nette\InvalidStateException("LatteExtension not found, did you register it in your configuration?");
        }

        $container = $this->getContainerBuilder();
        $templateFactory = reset($latteExtension)->prefix('templateFactory');
        if (!$container->hasDefinition($templateFactory)) {
            throw new Nette\InvalidStateException("TemplateFactory service from LatteExtension not found. Make sure LatteExtension is loaded before TemplateFactoryExtension.");
        }

        return $container->getDefinition($templateFactory);
    }

}
