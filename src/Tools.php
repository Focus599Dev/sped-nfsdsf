<?php

namespace NFePHP\NFSe\DSF;

use NFePHP\NFSe\DSF\Common\Tools as ToolsBase;
use NFePHP\Common\Strings;

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

            $rps = $this->getRps($xml);

            $hash = $this->recreateHash($xml, $rps);

            $xml = $this->subsInXml($xml, $rps, $hash);
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

    public function consultaSituacaoLoteRPS($CodCidade, $CPFCNPJRemetente, $Lote)
    {

        $make = new Make();

        $Lote = preg_replace('/[^0-9]/', '', $Lote);

        $xml = $make->consulta($CodCidade, $CPFCNPJRemetente, $Lote);

        $xml = Strings::clearXmlString($xml);

        $servico = 'consultarLote';

        $request = $this->envelopXML($xml, $servico);

        $request = $this->envelopSoapXML($request);

        $response = $this->sendRequest($request, $this->soapUrl, $CPFCNPJRemetente);

        $response = strip_tags($response);

        $response = htmlspecialchars_decode($response);

        return $response;
    }
}
