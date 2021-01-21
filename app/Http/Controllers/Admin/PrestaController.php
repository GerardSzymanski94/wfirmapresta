<?php

namespace App\Http\Controllers\Admin;

use App\Models\PrestaProduct;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Protechstudio\PrestashopWebService\PrestashopWebService;
use Protechstudio\PrestashopWebService\PrestashopWebServiceFacade;

class PrestaController extends Controller
{
    private $prestashop;

    public function __construct(PrestashopWebService $prestashop)
    {
        $this->prestashop = $prestashop;
    }

    public function getProducts()
    {

        $opt['resource'] = 'products';
        $opt['display'] = 'full';
        $opt['limit'] = '100';
        $xml = $this->prestashop->get($opt);

        $json = json_encode($xml);
        $products = json_decode($json, TRUE);
        foreach ($products['products']['product'] as $product) {
            if (is_array($product['reference']) || is_null($product['reference'])) {
                continue;
            }
            PrestaProduct::updateOrCreate([
                'presta_id' => $product['id'],
            ], [
                'name' => $product['name']['language'],
                'count' => $product['quantity'],
                'code' => $product['reference'],
                'status' => 1,
                'presta_config_id' => 1,
            ]);

            //  $stokid = $product['associations']['stock_availables']['stock_available']['id'];
            //  $attrid = $product['associations']['stock_availables']['stock_available']['id_product_attribute'];
            // $this->set_product_quantity($product['id'], $stokid, $attrid);
        }
        echo 'end';
        dd();
    }

    private function set_product_quantity($ProductId, $StokId, $AttributeId)
    {
        //global $webService;
        $opt['resource'] = 'stock_availables';
        $opt['filter'] = 'schema=blank';
        $xml = $this->prestashop->get($opt);
        $resources = $xml->children()->children();
        $resources->id = $StokId;
        $resources->id_product = $ProductId;
        $resources->quantity = 9;
        $resources->id_shop = 1;
        $resources->out_of_stock = 1;
        $resources->depends_on_stock = 0;
        $resources->id_product_attribute = $AttributeId;
        try {
            $opt = array('resource' => 'stock_availables');
            $opt['putXml'] = $xml->asXML();
            $opt['id'] = $StokId;
            $xml = $this->prestashop->edit($opt);
        } catch (PrestaShopWebserviceException $ex) {
            echo "<b>Error al setear la cantidad  ->Error : </b>" . $ex->getMessage() . '<br>';
        }
        //  dd($xml);
        return $xml;
    }

    public function editCountInPresta($product)
    {
        $opt['resource'] = 'products';
        $opt['display'] = 'full';
        $xml = $this->prestashop->edit($opt);
    }
}
