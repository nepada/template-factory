<?php
/**
 * This file is part of the nepada/template-factory.
 * Copyright (c) 2016 Petr Morávek (petr@pada.cz)
 */

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
