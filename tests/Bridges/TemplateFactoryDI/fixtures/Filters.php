<?php
declare(strict_types = 1);

namespace NepadaTests\Bridges\TemplateFactoryDI;


class Filters
{

    public static function lower(): string
    {
        return 'lower filter';
    }

    public static function upper(): string
    {
        return 'upper filter';
    }

}
