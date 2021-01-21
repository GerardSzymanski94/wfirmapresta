<?php

namespace App\Repositories;


use Protechstudio\PrestashopWebService\PrestashopWebService;

class PrestashopApiRepository
{
    private $prestashop;

    public function __construct(PrestashopWebService $prestashop)
    {
        dd($prestashop);
        $this->prestashop = $prestashop;
    }

    public function getProducts()
    {
        $opt['resource'] = 'products';
        $opt['display'] = 'full';
        $xml=Prestashop::get($opt);
        dd($xml);
    }
}
