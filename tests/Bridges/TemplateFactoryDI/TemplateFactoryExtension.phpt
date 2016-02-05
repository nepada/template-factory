<?php
/**
 * This file is part of the nepada/template-factory.
 * Copyright (c) 2016 Petr Morávek (petr@pada.cz)
 */

namespace NepadaTests\Bridges\TemplateFactoryDI;

use Nepada\TemplateFactory\TemplateConfigurator;
use Nepada\TemplateFactory\TemplateFactory;
use Nette;
use Tester;
use Tester\Assert;


require_once __DIR__ . '/../../bootstrap.php';


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

        // parameters
        Assert::same('bar', $template->foo);
        Assert::same($this->container->getService('application.application'), $template->application);

        // filters
        $filters = $template->getLatte()->getFilters();
        Assert::same('Nette\Utils\Strings::firstLower', $filters['lower']);
        Assert::same('Nette\Utils\Strings::firstUpper', $filters['upper']);

        // translator
        Assert::same('translated message', call_user_func($filters['translate'], 'test'));

        // onCreateTemplate event
        Assert::same($template, $this->container->getByType(FooConfigurator::class)->template);
    }

}


class MockTranslator implements Nette\Localization\ITranslator
{

    public function translate($message, $count = null)
    {
        return 'translated message';
    }

}


class FooConfigurator
{

    public $template;


    public function callback($template)
    {
        $this->template = $template;
    }

}


\run(new TemplateFactoryExtensionTest());
