<?php

namespace RedSnapper\DocCheck;

use Illuminate\Support\Facades\Facade;


class DocCheckFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'doccheck';
    }
}
