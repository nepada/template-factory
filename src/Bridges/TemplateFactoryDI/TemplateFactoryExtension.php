<?php
declare(strict_types = 1);

namespace Nepada\Bridges\TemplateFactoryDI;

use Nepada\TemplateFactory;
use Nette;
use Nette\DI\Definitions\ServiceDefinition;

class TemplateFactoryExtension extends Nette\DI\CompilerExtension
{

    public function getConfigSchema(): Nette\Schema\Schema
    {
        return Nette\Schema\Expect::structure([
            'parameters' => Nette\Schema\Expect::array(),
            'providers' => Nette\Schema\Expect::array(),
            'filters' => Nette\Schema\Expect::array()->items('callable'),
            'functions' => Nette\Schema\Expect::array()->items('callable'),
        ]);
    }

    public function loadConfiguration(): void
    {
        $container = $this->getContainerBuilder();

        $container->addDefinition($this->prefix('templateConfigurator'), new ServiceDefinition())
            ->setType(TemplateFactory\TemplateConfigurator::class)
            ->addSetup('setTranslator');
    }

    public function beforeCompile(): void
    {
        $container = $this->getContainerBuilder();
        $config = $this->getConfig();
        assert($config instanceof \stdClass);

        $templateFactory = $container->getDefinitionByType(Nette\Application\UI\ITemplateFactory::class);
        assert($templateFactory instanceof ServiceDefinition);
        $templateFactory->addSetup(
            '?->onCreate[] = function (Nette\Application\UI\ITemplate $template): void {?->configure($template);}',
            ['@self', $this->prefix('@templateConfigurator')],
        );

        $templateConfigurator = $container->getDefinition($this->prefix('templateConfigurator'));
        assert($templateConfigurator instanceof ServiceDefinition);

        foreach ($config->parameters as $name => $value) {
            $templateConfigurator->addSetup('addParameter', [$name, $value]);
        }

        foreach ($config->providers as $name => $value) {
            $templateConfigurator->addSetup('addProvider', [$name, $value]);
        }

        foreach ($config->filters as $name => $filter) {
            $templateConfigurator->addSetup('addFilter', [$name, $filter]);
        }

        foreach ($config->functions as $name => $function) {
            $templateConfigurator->addSetup('addFunction', [$name, $function]);
        }
    }

}
