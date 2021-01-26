<?php

namespace App\Http\Controllers\Admin;

use App\Models\Log;
use App\Models\WFirmaGood;
use App\Repositories\PrestashopApiRepository;
use App\Repositories\WfirmaApiRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Protechstudio\PrestashopWebService\PrestashopWebService;

class DashboardController extends BaseController
{
    public function index()
    {
        return view('admin.index');
    }

    public function logs()
    {
        $logs = Log::where('id', '>', 0)->orderByDesc('id')->paginate(50);
        return view('admin.logs', compact('logs'));
    }

    public function log(Log $log)
    {
        return view('admin.log', compact('log'));
    }


    public function runUpdate()
    {
        $this->getWFirmaGoods();
    }

    public function getWFirmaGoods($log = null)
    {
        ini_set('max_execution_time', 36000);
        $repo = new WfirmaApiRepository("grazyna.chojnacka@belamesa.pl", 'Magazynowanie2014');

        $true = true;
        $page = 1;
        $limit = 1000;
        while ($true) {
            $goods = $repo->getGoods($page, $limit, 225479);
            if ($goods['status']['code'] == "OK") {

                foreach ($goods['goods'] as $key => $good) {
                    if ($key != 'parameters') {
                        WFirmaGood::updateOrCreate([
                            'code' => $good['good']['code'],
                            'w_firma_config_id' => 1,
                        ], [
                            'name' => $good['good']['name'],
                            'good_id' => $good['good']['id'],
                            'unit' => $good['good']['unit'],
                            'netto' => $good['good']['netto'],
                            'brutto' => $good['good']['brutto'],
                            'lumpcode' => $good['good']['lumpcode'],
                            'classification' => $good['good']['classification'],
                            'discount' => $good['good']['discount'],
                            'description' => $good['good']['description'],
                            'notes' => $good['good']['notes'],
                            'documents' => $good['good']['documents'],
                            'tags' => $good['good']['tags'],
                            'count' => (int)$good['good']['count'],
                        ]);
                    }
                }
                if ($goods['goods']['parameters']['total'] > $page * $limit) {
                    $page++;
                    //$true = false;
                } else {
                    $true = false;
                }
            } else {
                return false;
            }
        }

        $true = true;
        $page = 1;
        $limit = 1000;
        while ($true) {
            $goods = $repo->getGoods($page, $limit, '393229');
            if ($goods['status']['code'] == "OK") {

                foreach ($goods['goods'] as $key => $good) {
                    if ($key != 'parameters') {

                        if (WFirmaGood::whereCode($good['good']['code'])->where('w_firma_config_id', 1)->exists()) {
                            $product = WFirmaGood::whereCode($good['good']['code'])->where('w_firma_config_id', 1)->first();
                            if (!is_int($product->count)) {
                                $product->count = (int)$product->count + (int)$good['good']['count'];
                            } elseif (!is_int($good['good']['count'])) {
                                dd($good);
                            } else {
                                $product->count = (int)$product->count + (int)$good['good']['count'];


                            }
                            //dd($product);
                            $product->save();
                        } else {
                            WFirmaGood::updateOrCreate([
                                'code' => $good['good']['code'],
                                'w_firma_config_id' => 1,
                            ], [
                                'name' => $good['good']['name'],
                                'good_id' => $good['good']['id'],
                                'unit' => $good['good']['unit'],
                                'netto' => $good['good']['netto'],
                                'brutto' => $good['good']['brutto'],
                                'lumpcode' => $good['good']['lumpcode'],
                                'classification' => $good['good']['classification'],
                                'discount' => $good['good']['discount'],
                                'description' => $good['good']['description'],
                                'notes' => $good['good']['notes'],
                                'documents' => $good['good']['documents'],
                                'tags' => $good['good']['tags'],
                                'count' => (int)$good['good']['count'],
                            ]);
                        }
                    }
                }
                if ($goods['goods']['parameters']['total'] > $page * $limit) {
                    $page++;
                  //  $true = false;
                } else {
                    $true = false;
                }
            } else {
                return false;
            }
        }

        return true;
    }

    public function getPrestaProducts()
    {
        $presta = new PrestashopWebService();
        $repo = new PrestashopApiRepository($presta);
        $repo->getProducts();
    }


}
