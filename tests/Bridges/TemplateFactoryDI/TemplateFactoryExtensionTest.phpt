<?php
declare(strict_types = 1);

namespace NepadaTests\Bridges\TemplateFactoryDI;

use Latte\Loaders\StringLoader;
use Nepada\TemplateFactory\TemplateConfigurator;
use NepadaTests\Bridges\TemplateFactoryDI\Fixtures\FooControl;
use NepadaTests\Bridges\TemplateFactoryDI\Fixtures\MockTranslatorFactory;
use NepadaTests\Environment;
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
        $control = new FooControl();
        /** @var Nette\Bridges\ApplicationLatte\Template $template */
        $template = $this->container->getByType(Nette\Application\UI\ITemplateFactory::class)->createTemplate($control);
        $template->getLatte()->setLoader(new StringLoader());

        // parameters
        Assert::same('bar', $template->foo);
        Assert::same($this->container->getService('application.application'), $template->application);

        // providers
        $latteTemplate = $template->getLatte()->createTemplate('test');
        Assert::same('bar', $latteTemplate->global->fooProvider);
        Assert::same($this->container->getService('application.application'), $latteTemplate->global->applicationProvider);

        $latte = $template->getLatte();

        // filters
        Assert::same('lower filter', $latte->invokeFilter('lower', []));
        Assert::same('upper filter', $latte->invokeFilter('upper', []));

        // translator
        Assert::same(MockTranslatorFactory::TRANSLATED_MESSAGE, $latte->invokeFilter('translate', ['message']));

        // functions
        Assert::same('LOREM ipsum', $template->renderToString('{=upper(Lorem)} {=lower(Ipsum)}'));
    }

    protected function setUp(): void
    {
        $configurator = new Nette\Configurator();
        $configurator->setTempDirectory(Environment::getTempDir());
        $configurator->setDebugMode(true);
        $configurator->addConfig(__DIR__ . '/Fixtures/config.neon');
        $this->container = $configurator->createContainer();
    }

}


(new TemplateFactoryExtensionTest())->run();
