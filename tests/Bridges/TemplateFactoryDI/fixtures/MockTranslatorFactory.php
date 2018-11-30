<?php
declare(strict_types = 1);

namespace NepadaTests\Bridges\TemplateFactoryDI;

use Mockery;
use Nette\Localization\ITranslator;

class MockTranslatorFactory
{

    public const TRANSLATED_MESSAGE = 'translated message';

    /**
     * @return ITranslator|Mockery\MockInterface
     */
    public static function create(): ITranslator
    {
        $translator = Mockery::mock(ITranslator::class);
        $translator->shouldReceive('translate')->andReturn(self::TRANSLATED_MESSAGE);
        return $translator;
    }

}
