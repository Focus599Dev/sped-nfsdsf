<?php

namespace NFePHP\NFSe\DSF;

use NFePHP\Common\DOMImproved as Dom;
use stdClass;

class Make
{

    public $dom;

    public $xml;

    public function __construct()
    {

        $this->dom = new Dom();

        $this->dom->preserveWhiteSpace = false;

        $this->dom->formatOutput = false;

        $this->cabecalho = $this->dom->createElement('Cabecalho');

        $this->lote = $this->dom->createElement('Lote');

        $this->rps = $this->dom->createElement('RPS');

        $this->itens = $this->dom->createElement('Itens');
    }

    public function getXML()
    {
        if (empty($this->xml)) {

            $this->monta();
        }

        return $this->xml;
    }

    public function monta()
    {

        $req = $this->dom->createElement('ns1:ReqEnvioLoteRPS');
        $req->setAttribute('xmlns:ns1', 'http://localhost:8080/WsNFe2/lote');
        $req->setAttribute('xsi:schemaLocation', 'http://localhost:8080/WsNFe2/lote http://localhost:8080/WsNFe2/xsd/ReqEnvioLoteRPS.xsd');
        $req->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $this->dom->appendChild($req);

        $req->appendChild($this->cabecalho);

        $req->appendChild($this->lote);

        $this->lote->appendChild($this->rps);

        $this->rps->appendChild($this->itens);

        $this->xml = $this->dom->saveXML();

        return $this->xml;
    }

    public function buildLoteNum($std)
    {

        $this->lote->setAttribute('Id', 'lote:' . $std->NumeroLote);
    }

    public function buildCabec($std)
    {

        $this->dom->addChild(
            $this->cabecalho,
            "CodCidade",
            '7145',
            true,
            "Numero de QuantidadeRps"
        );

        $this->dom->addChild(
            $this->cabecalho,
            "CPFCNPJRemetente",
            $std->Cnpj,
            true,
            "Numero de QuantidadeRps"
        );

        $this->dom->addChild(
            $this->cabecalho,
            "RazaoSocialRemetente",
            $std->RazaoSocial,
            true,
            "Numero de QuantidadeRps"
        );

        $this->dom->addChild(
            $this->cabecalho,
            "transacao",
            "true",
            true,
            "true - Se os RPS fazem parte de uma mesma transação."
        );

        $this->dom->addChild(
            $this->cabecalho,
            "dtInicio",
            $std->DataInit,
            true,
            "Numero de QuantidadeRps"
        );

        $this->dom->addChild(
            $this->cabecalho,
            "dtFim",
            $std->DataEnd,
            true,
            "Numero de QuantidadeRps"
        );

        $this->dom->addChild(
            $this->cabecalho,
            "QtdRPS",
            '1',
            true,
            "Quantidade de RPS contidos na remessa"
        );

        $this->dom->addChild(
            $this->cabecalho,
            "ValorTotalServicos",
            $std->ValorServicos,
            true,
            "Numero de QuantidadeRps"
        );

        $this->dom->addChild(
            $this->cabecalho,
            "ValorTotalDeducoes",
            $std->ValorDeducoes,
            true,
            "Quantidade de RPS contidos na remessa"
        );

        $this->dom->addChild(
            $this->cabecalho,
            "Versao",
            '1',
            true,
            "Informe a versão do Schema XML. Padrão “1”"
        );

        $this->dom->addChild(
            $this->cabecalho,
            "MetodoEnvio",
            'WS',
            true,
            "Padrão “WS”"
        );
    }

