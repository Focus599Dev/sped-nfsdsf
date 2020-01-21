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

    public function getXML($std)
    {

        if (empty($this->xml)) {

            $this->gerarNota($std);
        }

        return $this->xml;
    }

    public function gerarNota($std)
    {

        $cabecalho = $this->dom->createElement('Cabecalho');
        $this->dom->appendChild($cabecalho);

        $this->dom->addChild(
            $cabecalho,                                     // pai    
            "CodCidade",                                    // nome
            $std,                                           // valor
            true,                                           // se é obrigatorio
            "Código da cidade da declaração padrão SIAFI."  // descrição se der catch
        );

        $this->dom->addChild(
            $cabecalho,
            "CPFCNPJRemetente",
            $std,
            true,
            "CPF /CNPJ do remetente autorizado a transmitir o RPS"
        );

        $this->dom->addChild(
            $cabecalho,
            "RazaoSocialRemetente",
            $std,
            true,
            "Razão Social do Remetente"
        );

        $this->dom->addChild(
            $cabecalho,
            "transacao",
            $std,
            true,
            "true - Se os RPS fazem parte de uma mesma transação."
        );

        $this->dom->addChild(
            $cabecalho,
            "dtInicio",
            $std,
            true,
            "Data de início do período transmitido. Data do primeiro RPS contido no lote Formato: YYYY-MM-DD"
        );

        $this->dom->addChild(
            $cabecalho,
            "dtFim",
            $std,
            true,
            "Data Final do período transmitido. Data do último RPS contida no lote Formato: YYYY-MM-DD"
        );

        $this->dom->addChild(
            $cabecalho,
            "QtdRPS",
            $std,
            true,
            "Quantidade de RPS contidos na remessa"
        );

        $this->dom->addChild(
            $cabecalho,
            "ValorTotalServicos",
            $std,
            true,
            "Valor total dos Serviços prestados nos RPS"
        );

        $this->dom->addChild(
            $cabecalho,
            "ValorTotalDeducoes",
            $std,
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

        $xml1 = $this->dom->saveXML();

        $lote = $this->dom->createElement('Lote');
        $this->dom->appendChild($lote);

        $rps = $this->dom->createElement('RPS');
        $lote->appendChild($rps);

        $this->dom->addChild(
            $rps,
            "Assinatura",
            $std,
            true,
            "Código hash de validação do conteúdo"
        );

        $this->dom->addChild(
            $rps,
            "InscricaoMunicipalPrestador",
            $std,
            true,
            "Inscrição Municipal do Prestador"
        );

        $this->dom->addChild(
            $rps,
            "RazaoSocialPrestador",
            'RPS',
            true,
            "Razão Social do Prestador"
        );

        $this->dom->addChild(
            $rps,
            "TipoRPS",
            $std,
            true,
            "Tipo de RPS, padrão “RPS”"
        );

        $this->dom->addChild(
            $rps,
            "SerieRPS",
            $std,
            true,
            "Série do RPS - Padrão “NF”"
        );

        $this->dom->addChild(
            $rps,
            "NumeroRPS",
            $std,
            true,
            "Número da RPS"
        );

        $this->dom->addChild(
            $rps,
            "DataEmissaoRPS",
            $std,
            true,
            "Data e Hora de EmissãoFormato: AAAA-MM-DDTHH:MM:SS"
        );

        $this->dom->addChild(
            $rps,
            "SituacaoRPS",
            $std,
            true,
            "Situação da RPS “N”-Normal “C”-Cancelada"
        );

        $this->dom->addChild(
            $rps,
            "SerieRPSSubstituido",
            $std,
            false,
            "Série do RPS a ser substituído – Padrão “NF”. Se não for substituto não preencher"
        );

        $this->dom->addChild(
            $rps,
            "NumeroRPSSubstituido",
            $std,
            false,
            "Número da NFSe Substituida Se não for subtituto não preencher"
        );

        $this->dom->addChild(
            $rps,
            "NumeroNFSeSubstituida",
            $std,
            false,
            "Número do RPS a ser substituído. Se não for substituto não preencher"
        );

        $this->dom->addChild(
            $rps,
            "DataEmissaoNFSeSubstituida",
            $std,
            false,
            "Data de emissão da NFSe Formato= AAAA-MM-DD. Se não for substituto preencher com “01/01/1900”"
        );

        $this->dom->addChild(
            $rps,
            "SeriePrestacao",
            $std,
            true,
            "Número do equipamento emissor do RPS ou série de prestação."
        );

        $this->dom->addChild(
            $rps,
            "InscricaoMunicipalTomador",
            $std,
            true,
            "Inscrição Municipal do Tomador. Caso o tomador não for do municipio não preencher,"
        );

        $this->dom->addChild(
            $rps,
            "CPFCNPJTomador",
            $std,
            true,
            "CPF ou CNPJ do Tomador"
        );

        $this->dom->addChild(
            $rps,
            "RazaoSocialTomador",
            $std,
            true,
            "Razão Social do Tomador"
        );

        $this->dom->addChild(
            $rps,
            "DocTomadorEstrangeiro",
            $std,
            false,
            "Documento de Identificação de Tomador Estrangeiro"
        );

        $this->dom->addChild(
            $rps,
            "TipoLogradouroTomador",
            $std,
            true,
            "Tipo de Logradouro do Tomador."
        );

        $this->dom->addChild(
            $rps,
            "LogradouroTomador",
            $std,
            true,
            "Logradouro do Tomado"
        );

        $this->dom->addChild(
            $rps,
            "NumeroEnderecoTomador",
            $std,
            true,
            "Numero de Endereço do Tomado"
        );

        $this->dom->addChild(
            $rps,
            "ComplementoEnderecoTomador",
            $std,
            false,
            "Complemento do Endereço do Tomador"
        );

        $this->dom->addChild(
            $rps,
            "TipoBairroTomador",
            $std,
            true,
            "Tipo de Bairro do Tomador"
        );

        $this->dom->addChild(
            $rps,
            "BairroTomador",
            $std,
            true,
            "Bairro do Tomador"
        );

        $this->dom->addChild(
            $rps,
            "CidadeTomador",
            $std,
            true,
            "Código da Cidade do Tomador padrão SIAFI"
        );

        $this->dom->addChild(
            $rps,
            "CidadeTomadorDescricao",
            $std,
            true,
            "Nome da Cidade do Tomador"
        );

        $this->dom->addChild(
            $rps,
            "CEPTomador",
            $std,
            true,
            "CEP do Tomador"
        );

        $this->dom->addChild(
            $rps,
            "EmailTomador",
            $std,
            true,
            "Email do Tomador. Caso o Tomador não possua email informar o valor “-”. "
        );

        $this->dom->addChild(
            $rps,
            "CodigoAtividade",
            $std,
            true,
            "Código da Atividade da RPS"
        );

        $this->dom->addChild(
            $rps,
            "AliquotaAtividade",
            $std,
            true,
            "Alíquota de ISS da Atividade"
        );

        $this->dom->addChild(
            $rps,
            "TipoRecolhimento",
            $std,
            true,
            "Tipo de Recolhimento “A” – A Receber “R” - Retido na Fonte"
        );

        $this->dom->addChild(
            $rps,
            "MunicipioPrestacao",
            $std,
            true,
            "Código do Município de Prestação"
        );

        $this->dom->addChild(
            $rps,
            "MunicipioPrestacaoDescricao",
            $std,
            true,
            "Município de Prestação do Serviço"
        );

        $this->dom->addChild(
            $rps,
            "Operacao",
            $std,
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
            $std,
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
            $std,
            true,
            "Valor PIS"
        );

        $this->dom->addChild(
            $rps,
            "ValorCOFINS",
            $std,
            true,
            "Valor COFINS"
        );

        $this->dom->addChild(
            $rps,
            "ValorINSS",
            $std,
            true,
            "Valor do INSS"
        );

        $this->dom->addChild(
            $rps,
            "ValorIR",
            $std,
            true,
            "Valor do IR"
        );

        $this->dom->addChild(
            $rps,
            "ValorCSLL",
            $std,
            true,
            "Valor do CSLL"
        );

        $this->dom->addChild(
            $rps,
            "AliquotaPIS",
            $std,
            true,
            "Alíquota PIS"
        );

        $this->dom->addChild(
            $rps,
            "AliquotaCOFINS",
            $std,
            true,
            "Alíquota COFINS"
        );

        $this->dom->addChild(
            $rps,
            "AliquotaINSS",
            $std,
            true,
            "Alíquota INSS"
        );

        $this->dom->addChild(
            $rps,
            "AliquotaIR",
            $std,
            true,
            "Alíquota IR"
        );

        $this->dom->addChild(
            $rps,
            "AliquotaCSLL",
            $std,
            true,
            "Alíquota CSLL"
        );

        $this->dom->addChild(
            $rps,
            "DescricaoRPS",
            $std,
            true,
            "Descrição/Dados Complementares do RPS"
        );

        $this->dom->addChild(
            $rps,
            "DDDPrestador",
            $std,
            false,
            "DDD Telefone do Prestador"
        );

        $this->dom->addChild(
            $rps,
            "TelefonePrestador",
            $std,
            false,
            "Telefone do Prestador"
        );

        $this->dom->addChild(
            $rps,
            "DDDTomador",
            $std,
            false,
            "DDD do telefone do tomador"
        );

        $this->dom->addChild(
            $rps,
            "TelefoneTomador",
            $std,
            false,
            "Telefone do Tomador"
        );

        $this->dom->addChild(
            $rps,
            "MotCancelamento",
            $std,
            false,
            "Motivo do Cancelamento"
        );

        $this->dom->addChild(
            $rps,
            "CpfCnpjIntermediario",
            $std,
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
                $std->servico,
                true,
                "Discriminação do Serviço"
            );

            $this->dom->addChild(
                $item,
                "Quantidade",
                $std->servico,
                true,
                "Quantidade do serviço tomado"
            );

            $this->dom->addChild(
                $item,
                "ValorUnitario",
                $std->servico,
                true,
                "Valor Unitário"
            );

            $this->dom->addChild(
                $item,
                "ValorTotal",
                $std->servico,
                true,
                "Valor total do serviço"
            );

            $this->dom->addChild(
                $item,
                "Tributavel",
                $std->servico,
                true,
                "Tributável S-Item tributável, N-Não tributável."
            );
        }

        $xml2 = $this->dom->saveXML();

        $this->xml = $xml1 . $xml2;
        return $this->xml;
    }

    public function cancelamento($std)
    {

        $root = $this->dom->createElement('NFSE');
        $this->dom->appendChild($root);

        $identificacao = $this->dom->createElement('IDENTIFICACAO');
        $root->appendChild($identificacao);

        $this->dom->addChild(
            $identificacao,
            "INSCRICAO",
            $this->inscricaoUser,
            true,
            "Inscrição mobiliária do prestador da NFS-e"
        );

        $this->dom->addChild(
            $identificacao,
            "LOTE",
            $std->sequencia,
            true,
            "Lote da NFS-e, numeros inteiros de até 9"
        );

        $this->dom->addChild(
            $identificacao,
            "SEQUENCIA",
            '1',
            // $std->sequencia,
            true,
            "Sequência da NFS-e, numeros inteiros de até 9"
        );

        $this->dom->addChild(
            $identificacao,
            "OBSERVACAO",
            $std->observacao,
            true,
            "Observação do cancelamento da NFS-e"
        );

        $this->xml = $this->dom->saveXML();

        return $this->xml;
    }

    public function consultaLote($std)
    {

        $root = $this->dom->createElement('NFSE');
        $this->dom->appendChild($root);

        $identificacao = $this->dom->createElement('IDENTIFICACAO');
        $root->appendChild($identificacao);

        $this->dom->addChild(
            $identificacao,
            "INSCRICAO",
            $this->inscricaoUser,
            true,
            "Inscrição mobiliária do prestador da NFS-e"
        );

        $this->dom->addChild(
            $identificacao,
            "LOTE",
            $std->nfml_numero_lote,
            true,
            "Lote da NFS-e, numeros inteiros de até 9"
        );

        $this->xml = $this->dom->saveXML();

        return $this->xml;
    }

    public function consulta($std)
    {

        $root = $this->dom->createElement('NFSE');
        $this->dom->appendChild($root);

        $identificacao = $this->dom->createElement('IDENTIFICACAO');
        $root->appendChild($identificacao);

        $this->dom->addChild(
            $identificacao,
            "INSCRICAO",
            $this->inscricaoUser,
            true,
            "Inscrição mobiliária do prestador da NFS-e"
        );

        $this->dom->addChild(
            $identificacao,
            "LOTE",
            $std->NumeroLote,
            true,
            "Lote da NFS-e, numeros inteiros de até 9"
        );

        $this->dom->addChild(
            $identificacao,
            "SEQUENCIA",
            $std->Sequencia,
            true,
            "Sequência da NFS-e, numeros inteiros de até 9"
        );

        $this->xml = $this->dom->saveXML();

        return $this->xml;
    }
}
