<?php

namespace MedeirosDev\Arquivei\Frameworks\Laravel;

use Illuminate\Support\Facades\Facade;

/**
 * Class Arquivei
 *
 * @package MedeirosDev\Arquivei\Frameworks\Laravel
 */
class Arquivei extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \MedeirosDev\Arquivei\Arquivei::class;
    }
}