    public function buildLote($std)
    {

        $this->rps->setAttribute('Id', 'rps:' . $std->RPSNum);

        $this->dom->addChild(
            $this->rps,
            "Assinatura",
            $std->assinatura,
            true,
            "Código hash de validação do conteúdo"
        );

        $this->dom->addChild(
            $this->rps,
            "InscricaoMunicipalPrestador",
            str_pad($std->prestador->InscricaoMunicipal, 9, "0", STR_PAD_LEFT),
            true,
            "Inscrição Municipal do Prestador"
        );

        $this->dom->addChild(
            $this->rps,
            "RazaoSocialPrestador",
            $std->prestador->RazaoSocial,
            true,
            "Razão Social do Prestador"
        );

        $this->dom->addChild(
            $this->rps,
            "TipoRPS",
            'RPS',
            true,
            "Tipo de RPS, padrão “RPS”"
        );

        $this->dom->addChild(
            $this->rps,
            "SerieRPS",
            $std->Serie,
            true,
            "Série do RPS - Padrão “NF”"
        );

        $this->dom->addChild(
            $this->rps,
            "NumeroRPS",
            $std->RPSNum,
            true,
            "Número da RPS"
        );

        $this->dom->addChild(
            $this->rps,
            "DataEmissaoRPS",
            $std->DataEmissao,
            true,
            "Data e Hora de Emissão Formato: AAAA-MM-DDTHH:MM:SS"
        );

        $this->dom->addChild(
            $this->rps,
            "SituacaoRPS",
            $std->SituacaoRPS,
            true,
            "Situação da RPS “N”-Normal “C”-Cancelada"
        );

        $this->dom->addChild(
            $this->rps,
            "SerieRPSSubstituido",
            $std->SerieRPSSubstituido,
            false,
            "Série do RPS a ser substituído – Padrão “NF”. Se não for substituto não preencher"
        );

        $this->dom->addChild(
            $this->rps,
            "NumeroRPSSubstituido",
            $std->NumeroRPSSubstituido,
            false,
            "Número da NFSe Substituida Se não for subtituto não preencher"
        );

        $this->dom->addChild(
            $this->rps,
            "NumeroNFSeSubstituida",
            $std->NumeroNFSeSubstituida,
            false,
            "Número do RPS a ser substituído. Se não for substituto não preencher"
        );

        $this->dom->addChild(
            $this->rps,
            "DataEmissaoNFSeSubstituida",
            "1900-01-01",
            false,
            "Data de emissão da NFSe Formato= AAAA-MM-DD. Se não for substituto preencher com “01/01/1900”"
        );

        $this->dom->addChild(
            $this->rps,
            "SeriePrestacao",
            '99',
            true,
            "Número do equipamento emissor do RPS ou série de prestação."
        );

        $this->dom->addChild(
            $this->rps,
            "InscricaoMunicipalTomador",
            $std->tomador->InscricaoMunicipal,
            true,
            "Inscrição Municipal do Tomador. Caso o tomador não for do municipio não preencher,"
        );

        $this->dom->addChild(
            $this->rps,
            "CPFCNPJTomador",
            $std->tomador->Cnpj,
            true,
            "CPF ou CNPJ do Tomador"
        );

        $this->dom->addChild(
            $this->rps,
            "RazaoSocialTomador",
            $std->tomador->RazaoSocial,
            true,
            "Razão Social do Tomador"
        );

        $this->dom->addChild(
            $this->rps,
            "DocTomadorEstrangeiro",
            $std->tomador->DocTomadorEstrangeiro,
            false,
            "Documento de Identificação de Tomador Estrangeiro"
        );

        $this->dom->addChild(
            $this->rps,
            "TipoLogradouroTomador",
            $std->tomador->Prefixo,
            true,
            "Tipo de Logradouro do Tomador."
        );

        $this->dom->addChild(
            $this->rps,
            "LogradouroTomador",
            $std->tomador->Endereco,
            true,
            "Logradouro do Tomador"
        );

        $this->dom->addChild(
            $this->rps,
            "NumeroEnderecoTomador",
            $std->tomador->Numero,
            true,
            "Numero de Endereço do Tomado"
        );

        $this->dom->addChild(
            $this->rps,
            "ComplementoEnderecoTomador",
            $std->tomador->Complemento,
            false,
            "Complemento do Endereço do Tomador"
        );

        $this->dom->addChild(
            $this->rps,
            "TipoBairroTomador",
            $std->tomador->TipoBairro,
            true,
            "Tipo de Bairro do Tomador"
        );

        $this->dom->addChild(
            $this->rps,
            "BairroTomador",
            $std->tomador->Bairro,
            true,
            "Bairro do Tomador"
        );

        $this->dom->addChild(
            $this->rps,
            "CidadeTomador",
            $std->tomador->CodigoSIAFITomador,
            true,
            "Código da Cidade do Tomador padrão SIAFI"
        );

        $this->dom->addChild(
            $this->rps,
            "CidadeTomadorDescricao",
            $std->tomador->DescMunicipio,
            true,
            "Nome da Cidade do Tomador"
        );

        $this->dom->addChild(
            $this->rps,
            "CEPTomador",
            $std->tomador->Cep,
            true,
            "CEP do Tomador"
        );

        $this->dom->addChild(
            $this->rps,
            "EmailTomador",
            $std->tomador->Email,
            true,
            "Email do Tomador. Caso o Tomador não possua email informar o valor “-”. "
        );

        $this->dom->addChild(
            $this->rps,
            "CodigoAtividade",
            $std->CodigoCnae,
            true,
            "Código da Atividade da RPS"
        );

        $this->dom->addChild(
            $this->rps,
            "AliquotaAtividade",
            $std->Aliquota,
            true,
            "Alíquota de ISS da Atividade"
        );

        $this->dom->addChild(
            $this->rps,
            "TipoRecolhimento",
            $std->TipoRecolhimento,
            true,
            "Tipo de Recolhimento “A” – A Receber “R” - Retido na Fonte"
        );

        $this->dom->addChild(
            $this->rps,
            "MunicipioPrestacao",
            $std->CodigoMunicipioPrest,
            true,
            "Código do Município de Prestação – Padrão SIAFI"
        );

        $this->dom->addChild(
            $this->rps,
            "MunicipioPrestacaoDescricao",
            $std->DescMunicipioPrest,
            true,
            "Município de Prestação do Serviço"
        );

        $this->dom->addChild(
            $this->rps,
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
            $this->rps,
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
            $this->rps,
            "ValorPIS",
            $std->ValorPis,
            true,
            "Valor PIS"
        );

        $this->dom->addChild(
            $this->rps,
            "ValorCOFINS",
            $std->ValorCofins,
            true,
            "Valor COFINS"
        );

        $this->dom->addChild(
            $this->rps,
            "ValorINSS",
            $std->ValorInss,
            true,
            "Valor do INSS"
        );

        $this->dom->addChild(
            $this->rps,
            "ValorIR",
            $std->ValorIr,
            true,
            "Valor do IR"
        );

        $this->dom->addChild(
            $this->rps,
            "ValorCSLL",
            $std->ValorCsll,
            true,
            "Valor do CSLL"
        );

        $this->dom->addChild(
            $this->rps,
            "AliquotaPIS",
            $std->AliquotaPIS,
            true,
            "Alíquota PIS"
        );

        $this->dom->addChild(
            $this->rps,
            "AliquotaCOFINS",
            $std->AliquotaCOFINS,
            true,
            "Alíquota COFINS"
        );

        $this->dom->addChild(
            $this->rps,
            "AliquotaINSS",
            $std->AliquotaINSS,
            true,
            "Alíquota INSS"
        );

        $this->dom->addChild(
            $this->rps,
            "AliquotaIR",
            $std->AliquotaIR,
            true,
            "Alíquota IR"
        );

        $this->dom->addChild(
            $this->rps,
            "AliquotaCSLL",
            $std->AliquotaCSLL,
            true,
            "Alíquota CSLL"
        );

        $this->dom->addChild(
            $this->rps,
            "DescricaoRPS",
            $std->Observacao,
            true,
            "Descrição/Dados Complementares do RPS"
        );

        $this->dom->addChild(
            $this->rps,
            "DDDPrestador",
            $std->prestador->TelefonePrest,
            true,
            "DDD Telefone do Prestador"
        );

        $this->dom->addChild(
            $this->rps,
            "TelefonePrestador",
            $std->prestador->TelefonePrest,
            true,
            "Telefone do Prestador"
        );

        $this->dom->addChild(
            $this->rps,
            "DDDTomador",
            $std->tomador->DDDTomador,
            true,
            "DDD do telefone do tomador"
        );

        $this->dom->addChild(
            $this->rps,
            "TelefoneTomador",
            $std->tomador->Telefone,
            true,
            "Telefone do Tomador"
        );

        $this->dom->addChild(
            $this->rps,
            "MotCancelamento",
            $std->Observacao,
            true,
            "Motivo do Cancelamento"
        );

        $this->dom->addChild(
            $this->rps,
            "CPFCNPJIntermediario",
            "",
            true,
            "CPF/CNPJ Intemediário"
        );

        $this->dom->addChild(
            $this->rps,
            "Deducoes",
            "",
            true,
            "CPF/CNPJ Intemediário"
        );
    }

