<?php

namespace App\Repositories;

use App\Models\Config;
use App\Product;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Dompdf;
use Webit\WFirmaSDK\Auth\BasicAuth;
use Webit\WFirmaSDK\Entity\EntityApiFactory;
use Webit\WFirmaSDK\Entity\ModuleApiFactory;
use Webit\WFirmaSDK\Entity\Parameters\Parameters;
use Webit\WFirmaSDK\Invoices\DownloadParameters;
use Webit\WFirmaSDK\Invoices\InvoiceId;

class WfirmaApiRepository
{
    private $login;
    private $password;
    private $company_id;
    public $request = [];
    public $module;
    public $path;

    public function __construct($login, $password, $company_id = null)
    {
        //$this->configs = Config::all()->groupBy('name')->toArray();
        $this->login = $login;
        $this->password = $password;
        $this->company_id = $company_id;
    }

    public function addInvoice($invoice)
    {
        $this->setPath('invoices', 'add');
        $result = $this->getPostEdit($invoice);
        return json_decode($result, true);;
    }

    public function addReceipt($invoice)
    {
        $this->setPath('invoices', 'add');
        $result = $this->getPostEdit($invoice);
        return json_decode($result, true);;
    }

    public function addContractor($name, $address, $nip, $zip, $city, $country)
    {
        $this->setPath('contractors', 'add');

        $parameters['contractors']['contractor']['name'] = $name;
        $parameters['contractors']['contractor']['nip'] = $nip;
        $parameters['contractors']['contractor']['street'] = $address;
        $parameters['contractors']['contractor']['zip'] = $zip;
        $parameters['contractors']['contractor']['city'] = $city;
        $parameters['contractors']['contractor']['country'] = $country;
        $result = $this->getPostEdit($parameters);
        return $result;
    }

    public function addGood($name, $unit, $count, $brutto, $vat)
    {
        $this->setPath('goods', 'add');

        $parameters['goods']['good']['name'] = $name;
        $parameters['goods']['good']['unit'] = $unit;
        $parameters['goods']['good']['count'] = $count;
        $parameters['goods']['good']['brutto'] = $brutto;
        $parameters['goods']['good']['vat'] = $vat;
        $result = $this->getPostEdit($parameters);
        return $result;
    }

    public function getContractors($page, $limit)
    {
        $this->setPath('contractors', 'find');
        $this->setParameter('page', $page);
        $this->setParameter('limit', $limit);
        $result = json_decode($this->getPost(), true);
        return $result;
    }

    public function getSeries()
    {
        $this->setPath('series', 'find');

        $this->setParameter('limit', 100);
        $result = json_decode($this->getPost(), true);
        return $result;
    }

    public function getGoods($page, $limit)
    {
        $this->setPath('goods', 'find');
        $this->setParameter('page', $page);
        $this->setParameter('limit', $limit);
        $result = json_decode($this->getPost(), true);
        return $result;
    }

    public function downloadInvoice($id)
    {
        $this->setPath('invoices', 'download/' . $id);
        $result = $this->getPost();
        return $result;
    }

    public function getInvoiceFromOrderId($id)
    {
        $this->setPath('invoices', 'find');
        $page = 1;
        $limit = 20;
        $true = true;
        $invoices = [];
        while ($true) {
            $parameters = [];
            $parameters['page'] = $page;
            $parameters['limit'] = $limit;
            foreach ($parameters as $key => $value) {
                $this->setParameter($key, $value);
            }
            $result = json_decode($this->getPost(), true);

            $resultTotal = intval($result['invoices']['parameters']['total']);

            if ($resultTotal <= $limit * $page) {
                $true = false;
            }
            $page++;
            unset($result['invoices']['parameters']);
            $invoices = array_merge($invoices, $result['invoices']);
        }
        return $invoices;
    }

    public function editInvoiceDate($id, $date)
    {
        $this->setPath('invoices', 'edit/' . $id);
        $parameters = [];
        $parameters['invoices']['invoice']['disposaldate'] = $date;
        $parameters['invoices']['invoice']['date'] = $date;

        $result = json_decode($this->getPostEdit($parameters), true);
        return $result;
    }

    public function getInvoice($id)
    {
        $this->setPath('invoices', 'get/' . $id);
        $result = json_decode($this->getPost(), true);
        return $result;
    }

    public function getAllInvoices()
    {
        $this->setPath('invoices', 'find');
        $page = 1;
        $limit = 20;
        $true = true;
        $invoices = [];
        while ($true) {
            $parameters = [];
            $parameters['page'] = $page;
            $parameters['limit'] = $limit;
            foreach ($parameters as $key => $value) {
                $this->setParameter($key, $value);
            }
            $result = json_decode($this->getPost(), true);
            if (isset($result['invoices'])) {
                $resultTotal = intval($result['invoices']['parameters']['total']);

                if ($resultTotal <= $limit * $page) {
                    $true = false;
                }
                $page++;
                unset($result['invoices']['parameters']);
                $invoices = array_merge($invoices, $result['invoices']);
            } else {
                $true = false;
            }
        }

        return $invoices;

    }

    public function setParameter($key, $value)
    {
        $v = is_int($value) ? (string)$value : $value;
        $this->request[$this->module]["parameters"][(string)$key] = $v;
    }

    public function setPath($module, $call)
    {
        $this->path = $module . '/' . $call . '?inputFormat=json&outputFormat=json';
        $this->module = $module;
        $this->request[$module]["parameters"] = [];
    }

    public function getPost()
    {
        $ch = curl_init();
        $path = $this->path;
        $request = json_encode($this->request, JSON_FORCE_OBJECT);
        curl_setopt($ch, CURLOPT_URL, "https://api2.wfirma.pl/{$path}");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        curl_setopt($ch, CURLOPT_USERPWD, $this->login . ':' . $this->password);
        return curl_exec($ch);
    }

    public function getPostEdit($parameters)
    {
        $ch = curl_init();
        $path = $this->path;
        $request = json_encode($parameters, JSON_FORCE_OBJECT);
        curl_setopt($ch, CURLOPT_URL, "https://api2.wfirma.pl/{$path}");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        curl_setopt($ch, CURLOPT_USERPWD, $this->login . ':' . $this->password);
        return curl_exec($ch);

    }

}
