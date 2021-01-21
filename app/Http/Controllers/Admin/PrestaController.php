<?php

namespace App\Http\Controllers\Admin;

use App\Models\LogDetail;
use App\Models\PrestaProduct;
use App\Models\WFirmaGood;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Protechstudio\PrestashopWebService\PrestashopWebService;
use Protechstudio\PrestashopWebService\PrestashopWebServiceFacade;

class PrestaController extends Controller
{
    private $prestashop;

    public function __construct()
    {
        $this->prestashop = new PrestashopWebService('https://belamesa-sklep.pl/', 'QETVZIIL3S8DJTPC2M7KNQD6CEYE7PG1', false);
    }

    public function getProducts($log = null)
    {
        $opt['resource'] = 'products';
        $opt['display'] = 'full';
        $opt['limit'] = '5';
        $xml = $this->prestashop->get($opt);

        $json = json_encode($xml);
        $products = json_decode($json, TRUE);

        foreach ($products['products']['product'] as $product) {
            if (is_array($product['reference']) || is_null($product['reference'])) {
                continue;
            }
            $prestaProduct = PrestaProduct::updateOrCreate([
                'presta_id' => $product['id'],
            ], [
                'name' => $product['name']['language'],
                //'count' => $product['quantity'],
                'code' => $product['reference'],
                'status' => 1,
                'presta_config_id' => 1,
            ]);

            $wfirma = WFirmaGood::whereCode($product['reference'])->first();

            if (isset($wfirma->id)) {
                if (!is_null($wfirma->count)) {
                    $newCount = (int)$wfirma->count;
                    if ($prestaProduct['count'] != $newCount) {


                        $stokid = $product['associations']['stock_availables']['stock_available']['id'];
                        $attrid = $product['associations']['stock_availables']['stock_available']['id_product_attribute'];
                        $this->set_product_quantity($product['id'], $stokid, $attrid, $newCount);
                        LogDetail::create([
                            'log_id' => $log->id,
                            'product_code' => $product['reference'],
                            'product_name' => $product['name']['language'],
                            'count_after' => $newCount,
                            'count_before' => $prestaProduct['count'],
                            'product_id' => $product['id'],
                            'status' => 1,
                        ]);
                        $prestaProduct->update(['count' => $newCount]);
                    }
                }
            }

        }
        return true;
    }

    public function getStockAvailable()
    {
        $opt['resource'] = 'stock_availables';
        $opt['display'] = 'full';
        $xml = $this->prestashop->get($opt);

        $json = json_encode($xml);
        $products = json_decode($json, TRUE);
        foreach ($products['stock_availables']['stock_available'] as $product) {
            PrestaProduct::where('presta_id', $product['id_product'])->update([
                'count' => $product['quantity']
            ]);
        }
        dd($products);
    }

    private function set_product_quantity($ProductId, $StokId, $AttributeId, $newValue)
    {
        //global $webService;
        $opt['resource'] = 'stock_availables';
        $opt['filter'] = 'schema=blank';
        $xml = $this->prestashop->get($opt);
        $resources = $xml->children()->children();
        $resources->id = $StokId;
        $resources->id_product = $ProductId;
        $resources->quantity = $newValue;
        $resources->id_shop = 1;
        $resources->out_of_stock = 0;
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
