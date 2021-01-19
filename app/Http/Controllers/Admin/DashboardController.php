<?php

namespace App\Http\Controllers\Admin;

use App\Models\WFirmaGood;
use App\Repositories\WfirmaApiRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends BaseController
{
    public function index()
    {
        return view('admin.index');
    }

    public function getWFirmaGoods()
    {
        ini_set('max_execution_time', 36000);
        $repo = new WfirmaApiRepository("grazyna.chojnacka@belamesa.pl", 'Magazynowanie2014');

        $true = true;
        $page = 1;
        $limit = 20;
        while ($true) {
            $goods = $repo->getGoods($page, $limit);
            if ($goods['status']['code'] == "OK") {

                foreach ($goods['goods'] as $key => $good) {
                    if ($key != 'parameters') {
                        WFirmaGood::updateOrCreate([
                            'good_id' => $good['good']['id'],
                            'w_firma_config_id' => 1,
                        ], [
                            'name' => $good['good']['name'],
                            'code' => $good['good']['code'],
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
                            'count' => $good['good']['count'],
                        ]);
                    }
                }
                if ($goods['goods']['parameters']['total'] > $page * $limit) {
                    $page++;
                } else {
                    $true = false;
                }
            } else {
                return false;
            }
        }
        echo "end";
        dd();
        // dd($repo->getGoods(1, 20));
    }
}
