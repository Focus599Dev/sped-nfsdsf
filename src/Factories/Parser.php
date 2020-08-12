<?php

namespace NFePHP\NFSe\DSF\Factories;

use NFePHP\NFSe\DSF\Make;
use NFePHP\NFSe\DSF\Factories\CreateHash;
use stdClass;
use NFePHP\Common\Strings;
use App\Http\Model\Uteis;
use VARIANT;

class Parser
{

    protected $structure;

    protected $make;

    protected $hash;

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

        $this->hash = new CreateHash();
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

        $this->make->buildLoteNum($this->loteRps);

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

        $hash = $this->hash->createSignature(
            $this->lote->prestador->InscricaoMunicipal,
            $this->lote->Serie,
            $this->lote->RPSNum,
            $this->lote->DataEmissao,
            $this->lote->Tributacao,
            $this->lote->SituacaoRPS,
            $this->lote->TipoRecolhimento,
            $this->lote->ValorServicos,
            $this->lote->Deducao,
            $this->lote->CodigoCnae,
            $this->lote->tomador->Cnpj
        );

        $this->lote->assinatura = $hash;

        $this->make->buildLote($this->lote);
    }
}
