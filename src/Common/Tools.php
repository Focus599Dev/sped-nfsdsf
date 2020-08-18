<?php

namespace NFePHP\NFSe\DSF\Common;

use NFePHP\NFSe\DSF\Soap\Soap;
use NFePHP\Common\Validator;
use NFePHP\NFSe\DSF\Make;
use NFePHP\NFSe\DSF\Factories\CreateHash;

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

        if ($this->config->municipio == '3552205') { #Sorocaba

            $this->soapUrl = 'http://www.issdigitalsod.com.br/WsNFe2/LoteRps.jws?wsdl';
        } elseif ($this->config->municipio == '3170206') { #Uberlândia

            $this->soapUrl = 'http://udigital.uberlandia.mg.gov.br/WsNFe2/LoteRps.jws?wsdl';
        }
    }

    protected function sendRequest($request, $soapUrl, $cnpj)
    {

        $soap = new Soap;

        $response = $soap->send($request, $soapUrl, $cnpj);

        return (string) $response;
    }

    public function envelopXML($xml, $method)
    {

        $xml = trim(preg_replace("/<\?xml.*?\?>/", "", $xml));

        $this->xml =
            '<proc:' . $method . ' soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
            <mensagemXml xsi:type="xsd:string"><![CDATA[' . $xml . ']]></mensagemXml>
            </proc:' . $method . '>';

        return $this->xml;
    }

    public function envelopSoapXML($xml)
    {
        $this->xml =
            '<soapenv:Envelope  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
                                xmlns:xsd="http://www.w3.org/2001/XMLSchema" 
                                xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" 
                                xmlns:proc="http://proces.wsnfe2.dsfnet.com.br">
                <soapenv:Header/>
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

    protected function getCodCidadeSIAFI($std)
    {

        if ($std->nfml_cmun == '3552205') {
            $codigoCidade = '7145';
            $std->CodigoMunicipioPrest = '7145';
        } elseif ($std->nfml_cmun == '3170206') {
            $codigoCidade = '5403';
            $std->CodigoMunicipioPrest = '5403';
        }

        return $codigoCidade;
    }

    protected function getCNPJ($xml)
    {

        $xml = simplexml_load_string($xml);

        return (string) $xml->Cabecalho->CPFCNPJRemetente;
    }

    protected function getRps($xml)
    {

        $make = new Make();

        $std = simplexml_load_string($xml);

        $cnpj = $this->getCNPJ($xml);

        $xml = $make->consultarRps($std);

        $servico = 'consultarSequencialRps';

        $request = $this->envelopXML($xml, $servico);

        $request = $this->envelopSoapXML($request);

        $response = $this->sendRequest($request, $this->soapUrl, $cnpj);

        $response = strip_tags($response);

        $response = htmlspecialchars_decode($response);

        $std = simplexml_load_string($response);

        $rps = (int) $std->Cabecalho->NroUltimoRps;

        $rps = $rps + 1;

        return (string) $rps;
    }

    public function recreateHash($xml, $rps)
    {
        $hash = new CreateHash();

        $std = simplexml_load_string($xml);

        $hash = $hash->createSignature(
            $std->Lote->RPS->InscricaoMunicipalPrestador,
            $std->Lote->RPS->SerieRPS,
            $rps,
            $std->Lote->RPS->DataEmissaoRPS,
            $std->Lote->RPS->Tributacao,
            $std->Lote->RPS->SituacaoRPS,
            $std->Lote->RPS->TipoRecolhimento,
            $std->Cabecalho->ValorTotalServicos,
            $std->Cabecalho->ValorTotalDeducoes,
            $std->Lote->RPS->CodigoAtividade,
            $std->Lote->RPS->CPFCNPJTomador
        );

        return $hash;
    }

    public function subsInXml($xml, $rps, $hash)
    {
        $std = simplexml_load_string($xml);

        $std->Lote->RPS->NumeroRPS = $rps;

        $std->Lote->RPS->Assinatura = $hash;

        $xml = $std->asXML();

        return $xml;
    }
}
