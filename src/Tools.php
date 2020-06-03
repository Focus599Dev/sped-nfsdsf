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

        $request = $this->envelopXML($xml, $servico);

        $request = $this->envelopSoapXML($request);

        $response = $this->sendRequest($request, $this->soapUrl);

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

        $response = $this->sendRequest($request, $this->soapUrl);

        $response = strip_tags($response);

        $response = htmlspecialchars_decode($response);

        return $response;
    }

    public function consultaSituacaoLoteRPS($std)
    {

        $make = new Make();

        $codigoCidade = $this->getCodCidadeSIAFI($std);

        $xml = $make->consulta($std, $codigoCidade);

        $xml = Strings::clearXmlString($xml);

        $servico = 'consultarLote';

        $request = $this->envelopXML($xml, $servico);

        $request = $this->envelopSoapXML($request);

        $response = $this->sendRequest($request, $this->soapUrl);

        $response = strip_tags($response);

        $response = htmlspecialchars_decode($response);

        return $response;
    }
}