    public function buildServico($std)
    {

        $item = $this->dom->createElement('Item');

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

        $this->itens->appendChild($item);
    }

    public function cancelamento($std)
    {

        $req = $this->dom->createElement('ns1:ReqCancelamentoNFSe');
        $req->setAttribute('xmlns:ns1', 'http://localhost:8080/WsNFe2/lote');
        $req->setAttribute('xsi:schemaLocation', 'http://localhost:8080/WsNFe2/lote http://localhost:8080/WsNFe2/xsd/ReqCancelamentoNFSe.xsd');
        $req->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $this->dom->appendChild($req);

        $cabecalho = $this->dom->createElement('Cabecalho');
        $req->appendChild($cabecalho);

        $this->dom->addChild(
            $cabecalho,
            "CodCidade",
            $std->CodigoMunicipio,
            true,
            "Código da cidade da declaração padrão SIAFI."
        );

        $this->dom->addChild(
            $cabecalho,
            "CPFCNPJRemetente",
            $std->cnpj,
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
        $nota->setAttribute('Nota', 'id:' . $std->Numero);
        $lote->appendChild($nota);

        $this->dom->addChild(
            $lote,
            "InscricaoMunicipalPrestador",
            $std->InscricaoMunicipal,
            true,
            "Inscrição Municipal do Prestador"
        );

        $this->dom->addChild(
            $lote,
            "NumeroNota",
            $std->Numero,
            true,
            "Número da nota a ser cancelada"
        );

        $this->dom->addChild(
            $lote,
            "CodigoVerificacao",
            $std->CodigoCancelamento,
            true,
            "Código de verificação da nota"
        );

        $this->dom->addChild(
            $lote,
            "MotivoCancelamento",
            $std->observacao,
            true,
            "Motivo do cancelamento"
        );

        $this->xml = $this->dom->saveXML();

        return $this->xml;
    }

    public function consulta($std, $codigoCidade)
    {
        $req = $this->dom->createElement('ns1:ReqConsultaLote');
        $req->setAttribute('xmlns:ns1', 'http://localhost:8080/WsNFe2/lote');
        $req->setAttribute('xsi:schemaLocation', 'http://localhost:8080/WsNFe2/lote http://localhost:8080/WsNFe2/xsd/ReqConsultaLote.xsd');
        $req->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $this->dom->appendChild($req);

        $cabecalho = $this->dom->createElement('Cabecalho');
        $req->appendChild($cabecalho);

        $this->dom->addChild(
            $cabecalho,
            "CodCidade",
            $codigoCidade,
            true,
            "Código da cidade da declaração padrão SIAFI."
        );

        $this->dom->addChild(
            $cabecalho,
            "CPFCNPJRemetente",
            $std->nfml_cnpj_emit,
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
            $cabecalho,
            "NumeroLote",
            $std->nfml_rps,
            true,
            "Numero do lote a ser consultado"
        );

        $this->xml = $this->dom->saveXML();

        return $this->xml;
    }
}
