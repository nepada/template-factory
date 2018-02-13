<?php
declare(strict_types = 1);

namespace NepadaTests\Bridges\TemplateFactoryDI;


class Filters
{

    /**
     * @return string
     */
    public static function lower(): string
    {
        return 'lower filter';
    }

    /**
     * @return string
     */
    public static function upper(): string
    {
        return 'upper filter';
    }

}
