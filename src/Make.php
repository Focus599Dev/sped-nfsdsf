<?php

namespace NFePHP\NFSe\DSF;

use NFePHP\Common\DOMImproved as Dom;

class Make
{

    public $dom;

    public $xml;

    public function __construct()
    {

        $this->dom = new Dom();

        $this->dom->preserveWhiteSpace = false;

        $this->dom->formatOutput = false;
    }

    public function getXML($std, $hash)
    {

        if (empty($this->xml)) {

            $this->gerarNota($std, $hash);
        }

        return $this->xml;
    }

    public function gerarNota($std, $hash)
    {
        $req = $this->dom->createElement('ns1:ReqEnvioLoteRPS');
        $req->setAttribute('xmlns:ns1', 'http://localhost:8080/WsNFe2/lote');
        $req->setAttribute('xsi:schemaLocation', 'http://localhost:8080/WsNFe2/lote http://localhost:8080/WsNFe2/xsd/ReqEnvioLoteRPS.xsd');
        $req->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $this->dom->appendChild($req);

        $cabecalho = $this->dom->createElement('Cabecalho');
        $req->appendChild($cabecalho);

        $this->dom->addChild(
            $cabecalho,                                     // pai
            "CodCidade",                                    // nome
            $std->CodigoMunicipioPrest,                                         // valor
            true,                                           // se é obrigatorio
            "Código da cidade da declaração padrão SIAFI."  // descrição se der catch
        );

        $this->dom->addChild(
            $cabecalho,
            "CPFCNPJRemetente",
            $std->prestador->Cnpj,
            true,
            "CPF /CNPJ do remetente autorizado a transmitir o RPS"
        );

        $this->dom->addChild(
            $cabecalho,
            "RazaoSocialRemetente",
            $std->prestador->RazaoSocial,
            true,
            "Razão Social do Remetente"
        );

        $this->dom->addChild(
            $cabecalho,
            "transacao",
            "true",
            true,
            "true - Se os RPS fazem parte de uma mesma transação."
        );

        $this->dom->addChild(
            $cabecalho,
            "dtInicio",
            $std->DataInit,
            true,
            "Data de início do período transmitido. Data do primeiro RPS contido no lote Formato: YYYY-MM-DD"
        );

        $this->dom->addChild(
            $cabecalho,
            "dtFim",
            $std->DataEnd,
            true,
            "Data Final do período transmitido. Data do último RPS contida no lote Formato: YYYY-MM-DD"
        );

        $this->dom->addChild(
            $cabecalho,
            "QtdRPS",
            '1',
            true,
            "Quantidade de RPS contidos na remessa"
        );

        $this->dom->addChild(
            $cabecalho,
            "ValorTotalServicos",
            $std->ValorServicos,
            true,
            "Valor total dos Serviços prestados nos RPS"
        );

        $this->dom->addChild(
            $cabecalho,
            "ValorTotalDeducoes",
            $std->Deducao,
            true,
            "Valor total das deduções nos RPS"
        );

        $this->dom->addChild(
            $cabecalho,
            "Versao",
            '1',
            true,
            "Informe a versão do Schema XML. Padrão “1”"
        );

        $this->dom->addChild(
            $cabecalho,
            "MetodoEnvio",
            'WS',
            true,
            "Padrão “WS”"
        );

        $lote = $this->dom->createElement('Lote');
        $lote->setAttribute('Id', 'lote:1ABCDZ');
        $req->appendChild($lote);

        $rps = $this->dom->createElement('RPS');
        $rps->setAttribute('Id', 'rps:1');
        $lote->appendChild($rps);

        $this->dom->addChild(
            $rps,
            "Assinatura",
            $hash,
            true,
            "Código hash de validação do conteúdo"
        );

        $this->dom->addChild(
            $rps,
            "InscricaoMunicipalPrestador",
            str_pad($std->prestador->InscricaoMunicipal, 9, "0", STR_PAD_LEFT),
            true,
            "Inscrição Municipal do Prestador"
        );

        $this->dom->addChild(
            $rps,
            "RazaoSocialPrestador",
            $std->prestador->RazaoSocial,
            true,
            "Razão Social do Prestador"
        );

        $this->dom->addChild(
            $rps,
            "TipoRPS",
            'RPS',
            true,
            "Tipo de RPS, padrão “RPS”"
        );

        $this->dom->addChild(
            $rps,
            "SerieRPS",
            $std->Serie,
            true,
            "Série do RPS - Padrão “NF”"
        );

        $this->dom->addChild(
            $rps,
            "NumeroRPS",
            $std->RPSNum,
            true,
            "Número da RPS"
        );

        $this->dom->addChild(
            $rps,
            "DataEmissaoRPS",
            $std->DataEmissao,
            true,
            "Data e Hora de Emissão Formato: AAAA-MM-DDTHH:MM:SS"
        );

        $this->dom->addChild(
            $rps,
            "SituacaoRPS",
            $std->SituacaoRPS,
            true,
            "Situação da RPS “N”-Normal “C”-Cancelada"
        );

        $this->dom->addChild(
            $rps,
            "SerieRPSSubstituido",
            $std->SerieRPSSubstituido,
            false,
            "Série do RPS a ser substituído – Padrão “NF”. Se não for substituto não preencher"
        );

        $this->dom->addChild(
            $rps,
            "NumeroRPSSubstituido",
            $std->NumeroRPSSubstituido,
            false,
            "Número da NFSe Substituida Se não for subtituto não preencher"
        );

        $this->dom->addChild(
            $rps,
            "NumeroNFSeSubstituida",
            $std->NumeroNFSeSubstituida,
            false,
            "Número do RPS a ser substituído. Se não for substituto não preencher"
        );

        $this->dom->addChild(
            $rps,
            "DataEmissaoNFSeSubstituida",
            "1900-01-01",
            false,
            "Data de emissão da NFSe Formato= AAAA-MM-DD. Se não for substituto preencher com “01/01/1900”"
        );

        $this->dom->addChild(
            $rps,
            "SeriePrestacao",
            '99',
            true,
            "Número do equipamento emissor do RPS ou série de prestação."
        );

        $this->dom->addChild(
            $rps,
            "InscricaoMunicipalTomador",
            $std->tomador->InscricaoMunicipal,
            true,
            "Inscrição Municipal do Tomador. Caso o tomador não for do municipio não preencher,"
        );

        $this->dom->addChild(
            $rps,
            "CPFCNPJTomador",
            $std->tomador->Cnpj,
            true,
            "CPF ou CNPJ do Tomador"
        );

        $this->dom->addChild(
            $rps,
            "RazaoSocialTomador",
            $std->tomador->RazaoSocial,
            true,
            "Razão Social do Tomador"
        );

        $this->dom->addChild(
            $rps,
            "DocTomadorEstrangeiro",
            $std->tomador->DocTomadorEstrangeiro,
            false,
            "Documento de Identificação de Tomador Estrangeiro"
        );

        $this->dom->addChild(
            $rps,
            "TipoLogradouroTomador",
            $std->tomador->Prefixo,
            true,
            "Tipo de Logradouro do Tomador."
        );

        $this->dom->addChild(
            $rps,
            "LogradouroTomador",
            $std->tomador->Endereco,
            true,
            "Logradouro do Tomador"
        );

        $this->dom->addChild(
            $rps,
            "NumeroEnderecoTomador",
            $std->tomador->Numero,
            true,
            "Numero de Endereço do Tomado"
        );

        $this->dom->addChild(
            $rps,
            "ComplementoEnderecoTomador",
            $std->tomador->Complemento,
            false,
            "Complemento do Endereço do Tomador"
        );

        $this->dom->addChild(
            $rps,
            "TipoBairroTomador",
            $std->tomador->TipoBairro,
            true,
            "Tipo de Bairro do Tomador"
        );

        $this->dom->addChild(
            $rps,
            "BairroTomador",
            $std->tomador->Bairro,
            true,
            "Bairro do Tomador"
        );

        $this->dom->addChild(
            $rps,
            "CidadeTomador",
            $std->tomador->CodigoMunicipio,
            true,
            "Código da Cidade do Tomador padrão SIAFI"
        );

        $this->dom->addChild(
            $rps,
            "CidadeTomadorDescricao",
            $std->tomador->DescMunicipio,
            true,
            "Nome da Cidade do Tomador"
        );

        $this->dom->addChild(
            $rps,
            "CEPTomador",
            $std->tomador->Cep,
            true,
            "CEP do Tomador"
        );

        $this->dom->addChild(
            $rps,
            "EmailTomador",
            $std->tomador->Email,
            true,
            "Email do Tomador. Caso o Tomador não possua email informar o valor “-”. "
        );

        $this->dom->addChild(
            $rps,
            "CodigoAtividade",
            $std->CodigoCnae,
            true,
            "Código da Atividade da RPS"
        );

        $this->dom->addChild(
            $rps,
            "AliquotaAtividade",
            $std->Aliquota,
            true,
            "Alíquota de ISS da Atividade"
        );

        $this->dom->addChild(
            $rps,
            "TipoRecolhimento",
            $std->TipoRecolhimento,
            true,
            "Tipo de Recolhimento “A” – A Receber “R” - Retido na Fonte"
        );

        $this->dom->addChild(
            $rps,
            "MunicipioPrestacao",
            $std->CodigoMunicipioPrest,
            true,
            "Código do Município de Prestação – Padrão SIAFI"
        );

        $this->dom->addChild(
            $rps,
            "MunicipioPrestacaoDescricao",
            $std->DescMunicipioPrest,
            true,
            "Município de Prestação do Serviço"
        );

        $this->dom->addChild(
            $rps,
            "Operacao",
            $std->Operacao,
            true,
            "
                “A”-Sem Dedução
                “B”-Com Dedução/Materiais
                “C” - Imune/Isenta de ISSQN
                “D” - Devolução/Simples Remessa
                “J” - Intemediação
            "
        );

        $this->dom->addChild(
            $rps,
            "Tributacao",
            $std->Tributacao,
            true,
            "
                C - Isenta de ISS
                E - Não Incidência no Município
                F - Imune
                K - Exigibilidd Susp.Dec.J/Proc.A
                N - Não Tributável
                T – Tributável
                G - Tributável Fixo
                H - Tributável S.N.
                M - Micro Empreendedor Individual (MEI)
            "
        );

        $this->dom->addChild(
            $rps,
            "ValorPIS",
            $std->ValorPis,
            true,
            "Valor PIS"
        );

        $this->dom->addChild(
            $rps,
            "ValorCOFINS",
            $std->ValorCofins,
            true,
            "Valor COFINS"
        );

        $this->dom->addChild(
            $rps,
            "ValorINSS",
            $std->ValorInss,
            true,
            "Valor do INSS"
        );

        $this->dom->addChild(
            $rps,
            "ValorIR",
            $std->ValorIr,
            true,
            "Valor do IR"
        );

        $this->dom->addChild(
            $rps,
            "ValorCSLL",
            $std->ValorCsll,
            true,
            "Valor do CSLL"
        );

        $this->dom->addChild(
            $rps,
            "AliquotaPIS",
            $std->AliquotaPIS,
            true,
            "Alíquota PIS"
        );

        $this->dom->addChild(
            $rps,
            "AliquotaCOFINS",
            $std->AliquotaCOFINS,
            true,
            "Alíquota COFINS"
        );

        $this->dom->addChild(
            $rps,
            "AliquotaINSS",
            $std->AliquotaINSS,
            true,
            "Alíquota INSS"
        );

        $this->dom->addChild(
            $rps,
            "AliquotaIR",
            $std->AliquotaIR,
            true,
            "Alíquota IR"
        );

        $this->dom->addChild(
            $rps,
            "AliquotaCSLL",
            $std->AliquotaCSLL,
            true,
            "Alíquota CSLL"
        );

        $this->dom->addChild(
            $rps,
            "DescricaoRPS",
            $std->Observacao,
            true,
            "Descrição/Dados Complementares do RPS"
        );

        $this->dom->addChild(
            $rps,
            "DDDPrestador",
            $std->prestador->TelefonePrest,
            true,
            "DDD Telefone do Prestador"
        );

        $this->dom->addChild(
            $rps,
            "TelefonePrestador",
            $std->prestador->TelefonePrest,
            true,
            "Telefone do Prestador"
        );

        $this->dom->addChild(
            $rps,
            "DDDTomador",
            $std->tomador->DDDTomador,
            true,
            "DDD do telefone do tomador"
        );

        $this->dom->addChild(
            $rps,
            "TelefoneTomador",
            $std->tomador->Telefone,
            false,
            "Telefone do Tomador"
        );

        $this->dom->addChild(
            $rps,
            "MotCancelamento",
            $std->Observacao,
            false,
            "Motivo do Cancelamento"
        );

        $this->dom->addChild(
            $rps,
            "CpfCnpjIntermediario",
            "",
            false,
            "CPF/CNPJ Intemediário"
        );

        $itens = $this->dom->createElement('Itens');
        $rps->appendChild($itens);

        foreach ($std->servico as $key) {

            $item = $this->dom->createElement('Item');
            $itens->appendChild($item);

            $this->dom->addChild(
                $item,
                "DiscriminacaoServico",
                $std->Discriminacao,
                true,
                "Discriminação do Serviço"
            );

            $this->dom->addChild(
                $item,
                "Quantidade",
                $std->Quantidade,
                true,
                "Quantidade do serviço tomado"
            );

            $this->dom->addChild(
                $item,
                "ValorUnitario",
                $std->ValorUnit,
                true,
                "Valor Unitário"
            );

            $this->dom->addChild(
                $item,
                "ValorTotal",
                $std->ValorServicos,
                true,
                "Valor total do serviço"
            );

            $this->dom->addChild(
                $item,
                "Tributavel",
                $std->Tributavel,
                true,
                "Tributável S-Item tributável, N-Não tributável."
            );
        }

        $this->xml = $this->dom->saveXML();

        return $this->xml;
    }

