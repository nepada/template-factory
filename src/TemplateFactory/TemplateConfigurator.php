<?php
/**
 * This file is part of the nepada/template-factory.
 * Copyright (c) 2016 Petr Morávek (petr@pada.cz)
 */

namespace Nepada\TemplateFactory;

use Nette;


class TemplateConfigurator extends Nette\Object
{

    /** @var array */
    private $parameters = [];

    /** @var callable[] */
    private $filters = [];

    /** @var Nette\Localization\ITranslator */
    private $translator;


    /**
     * @param Nette\Localization\ITranslator $translator
     * @return self
     */
    public function setTranslator(Nette\Localization\ITranslator $translator = null)
    {
        $this->translator = $translator;
        return $this;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return self
     */
    public function addParameter($name, $value)
    {
        $this->parameters[$name] = $value;
        return $this;
    }

    /**
     * @param string $name
     * @param callable $filter
     * @return self
     */
    public function addFilter($name, callable $filter)
    {
        $this->filters[$name] = $filter;
        return $this;
    }

    /**
     * @param Nette\Application\UI\ITemplate $template
     */
    public function configure(Nette\Application\UI\ITemplate $template)
    {
        if (!$template instanceof Nette\Bridges\ApplicationLatte\Template) {
            return;
        }

        if ($this->translator) {
            $template->setTranslator($this->translator);
        }

        foreach ($this->filters as $name => $filter) {
            $template->addFilter($name, $filter);
        }

        $template->setParameters($this->parameters);
    }

}
