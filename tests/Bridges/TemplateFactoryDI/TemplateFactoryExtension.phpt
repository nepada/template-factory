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

        // onCreateTemplate event
        Assert::same($template, $this->container->getByType(FooConfigurator::class)->template);

        $latte = $template->getLatte();

        if (!method_exists($latte, 'invokeFilter')) { // Latte 2.2
            $filters = $latte->getFilters();

            // filters
            Assert::same('lower filter', call_user_func($filters['lower']));
            Assert::same('upper filter', call_user_func($filters['upper']));

            // translator
            Assert::same('translated message', call_user_func($filters['translate'], 'message'));

        } else {
            // filters
            Assert::same('lower filter', $latte->invokeFilter('lower', []));
            Assert::same('upper filter', $latte->invokeFilter('upper', []));

            // translator
            Assert::same('translated message', $latte->invokeFilter('translate', ['message']));
        }
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


class Filters
{

    public static function lower()
    {
        return 'lower filter';
    }

    public static function upper()
    {
        return 'upper filter';
    }

}


\run(new TemplateFactoryExtensionTest());
