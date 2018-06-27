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

        $templateFactory = $this->getNetteTemplateFactory();
        $templateFactory->addSetup(
            '?->onCreate[] = function (Nette\Application\UI\ITemplate $template): void {?->configure($template);}',
            ['@self', $this->prefix('@templateConfigurator')]
        );
    }

    public function beforeCompile(): void
    {
        $templateConfigurator = $this->getContainerBuilder()->getDefinition($this->prefix('templateConfigurator'));

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

    /**
     * Make sure that LatteExtension is loaded before us and return its TemplateFactory definition.
     *
     * @return Nette\DI\ServiceDefinition
     */
    public function getNetteTemplateFactory(): Nette\DI\ServiceDefinition
    {
        $latteExtension = $this->compiler->getExtensions(Nette\Bridges\ApplicationDI\LatteExtension::class);
        if ($latteExtension === []) {
            throw new \LogicException('LatteExtension not found, did you register it in your configuration?');
        }

        $container = $this->getContainerBuilder();
        $templateFactory = reset($latteExtension)->prefix('templateFactory');
        if (!$container->hasDefinition($templateFactory)) {
            throw new \LogicException('TemplateFactory service from LatteExtension not found. Make sure LatteExtension is loaded before TemplateFactoryExtension.');
        }

        return $container->getDefinition($templateFactory);
    }

}
