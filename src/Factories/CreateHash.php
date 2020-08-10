<?php

namespace NFePHP\NFSe\DSF\Factories;

class CreateHash
{
    public function createSignature($inscrMunicipal, $serie, $rpsNum, $dtEmi, $tributacao, $situacaoRPS, $tipoRec, $valorServicos, $valorDeducao, $codigoCnae, $cnpj)
    {

        $inscrMunicipal = str_pad($inscrMunicipal, 11, "0", STR_PAD_LEFT);
        $serie = str_pad($serie, 5, " ");
        $rpsNum = str_pad($rpsNum, 12, "0", STR_PAD_LEFT);
        $dtEmi = substr(preg_replace('/[^0-9]/', '', $dtEmi), 0, 8);
        $tributacao = str_pad($tributacao, 2, " ");
        $situacaoRPS = $situacaoRPS;

        if ($tipoRec == 'A') {
            $tipoRec = 'N';
        } else {
            $tipoRec = 'S';
        }

        $resultado = str_pad(str_replace('.', '', $valorServicos - $valorDeducao), 15, "0", STR_PAD_LEFT);
        $deducao = str_pad(preg_replace('/[^0-9]/', '', $valorDeducao), 15, "0", STR_PAD_LEFT);
        $codigoCnae = str_pad($codigoCnae, 10, "0", STR_PAD_LEFT);
        $cnpj = str_pad($cnpj, 14, "0", STR_PAD_LEFT);

        $assinatura = $inscrMunicipal . $serie . $rpsNum . $dtEmi . $tributacao . $situacaoRPS . $tipoRec . $resultado . $deducao . $codigoCnae . $cnpj;

        $hash = sha1($assinatura);

        return $hash;
    }
}
