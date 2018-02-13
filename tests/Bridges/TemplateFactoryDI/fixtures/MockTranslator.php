<?php
declare(strict_types = 1);

namespace NepadaTests\Bridges\TemplateFactoryDI;

use Nette;


class MockTranslator implements Nette\Localization\ITranslator
{

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
     * @param string $message
     * @param int|null $count
     * @return string
     */
    public function translate($message, $count = null)
    {
        return 'translated message';
    }

}
