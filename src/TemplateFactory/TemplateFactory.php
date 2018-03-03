<?php
declare(strict_types = 1);

namespace Nepada\TemplateFactory;

use Nette;
use Nette\Application\UI;

/**
 * @method onCreateTemplate(UI\ITemplate $template): void
 */
class TemplateFactory implements UI\ITemplateFactory
{

    use Nette\SmartObject;

    /** @var callable[] function(UI\ITemplate $template) */
    public $onCreateTemplate;

    /** @var UI\ITemplateFactory */
    private $baseFactory;

    public function __construct(UI\ITemplateFactory $baseFactory)
    {
        $this->baseFactory = $baseFactory;
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
     * @param UI\Control|null $control
     * @return UI\ITemplate
     */
    public function createTemplate(?UI\Control $control = null)
    {
        $template = $this->baseFactory->createTemplate($control);
        $this->onCreateTemplate($template);
        return $template;
    }

}
