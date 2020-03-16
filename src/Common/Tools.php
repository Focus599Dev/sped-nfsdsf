<?php

namespace NFePHP\NFSe\DSF\Common;

use NFePHP\NFSe\DSF\Soap\Soap;
use NFePHP\Common\Validator;

class Tools
{

    public $soapUrl;

    public $config;

    public $soap;

    public $pathSchemas;

    public function __construct($configJson)
    {
        $this->pathSchemas = realpath(
            __DIR__ . '/../../schemas'
        ) . '/';

        $this->config = json_decode($configJson);

        $this->soapUrl = 'http://www.issdigitalsod.com.br/WsNFe2/LoteRps.jws?wsdl';
    }

    protected function sendRequest($request, $soapUrl)
    {

        $soap = new Soap;

        $response = $soap->send($request, $soapUrl);

        return (string) $response;
    }

    public function envelopXML($xml, $method)
    {

        $xml = trim(preg_replace("/<\?xml.*?\?>/", "", $xml));

        $this->xml =
            '<dsf:' . $method . ' soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
            <mensagemXml xsi:type="xsd:string"><![CDATA[' . $xml . ']]></mensagemXml>
            </dsf:' . $method . '>';

        return $this->xml;
    }

    public function envelopSoapXML($xml)
    {
        $this->xml =
            '<?xml version="1.0" encoding="UTF-8"?>
            <soapenv:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
                xmlns:xsd="http://www.w3.org/2001/XMLSchema" 
                xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" 
                xmlns:dsf="http://dsfnet.com.br">
                <soapenv:Body>' . $xml . '</soapenv:Body>
            </soapenv:Envelope>';

        return $this->xml;
    }

    public function getLastRequest()
    {
        return $this->lastRequest;
    }

    protected function isValid($body, $method)
    {
        $pathschemes = realpath(__DIR__ . '/../../schemas/') . '/';

        $schema = $pathschemes . $method;

        if (!is_file($schema)) {
            return true;
        }

        return Validator::isValid(
            $body,
            $schema
        );
    }
}
