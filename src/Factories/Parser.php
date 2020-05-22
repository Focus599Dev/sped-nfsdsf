<?php

namespace NFePHP\NFSe\DSF\Factories;

use NFePHP\NFSe\DSF\Make;
use stdClass;
use NFePHP\Common\Strings;
use App\Http\Model\Uteis;

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

        $this->std->tomador = new \stdClass();

        $this->std->prestador = new \stdClass();

        $this->std->servico = array();

        $this->servicos = array();

        $this->structure = json_decode(file_get_contents($path), true);

        $this->version = $version;

        $this->make = new Make();
    }

    public function toXml($nota)
    {

        $std = $this->array2xml($nota);

        $this->fixDates();

        $this->fixPhoneNumbers();

        $this->getCodCidadeSIAFI($this->std);

        $hash = $this->createSignature();

        if ($this->make->getXML($this->std, $hash)) {

            return $this->make->getXML($this->std, $hash);
        }

        return null;
    }

    protected function array2xml($nota)
    {

        $obj = [];

        foreach ($nota as $lin) {

            $fields = explode('|', $lin);

            $struct = $this->structure[strtoupper($fields[0])];

            $std = $this->fieldsToStd($fields, $struct);

            $obj = (object) array_merge((array) $obj, (array) $std);
        }

        return $obj;
    }

    protected function fieldsToStd($dfls, $struct)
    {

        $sfls = explode('|', $struct);

        $len = count($sfls) - 1;

        for ($i = 1; $i < $len; $i++) {

            $name = $sfls[$i];

            if (isset($dfls[$i]))
                $data = $dfls[$i];
            else
                $data = '';

            if (!empty($name)) {

                if ($dfls[0] == 'C') {

                    $this->std->prestador->$name = Strings::replaceSpecialsChars($data);
                } elseif ($dfls[0] == 'E' || $dfls[0] == 'E02') {

                    $this->std->tomador->$name = Strings::replaceSpecialsChars($data);
                } else {

                    $this->std->$name = Strings::replaceSpecialsChars($data);
                }
            }
        }

        if ($dfls[0] == 'N') {

            $this->servicos[] = $dfls;

            $this->std->servico = $this->servicos;
        }

        return $this->std;
    }

    protected function createSignature()
    {
        $inscrMunicipal = str_pad($this->std->prestador->InscricaoMunicipal, 11, "0", STR_PAD_LEFT);
        $serie = str_pad($this->std->Serie, 5, " ");
        $rpsNum = str_pad($this->std->RPSNum, 12, "0", STR_PAD_LEFT);
        $dtEmi = substr(preg_replace('/[^0-9]/', '', $this->std->DataEmissao), 0, 8);
        $tributacao = str_pad($this->std->Tributacao, 2, " ");
        $situacaoRPS = $this->std->SituacaoRPS;

        if ($this->std->TipoRecolhimento == 'A') {
            $tipoRec = 'N';
        } else {
            $tipoRec = 'S';
        }

        $resultado = str_pad(str_replace('.', '', $this->std->ValorServicos - $this->std->Deducao), 15, "0", STR_PAD_LEFT);
        $deducao = str_pad(preg_replace('/[^0-9]/', '', $this->std->Deducao), 15, "0", STR_PAD_LEFT);
        $codigoCnae = str_pad($this->std->CodigoCnae, 10, "0", STR_PAD_LEFT);
        $cnpj = str_pad($this->std->tomador->Cnpj, 14, "0", STR_PAD_LEFT);

        $assinatura = $inscrMunicipal . $serie . $rpsNum . $dtEmi . $tributacao . $situacaoRPS . $tipoRec . $resultado . $deducao . $codigoCnae . $cnpj;

        $hash = sha1($assinatura);

        return $hash;
    }

    protected function fixDates()
    {

        $this->std->DataInit = substr($this->std->DataEmissao, 0, 10);
        $this->std->DataEnd = substr($this->std->DataEmissao, 0, 10);
    }

    protected function fixPhoneNumbers()
    {
        $this->std->tomador->DDDTomador = substr($this->std->tomador->Telefone, 0, 2);
        $this->std->tomador->Telefone = substr($this->std->tomador->Telefone, 2);

        if ($this->std->prestador->TelefonePrest) {

            $this->std->prestador->DDDPrestador = substr($this->std->prestador->TelefonePrest, 0, 2);
            $this->std->prestador->TelefonePrest = substr($this->std->prestador->TelefonePrest, 2);
        }
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
