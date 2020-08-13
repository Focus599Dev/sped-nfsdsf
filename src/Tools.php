<?php

namespace NFePHP\NFSe\DSF;

use NFePHP\NFSe\DSF\Common\Tools as ToolsBase;
use NFePHP\Common\Strings;
use NFePHP\NFSe\DSF\Make;

class Tools extends ToolsBase
{
    public function enviaRPS($xml)
    {

        if (empty($xml)) {
            throw new InvalidArgumentException('$xml');
        }

        $xml = Strings::clearXmlString($xml);

        $servico = 'enviar';

        if ($this->config->tpAmb == '2') {
            $servico = 'testeEnviar';
        }

        $xsd = 'ReqEnvioLoteRPS.xsd';

        $this->isValid($xml, $xsd);

        $this->lastRequest = htmlspecialchars_decode($xml);

        $cnpj = $this->getCNPJ($xml);

        $request = $this->envelopXML($xml, $servico);

        $request = $this->envelopSoapXML($request);

        $response = $this->sendRequest($request, $this->soapUrl, $cnpj);

        $response = strip_tags($response);

        $response = htmlspecialchars_decode($response);

        return $response;
    }

    public function CancelaNfse($std)
    {

        $make = new Make();

        $xml = $make->cancelamento($std);

        $xml = Strings::clearXmlString($xml);

        $servico = 'cancelar';

        $request = $this->envelopXML($xml, $servico);

        $request = $this->envelopSoapXML($request);

        $cnpj = $std->cnpj;

        $response = $this->sendRequest($request, $this->soapUrl, $cnpj);

        $response = strip_tags($response);

        $response = htmlspecialchars_decode($response);

        $response = simplexml_load_string($response);

        return $response;
    }

    public function consultaSituacaoLoteRPS($codigoCidade, $nfml_cnpj_emit, $nfml_rps)
    {

        $make = new Make();

        $xml = $make->consulta($nfml_cnpj_emit, $codigoCidade, ltrim(substr($nfml_rps, 5)));

        $xml = Strings::clearXmlString($xml);

        $servico = 'consultarLote';

        $request = $this->envelopXML($xml, $servico);

        $request = $this->envelopSoapXML($request);

        $cnpj = $this->getCNPJ($xml);

        $response = $this->sendRequest($request, $this->soapUrl, $cnpj);

        $response = strip_tags($response);

        $response = htmlspecialchars_decode($response);

        $response = simplexml_load_string($response);

        return $response;
    }
}
