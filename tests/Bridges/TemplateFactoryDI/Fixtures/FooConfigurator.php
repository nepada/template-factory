<?php
declare(strict_types = 1);

namespace NepadaTests\Bridges\TemplateFactoryDI\Fixtures;

use Nette\Application\UI;

class FooConfigurator
{

    /** @var UI\ITemplate|null */
    public $template;

    public function callback(UI\ITemplate $template): void
    {
        $this->template = $template;
    }

}
