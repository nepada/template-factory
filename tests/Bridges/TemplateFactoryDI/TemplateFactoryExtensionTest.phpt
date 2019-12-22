<?php
declare(strict_types = 1);

namespace NepadaTests\Bridges\TemplateFactoryDI;

use Latte\Loaders\StringLoader;
use Nepada\TemplateFactory\TemplateConfigurator;
use NepadaTests\Bridges\TemplateFactoryDI\Fixtures\FooConfigurator;
use NepadaTests\Bridges\TemplateFactoryDI\Fixtures\MockTranslatorFactory;
use NepadaTests\TestCase;
use Nette;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';


/**
 * @testCase
 */
class TemplateFactoryExtensionTest extends TestCase
{

    private Nette\DI\Container $container;

    public function testServices(): void
    {
        Assert::type(TemplateConfigurator::class, $this->container->getService('templateFactory.templateConfigurator'));
    }

    public function testTemplate(): void
    {
        /** @var Nette\Bridges\ApplicationLatte\Template $template */
        $template = $this->container->getByType(Nette\Application\UI\ITemplateFactory::class)->createTemplate();
        $template->getLatte()->setLoader(new StringLoader());

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
        Assert::same(MockTranslatorFactory::TRANSLATED_MESSAGE, $latte->invokeFilter('translate', ['message']));
    }

    protected function setUp(): void
    {
        $configurator = new Nette\Configurator();
        $configurator->setTempDirectory(TEMP_DIR);
        $configurator->setDebugMode(true);
        $configurator->addConfig(__DIR__ . '/Fixtures/config.neon');
        $this->container = $configurator->createContainer();
    }

}


(new TemplateFactoryExtensionTest())->run();
