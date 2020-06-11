<?php
declare(strict_types = 1);

namespace Nepada\TemplateFactory;

use Nette;

class TemplateConfigurator
{

    use Nette\SmartObject;

    /**
     * @var mixed[]
     */
    private array $parameters = [];

    /**
     * @var mixed[]
     */
    private array $providers = [];

    /**
     * @var callable[]
     */
    private array $filters = [];

    /**
     * @var callable[]
     */
    private array $functions = [];

    private ?Nette\Localization\ITranslator $translator = null;

    /**
     * @param Nette\Localization\ITranslator|null $translator
     * @return static
     */
    public function setTranslator(?Nette\Localization\ITranslator $translator = null): self
    {
        $this->translator = $translator;
        return $this;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return static
     */
    public function addParameter(string $name, $value): self
    {
        $this->parameters[$name] = $value;
        return $this;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return static
     */
    public function addProvider(string $name, $value): self
    {
        $this->providers[$name] = $value;
        return $this;
    }

    /**
     * @param string $name
     * @param callable $filter
     * @return static
     */
    public function addFilter(string $name, callable $filter): self
    {
        $this->filters[$name] = $filter;
        return $this;
    }

    /**
     * @param string $name
     * @param callable $function
     * @return static
     */
    public function addFunction(string $name, callable $function): self
    {
        $this->functions[$name] = $function;
        return $this;
    }

    public function configure(Nette\Application\UI\ITemplate $template): void
    {
        if (! $template instanceof Nette\Bridges\ApplicationLatte\Template) {
            return;
        }

        $latte = $template->getLatte();

        if ($this->translator !== null) {
            $template->setTranslator($this->translator);
        }

        foreach ($this->providers as $name => $provider) {
            $latte->addProvider($name, $provider);
        }

        foreach ($this->filters as $name => $filter) {
            $template->addFilter($name, $filter);
        }

        foreach ($this->functions as $name => $function) {
            $latte->addFunction($name, $function);
        }

        foreach ($this->parameters as $name => $value) {
            $template->$name = $value;
        }
    }

}
