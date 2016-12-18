<?php
/**
 * Test: Nepada\Bridges\TemplateFactoryDI\TemplateFactoryExtension
 *
 * This file is part of the nepada/template-factory.
 * Copyright (c) 2016 Petr Morávek (petr@pada.cz)
 */

namespace NepadaTests\Bridges\TemplateFactoryDI;

use Latte\Loaders\StringLoader;
use Nepada\TemplateFactory\TemplateConfigurator;
use Nepada\TemplateFactory\TemplateFactory;
use Nette;
use Tester;
use Tester\Assert;


require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/fixtures/Filters.php';
require_once __DIR__ . '/fixtures/FooConfigurator.php';
require_once __DIR__ . '/fixtures/MockTranslator.php';


class TemplateFactoryExtensionTest extends Tester\TestCase
{

    /** @var Nette\DI\Container */
    private $container;


    public function setUp()
    {
        $configurator = new Nette\Configurator;
        $configurator->setTempDirectory(TEMP_DIR);
        $configurator->setDebugMode(true);
        $configurator->addConfig(__DIR__ . '/fixtures/config.neon');
        $this->container = $configurator->createContainer();
    }

    public function testServices()
    {
        Assert::type(TemplateConfigurator::class, $this->container->getService('templateFactory.templateConfigurator'));
        Assert::type(TemplateFactory::class, $this->container->getService('templateFactory.templateFactory'));
    }

    public function testTemplate()
    {
        /** @var Nette\Bridges\ApplicationLatte\Template $template */
        $template = $this->container->getByType(Nette\Application\UI\ITemplateFactory::class)->createTemplate();
        $template->getLatte()->setLoader(new StringLoader);

        // parameters
        Assert::same('bar', $template->foo);
        Assert::same($this->container->getService('application.application'), $template->application);

        // providers
        $latteTemplate = $template->getLatte()->createTemplate('test');
        Assert::same('bar', $latteTemplate->global->fooProvider);
        Assert::same($this->container->getService('application.application'), $latteTemplate->global->applicationProvider);

        // onCreateTemplate event
        Assert::same($template, $this->container->getByType(FooConfigurator::class)->template);

        $latte = $template->getLatte();

        // filters
        Assert::same('lower filter', $latte->invokeFilter('lower', []));
        Assert::same('upper filter', $latte->invokeFilter('upper', []));

        // translator
        Assert::same('translated message', $latte->invokeFilter('translate', ['message']));
    }

}


\run(new TemplateFactoryExtensionTest());