    public function cancelamento($std)
    {

        $req = $this->dom->createElement('ns1:ReqEnvioLoteRPS');
        $req->setAttribute('xmlns:ns1', 'http://localhost:8080/WsNFe2/lote');
        $req->setAttribute('xsi:schemaLocation', 'http://localhost:8080/WsNFe2/lote http://localhost:8080/WsNFe2/xsd/ReqEnvioLoteRPS.xsd');
        $req->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $this->dom->appendChild($req);

        $cabecalho = $this->dom->createElement('Cabecalho');
        $req->appendChild($cabecalho);

        $this->dom->addChild(
            $cabecalho,
            "CodCidade",
            $std->CodigoMunicipioPrest,
            true,
            "Código da cidade da declaração padrão SIAFI."
        );

        $this->dom->addChild(
            $cabecalho,
            "CPFCNPJRemetente",
            $std->prestador->Cnpj,
            true,
            "CPF /CNPJ do remetente autorizado a transmitir o RPS"
        );

        $this->dom->addChild(
            $cabecalho,
            "transacao",
            "true",
            true,
            "true - Se os RPS fazem parte de uma mesma transação."
        );

        $this->dom->addChild(
            $cabecalho,
            "Versao",
            '1',
            true,
            "Informe a versão do Schema XML. Padrão “1”"
        );

        $lote = $this->dom->createElement('Lote');
        $lote->setAttribute('Id', 'lote:1ABCDZ');
        $req->appendChild($lote);

        $nota = $this->dom->createElement('Nota');
        $nota->setAttribute('Nota', 'id:1');
        $lote->appendChild($nota);

        $this->dom->addChild(
            $lote,
            "InscricaoMunicipalPrestador",
            str_pad($std->prestador->InscricaoMunicipal, 9, "0", STR_PAD_LEFT),
            true,
            "Inscrição Municipal do Prestador"
        );

        $this->dom->addChild(
            $lote,
            "NumeroNota",
            str_pad($std->prestador->InscricaoMunicipal, 9, "0", STR_PAD_LEFT),
            true,
            "Inscrição Municipal do Prestador"
        );

        $this->dom->addChild(
            $lote,
            "CodigoVerificacao",
            str_pad($std->prestador->InscricaoMunicipal, 9, "0", STR_PAD_LEFT),
            true,
            "Inscrição Municipal do Prestador"
        );

        $this->dom->addChild(
            $lote,
            "MotivoCancelamento",
            str_pad($std->prestador->InscricaoMunicipal, 9, "0", STR_PAD_LEFT),
            true,
            "Inscrição Municipal do Prestador"
        );

        $this->xml = $this->dom->saveXML();

        return $this->xml;
    }

