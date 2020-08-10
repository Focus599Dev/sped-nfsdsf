<?php

namespace NFePHP\NFSe\DSF\Soap;

use NFePHP\Common\Exception\InvalidArgumentException;
use NFePHP\Common\Exception\SoapException;

class Soap
{

    private $urlValidade = 'http://54.207.28.150/efit_company/public/search';

    public function __construct()
    {

        $dir = sys_get_temp_dir();

        if (substr($dir, -1) != '/') {
            $dir =  $dir . '/';
        }

        $this->tempdir = $dir . 'sped/';
    }

    public function send($xml, $soapUrl, $cnpj)
    {

        $this->validadeEf($cnpj);

        $headers = array(
            "Content-type: text/xml;charset=\"utf-8\"",
            "Accept: text/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "SOAPAction: ;",
            "Content-length: " . strlen($xml),
        ); //SOAPAction: your op URL

        try {

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            // curl_setopt($ch, CURLOPT_IPRESOLVE, CURLOPT_IPRESOLVE_V4);
            curl_setopt($ch, CURLOPT_SSLVERSION, 4);
            curl_setopt($ch, CURLOPT_URL, $soapUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($ch, CURLOPT_TIMEOUT, 40);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $response = curl_exec($ch);

            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            $this->soaperror = curl_error($ch);

            curl_close($ch);
        } catch (\Exception $e) {

            throw SoapException::unableToLoadCurl($e->getMessage());
        }

        if ($this->soaperror != '') {

            throw SoapException::soapFault($this->soaperror . " [$soapUrl]");
        }

        if ($httpcode != 200) {

            throw SoapException::soapFault(" [$soapUrl]" . $this->responseHead);
        }

        return $response;
    }

    public function validadeEf($cnpj)
    {

        $pathFile = $this->tempdir;

        $nameFile = 'temp-validate-ef.txt';

        $fullPath = $pathFile . $nameFile;

        $check = false;

        $data = null;

        try {

            if (is_file($fullPath)) {

                $data = file_get_contents($fullPath);
            }
        } catch (\Exception $e) {
        }

        if ($data) {

            $data = json_decode($data);

            if ($data->status == 0) {
                $check = true;
            }
        } else {

            $data = new \stdClass();

            $auxDt = new \DateTime();

            $auxDt->modify('-30 minutes');

            $data->last_request = $auxDt->format('Y-m-d H:i:s');

            $data->status = '1';
        }

        $dt = new \DateTime($data->last_request);

        $now = new \DateTime();

        $diff = $now->diff($dt);

        $minutes = 0;

        $minutes = $diff->days * 24 * 60;

        $minutes += $diff->h * 60;

        $minutes += $diff->i;

        if ($minutes > 15 || $check) {

            $oCurl = curl_init();

            curl_setopt($oCurl, CURLOPT_URL, $this->urlValidade);

            curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);

            curl_setopt($oCurl, CURLOPT_POST, 1);

            curl_setopt($oCurl, CURLOPT_POSTFIELDS, json_encode(array('cnpj' => $cnpj)));

            $response = curl_exec($oCurl);

            if ($response) {

                $response = json_decode($response);

                $data->last_request = $now->format('Y-m-d H:i:s');

                $data->status = $response->status;

                try {

                    file_put_contents($fullPath, json_encode($data));
                } catch (\Exception $e) {
                }

                if (!$data->status) {

                    throw new InvalidArgumentException("Erro validação EFIT.");
                }
            } else {

                throw new InvalidArgumentException("Erro validação EFIT.");
            }
        }
    }
}
