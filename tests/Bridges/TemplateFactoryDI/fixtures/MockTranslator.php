<?php
/**
 * This file is part of the nepada/template-factory.
 * Copyright (c) 2016 Petr Morávek (petr@pada.cz)
 */

namespace NepadaTests\Bridges\TemplateFactoryDI;

use Nette;


class MockTranslator implements Nette\Localization\ITranslator
{

    public function translate($message, $count = null)
    {
        return 'translated message';
    }

}
