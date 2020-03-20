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

        // $servico = 'enviar';

        $servico = 'enviar';

        if ($this->config->tpAmb == '2') {
            $servico = 'testeEnviar';
        }

        $xsd = 'ReqEnvioLoteRPS.xsd';

        $this->isValid($xml, $xsd);

        $request = $this->envelopXML($xml, $servico);

        $this->lastRequest = htmlspecialchars_decode($request);

        $request = $this->envelopSoapXML($request);
        echo $request;
        $auxRequest = $this->sendRequest($request, $this->soapUrl);

        $auxRequest = htmlspecialchars_decode($auxRequest);
        var_dump('-');
        echo $auxRequest;
        return $auxRequest;
    }

    public function CancelaNfse($std)
    {

        $make = new Make();

        $xml = $make->cancelamento($std);

        $xml = Strings::clearXmlString($xml);

        $servico = 'cancelar';

        $request = $this->envelopXML($xml, $servico);

        $request = $this->envelopSoapXML($request);

        $this->lastResponse = $this->sendRequest($request, $this->soapUrl);

        $this->lastResponse = htmlspecialchars_decode($this->lastResponse);

        $this->lastResponse = $this->removeStuffs($this->lastResponse);

        $this->lastResponse = substr($this->lastResponse, strpos($this->lastResponse, '<Mensagem>') + 10);

        $this->lastResponse = substr($this->lastResponse, 0, strpos($this->lastResponse, '</Mensagem>'));

        $auxResp = $this->lastResponse;

        return $auxResp;
    }

    public function consultaSituacaoLoteRPS($std)
    {

        $make = new Make();

        $xml = $make->consultaLote($std);

        $xml = Strings::clearXmlString($xml);

        $servico = 'consultarNota';

        $request = $this->envelopXML($xml, $servico);

        $request = $this->envelopSoapXML($request);

        $this->lastResponse = $this->sendRequest($request, $this->soapUrl);

        $this->lastResponse = htmlspecialchars_decode($this->lastResponse);

        $this->lastResponse = $this->removeStuffs($this->lastResponse);

        $this->lastResponse = substr($this->lastResponse, strpos($this->lastResponse, '<Mensagem>') + 10);

        $this->lastResponse = substr($this->lastResponse, 0, strpos($this->lastResponse, '</Mensagem>'));

        $auxResp = simplexml_load_string($this->lastResponse);

        return $auxResp;
    }

    public function testeEnviaRPS($xml)
    {

        if (empty($xml)) {
            throw new InvalidArgumentException('$xml');
        }

        $xml = Strings::clearXmlString($xml);

        $servico = 'testeEnviar';

        $xsd = 'ReqEnvioLoteRPS.xsd';

        $this->isValid($xml, $xsd);

        $request = $this->envelopXML($xml, $servico);

        $request = $this->envelopSoapXML($request);

        $auxRequest = $this->sendRequest($request, $this->soapUrl);

        $auxRequest = htmlspecialchars_decode($auxRequest);

        // $auxRequest = $this->removeStuffs($auxRequest);

        return $auxRequest;
    }
}
