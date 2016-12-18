<?php
/**
 * This file is part of the nepada/template-factory.
 * Copyright (c) 2016 Petr Morávek (petr@pada.cz)
 */

namespace NepadaTests\Bridges\TemplateFactoryDI;


class FooConfigurator
{

    public $template;


    public function callback($template)
    {
        $this->template = $template;
    }

}
