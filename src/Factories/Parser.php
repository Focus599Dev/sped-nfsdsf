<?php

namespace NFePHP\NFSe\DSF\Factories;

use NFePHP\NFSe\DSF\Make;
use stdClass;
use NFePHP\Common\Strings;
use App\Http\Model\Uteis;
use VARIANT;

class Parser
{

    protected $structure;

    protected $make;

    protected $loteRps;

    protected $tomador;

    protected $std;

    public function __construct($version = '3.0.1')
    {

        $ver = str_replace('.', '', $version);

        $path = realpath(__DIR__ . "/../../storage/txtstructure301.json");

        $this->std = new \stdClass();

        $this->lote = new \stdClass();

        $this->lote->tomador = new \stdClass();

        $this->lote->prestador = new \stdClass();

        $this->cabecalho = new \stdClass();

        $this->servico = new \stdClass();

        $this->structure = json_decode(file_get_contents($path), true);

        $this->version = $version;

        $this->make = new Make();
    }

    public function toXml($nota)
    {

        $this->array2xml($nota);

        if ($this->make->monta()) {

            return $this->make->getXML();
        }

        return null;
    }

    protected function array2xml($nota)
    {

        foreach ($nota as $lin) {

            $fields = explode('|', $lin);

            if (empty($fields)) {
                continue;
            }

            $metodo = strtolower(str_replace(' ', '', $fields[0])) . 'Entity';

            if (method_exists(__CLASS__, $metodo)) {

                $struct = $this->structure[strtoupper($fields[0])];

                $std = $this->fieldsToStd($fields, $struct);

                $this->$metodo($std);
            }
        }
    }

    protected function fieldsToStd($dfls, $struct)
    {

        $sfls = explode('|', $struct);

        $len = count($sfls) - 1;

        $std = new \stdClass();

        for ($i = 1; $i < $len; $i++) {

            $name = $sfls[$i];

            if (isset($dfls[$i]))
                $data = $dfls[$i];
            else
                $data = '';

            if (!empty($name)) {

                $std->$name = Strings::replaceSpecialsChars($data);
            }
        }

        return $std;
    }

    private function aEntity($std)
    {
        $this->loteRps = $std;
    }

    private function bEntity($std)
    {
        $this->loteRps = (object) array_merge((array) $this->loteRps, (array) $std);

        $this->lote = (object) array_merge((array) $this->lote, (array) $std);
    }

    private function cEntity($std)
    {
        if ($std->TelefonePrest) {

            $std->DDDPrestador = substr($std->TelefonePrest, 0, 2);
            $std->TelefonePrest = substr($std->TelefonePrest, 2);
        }

        $this->cabecalho = (object) array_merge((array) $this->cabecalho, (array) $std);

        $this->lote->prestador = (object) array_merge((array) $this->lote->prestador, (array) $std);
    }

    private function eEntity($std)
    {
        $std->DDDTomador = substr($std->Telefone, 0, 2);
        $std->Telefone = substr($std->Telefone, 2);

        $this->lote->tomador = (object) array_merge((array) $this->lote->tomador, (array) $std);
    }

    private function e02Entity($std)
    {

        $this->lote->tomador = (object) array_merge((array) $this->lote->tomador, (array) $std);
    }

    private function fEntity($std)
    {
        $this->lote = (object) array_merge((array) $this->lote, (array) $std);
    }

    private function hEntity($std)
    {

        $this->lote = (object) array_merge((array) $this->lote, (array) $std);
    }

    private function h01Entity($std)
    {

        $this->lote = (object) array_merge((array) $this->lote, (array) $std);
    }

    private function mEntity($std)
    {
        $this->cabecalho = (object) array_merge((array) $this->cabecalho, (array) $std);

        $this->lote = (object) array_merge((array) $this->lote, (array) $std);

        $this->servico = (object) array_merge((array) $this->servico, (array) $std);
    }

    private function nEntity($std)
    {

        $this->lote = (object) array_merge((array) $this->lote, (array) $std);

        $this->servico = (object) array_merge((array) $this->servico, (array) $std);

        $this->make->buildServico($this->servico);
    }

    private function wEntity($std)
    {
        $std->DataInit = substr($std->DataEmissao, 0, 10);

        $std->DataEnd = substr($std->DataEmissao, 0, 10);

        $this->cabecalho = (object) array_merge((array) $this->cabecalho, (array) $std);

        $this->make->buildCabec($this->cabecalho);

        $this->lote = (object) array_merge((array) $this->lote, (array) $std);

        $this->lote->assinatura = $this->createSignature();

        $this->make->buildLote($this->lote);
    }

    protected function createSignature()
    {
        $inscrMunicipal = str_pad($this->lote->prestador->InscricaoMunicipal, 11, "0", STR_PAD_LEFT);
        $serie = str_pad($this->lote->Serie, 5, " ");
        $rpsNum = str_pad($this->lote->RPSNum, 12, "0", STR_PAD_LEFT);
        $dtEmi = substr(preg_replace('/[^0-9]/', '', $this->lote->DataEmissao), 0, 8);
        $tributacao = str_pad($this->lote->Tributacao, 2, " ");
        $situacaoRPS = $this->lote->SituacaoRPS;

        if ($this->lote->TipoRecolhimento == 'A') {
            $tipoRec = 'N';
        } else {
            $tipoRec = 'S';
        }

        $resultado = str_pad(str_replace('.', '', $this->lote->ValorServicos - $this->lote->Deducao), 15, "0", STR_PAD_LEFT);
        $deducao = str_pad(preg_replace('/[^0-9]/', '', $this->lote->Deducao), 15, "0", STR_PAD_LEFT);
        $codigoCnae = str_pad($this->lote->CodigoCnae, 10, "0", STR_PAD_LEFT);
        $cnpj = str_pad($this->lote->tomador->Cnpj, 14, "0", STR_PAD_LEFT);

        $assinatura = $inscrMunicipal . $serie . $rpsNum . $dtEmi . $tributacao . $situacaoRPS . $tipoRec . $resultado . $deducao . $codigoCnae . $cnpj;

        $hash = sha1($assinatura);

        return $hash;
    }

    protected function getCodCidadeSIAFI($std)
    {

        if ($std->tomador->CodigoMunicipio == '3552205') {
            $codigoCidade = '7145';
            $this->std->SIAFI = '7145';
        } elseif ($std->tomador->CodigoMunicipio == '3170206') {
            $codigoCidade = '5403';
            $this->std->SIAFI = '5403';
        } elseif ($std->tomador->CodigoMunicipio == '3549409') {
            $codigoCidade = '7089';
            $this->std->SIAFI = '7089';
        }

        return $codigoCidade;
    }
}
