<?php

namespace FilippoToso\VatApi;

use Illuminate\Support\Facades\Facade;

class VatApiFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'vat';
    }
    
}