    public function consulta($std)
    {

        $req = $this->dom->createElement('ns1:ReqEnvioLoteRPS');
        $req->setAttribute('xmlns:ns1', 'http://localhost:8080/WsNFe2/lote');
        $req->setAttribute('xsi:schemaLocation', 'http://localhost:8080/WsNFe2/lote http://localhost:8080/WsNFe2/xsd/ReqEnvioLoteRPS.xsd');
        $req->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $this->dom->appendChild($req);

        $cabecalho = $this->dom->createElement('Cabecalho');
        $req->appendChild($cabecalho);

        $this->dom->addChild(
            $cabecalho,
            "CodCidade",
            $std->CodigoMunicipioPrest,
            true,
            "Código da cidade da declaração padrão SIAFI."
        );

        $this->dom->addChild(
            $cabecalho,
            "CPFCNPJRemetente",
            $std->prestador->Cnpj,
            true,
            "CPF /CNPJ do remetente autorizado a transmitir o RPS"
        );

        $this->dom->addChild(
            $cabecalho,
            "Versao",
            '1',
            true,
            "Informe a versão do Schema XML. Padrão “1”"
        );

        $this->dom->addChild(
            $lote,
            "NumeroLote",
            str_pad($std->prestador->InscricaoMunicipal, 9, "0", STR_PAD_LEFT),
            true,
            "Numero do lote a ser consultado"
        );

        $this->xml = $this->dom->saveXML();

        return $this->xml;
    }
}
