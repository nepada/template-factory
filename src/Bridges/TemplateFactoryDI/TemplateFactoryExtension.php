<?php
declare(strict_types = 1);

namespace Nepada\Bridges\TemplateFactoryDI;

use Nepada\TemplateFactory;
use Nette;

class TemplateFactoryExtension extends Nette\DI\CompilerExtension
{

    /** @var mixed[] */
    public $defaults = [
        'parameters' => [],
        'providers' => [],
        'filters' => [],
    ];

    public function loadConfiguration(): void
    {
        $this->validateConfig($this->defaults);
        $container = $this->getContainerBuilder();

        $container->addDefinition($this->prefix('templateConfigurator'))
            ->setClass(TemplateFactory\TemplateConfigurator::class)
            ->addSetup('setTranslator');
    }

    public function beforeCompile(): void
    {
        $container = $this->getContainerBuilder();

        $templateFactory = $container->getDefinitionByType(Nette\Application\UI\ITemplateFactory::class);
        $templateFactory->addSetup(
            '?->onCreate[] = function (Nette\Application\UI\ITemplate $template): void {?->configure($template);}',
            ['@self', $this->prefix('@templateConfigurator')]
        );

        $templateConfigurator = $container->getDefinition($this->prefix('templateConfigurator'));

        foreach ($this->config['parameters'] as $name => $value) {
            $templateConfigurator->addSetup('addParameter', [$name, $value]);
        }

        foreach ($this->config['providers'] as $name => $value) {
            $templateConfigurator->addSetup('addProvider', [$name, $value]);
        }

        foreach ($this->config['filters'] as $name => $filter) {
            $templateConfigurator->addSetup('addFilter', [$name, $filter]);
        }
    }

}
