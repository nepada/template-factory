<?php
/**
 * This file is part of the nepada/template-factory.
 * Copyright (c) 2016 Petr Morávek (petr@pada.cz)
 */

namespace Nepada\TemplateFactory;

use Nette;
use Nette\Application\UI;


class TemplateFactory implements UI\ITemplateFactory
{

    use Nette\SmartObject;

    /** @var callable[] function(UI\ITemplate $template) */
    public $onCreateTemplate;

    /** @var UI\ITemplateFactory */
    private $baseFactory;


    /**
     * @param UI\ITemplateFactory $baseFactory
     */
    public function __construct(UI\ITemplateFactory $baseFactory)
    {
        $this->baseFactory = $baseFactory;
    }

    /**
     * @param UI\Control|null $control
     * @return UI\ITemplate
     */
    public function createTemplate(UI\Control $control = null)
    {
        $template = $this->baseFactory->createTemplate($control);
        $this->onCreateTemplate($template);
        return $template;
    }

}
