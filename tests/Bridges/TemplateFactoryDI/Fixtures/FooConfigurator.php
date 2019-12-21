<?php
declare(strict_types = 1);

namespace NepadaTests\Bridges\TemplateFactoryDI\Fixtures;

use Nette\Application\UI;

class FooConfigurator
{

    public ?UI\ITemplate $template = null;

    public function callback(UI\ITemplate $template): void
    {
        $this->template = $template;
    }

}
