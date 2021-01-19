<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 19.05.2020
 * Time: 14:27
 */

namespace App\Repositories;

use PhpOffice\PhpSpreadsheet\Writer\Pdf\Dompdf;

class wFirmaClass
{
    public $request = [];
    public $module;
    public $path;
    protected $login;
    protected $password;
    protected $company_id;

    public function __construct($module, $call)
    {
        $this->path = $module . '/' . $call . '?inputFormat=json&outputFormat=json';
        $this->module = $module;
        $this->request[$module]["parameters"] = [];
    }

    public function setLogin($login)
    {
        $this->login = $login;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function setCompanyId($company_id)
    {
        $this->company_id = $company_id;
    }

    public function execute()
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
        /*$saveTo = 'logo.pdf';

        $fp = fopen($saveTo, 'w+');

        if ($fp === false) {
            throw new Exception('Could not open: ' . $saveTo);
        }
        $ch = curl_init();

        $path = $this->path;*/

        /* curl_setopt($ch, CURLOPT_URL, "https://api2.wfirma.pl/{$path}");
         curl_setopt($ch, CURLOPT_POST, 1);
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

         curl_setopt($ch, CURLOPT_FILE, $fp);
         curl_setopt($ch, CURLOPT_TIMEOUT, 20);

         curl_setopt($ch, CURLOPT_USERPWD, $login . ':' . $password);

         $result = curl_exec($ch);
         return $result;*/
    }



    public function setParameter($key, $value)
    {
        $v = is_int($value) ? (string)$value : $value;
        $this->request[$this->module]["parameters"][(string)$key] = $v;
    }

    public function addCondition($field, $op, $val)
    {
        $p = "parameters";
        $c = "conditions";
        $m = $this->module;
        $u = array_key_exists($c, $this->request[$m][$p]) ? count($this->request[$m][$p][$c]) : "0";
        $this->request[$m][$p][$c][$u]["condition"] = array(
            "field" => $field,
            "operator" => $op,
            "value" => $val
        );
    }

    public function setOrder($field, $way)
    {
        $this->setParameter("order", array($way => $field));
    }

    public function setFields($arr)
    {
        foreach ($arr as $field) {
            $p = "parameters";
            $c = "fields";
            $m = $this->module;
            $u = array_key_exists($c, $this->request[$m][$p]) ? count($this->request[$m][$p][$c]) : "0";
            $this->request[$m][$p][$c][$u]["field"] = $field;
        }
    }

    public function printRequest()
    {
        return json_encode($this->request, JSON_FORCE_OBJECT);
    }
}
