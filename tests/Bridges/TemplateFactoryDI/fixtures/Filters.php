<?php
/**
 * This file is part of the nepada/template-factory.
 * Copyright (c) 2016 Petr Morávek (petr@pada.cz)
 */

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
