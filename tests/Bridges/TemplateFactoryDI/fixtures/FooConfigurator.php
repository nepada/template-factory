<?php
declare(strict_types = 1);

namespace NepadaTests\Bridges\TemplateFactoryDI;

use Nette\Application\UI;


class FooConfigurator
{

    /** @var UI\ITemplate|null */
    public $template;


    /**
     * @param UI\ITemplate $template
     */
    public function callback(UI\ITemplate $template): void
    {
        $this->template = $template;
    }

}
