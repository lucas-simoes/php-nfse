<?php

namespace NFePHP\NFSe\Models\IPM\Factories\v100;

use Exception;
use InvalidArgumentException;
use stdClass;
use NFePHP\NFSe\Models\IPM\Rps;
use NFePHP\NFSe\Common\DOMImproved as Dom;
use NFePHP\NFSe\Models\IPM\Factories\Signer;
use NFePHP\NFSe\Models\IPM\Factories\Factory;

class GerarNota extends Factory
{
    /**
     * Método usado para gerar o XML do Soap Request
     * @param $versao
     * @param $rpss
     * @return string
     */
    public function render(
        Rps $rps,
        stdClass $config
    ) {
        $dom = new Dom('1.0', 'utf-8');
        $dom->formatOutput = false;
        //Cria o elemento nfse
        $root = $dom->createElement('nfse');
        if ($this->certificate) {
            $root->setAttribute('id', 'nota');
        }

        //Adiciona as tags ao DOM
        $dom->appendChild($root);

        if($config->teste) {
            $dom->addChild(
                $root,
                'nfse_teste',
                1,
                true,
                "Definir como teste de integração",
                true
            );
        }

        if ($rps->infIdentificador) {
            $dom->addChild(
                $root,
                'identificador',
                $rps->infIdentificador,
                true,
                "Identificador do arquivo a ser processado",
                true
            );
        }

        if ($config->trabalha_com_rps) {
            //Cria o elemento rps se a prefeitura trabalha com rps
            $rpsE = $dom->createElement('rps');

            $dom->addChild(
                $rpsE,
                'nro_recibo_provisorio',
                $rps->infNumero,
                true,
                "Número do Rps",
                true
            );

            $dom->addChild(
                $rpsE,
                'serie_recibo_provisorio',
                $rps->infSerie,
                true,
                "Número do Rps",
                true
            );

            $dom->addChild(
                $rpsE,
                'data_emissao_recibo_provisorio',
                $rps->infDataEmissao->format('d/m/Y'),
                true,
                "Data de emissão do Rps",
                true
            );

            $dom->addChild(
                $rpsE,
                'hora_emissao_recibo_provisorio',
                $rps->infDataEmissao->format('H:i:s'),
                true,
                "Hora de emissão do Rps",
                true
            );

            //Adiciona as tags ao DOM
            $root->appendChild($rpsE);
        }

        if ($rps->infPedagio) {
            //Cria o elemento pedagio
            $pedagio = $dom->createElement('pedagio');

            $dom->addChild(
                $pedagio,
                'cod_equipamento_automatico',
                $rps->infPedagio['cod_equipamento_automatico'],
                true,
                "Código do equipamento eletrônico do pedágio",
                true
            );

            //Adiciona as tags ao DOM
            $root->appendChild($pedagio);
        }

        //Cria o elemento nf
        $nf = $dom->createElement('nf');

        $dom->addChild(
            $nf,
            'data_fato_gerador',
            $rps->infDataFatoGerador->format('d/m/Y'),
            true,
            "Data do fator gerador da nota fiscal",
            true
        );

        $dom->addChild(
            $nf,
            'valor_total',
            $rps->infValorTotal,
            true,
            "Valor total da nota fiscal",
            true
        );

        $dom->addChild(
            $nf,
            'valor_desconto',
            $rps->infValorDesconto,
            true,
            "Valor desconto da nota fiscal",
            true
        );

        $dom->addChild(
            $nf,
            'valor_ir',
            $rps->infValorIr,
            true,
            "Valor do imposto de renda retido da nota fiscal",
            true
        );

        $dom->addChild(
            $nf,
            'valor_inss',
            $rps->infValorInss,
            true,
            "Valor do INSS nota fiscal",
            true
        );

        $dom->addChild(
            $nf,
            'valor_contribuicao_social',
            $rps->infValorContribuicaoSocial,
            true,
            "Valor da contrinuicao Social nota fiscal",
            true
        );

        $dom->addChild(
            $nf,
            'valor_rps',
            $rps->infValorRps,
            true,
            "Valor de retenções da previdência social nota fiscal",
            true
        );

        $dom->addChild(
            $nf,
            'valor_pis',
            $rps->infValorPis,
            true,
            "Valor do PIS nota fiscal",
            true
        );

        $dom->addChild(
            $nf,
            'valor_cofins',
            $rps->infValorCofins,
            true,
            "Valor do COFINS nota fiscal",
            true
        );

        $dom->addChild(
            $nf,
            'observacao',
            $rps->infObservacao,
            true,
            "Observações nota fiscal",
            true
        );

        //Adiciona as tags ao DOM
        $root->appendChild($nf);

        //Cria o elemento prestador
        $prestador = $dom->createElement('prestador');

        $dom->addChild(
            $prestador,
            'cpfcnpj',
            $rps->infCpfCnpjPrestador,
            true,
            "CPF/CNPJ do emissor da nota",
            true
        );

        $dom->addChild(
            $prestador,
            'cidade',
            $config->cod_tom_municipio,
            true,
            "Código tom do municipio do emissor da nota",
            true
        );

        //Adiciona as tags ao DOM
        $root->appendChild($prestador);

        //Cria o elemento tomador
        $tomador = $dom->createElement('tomador');

        $dom->addChild(
            $tomador,
            'endereco_informado',
            $rps->infTomadorEndereco['endereco_informado'],
            true,
            "Define se apresenta o endereco do tomador na nota",
            true
        );

        $dom->addChild(
            $tomador,
            'tipo',
            $rps->infTomador['tipo'],
            true,
            "Tipo de pessoa do tomador na nota",
            true
        );

        if ($rps->infTomadorEstrangeiro) {
            if ($rps->infTomador['tipo'] != 'E') {
                throw new InvalidArgumentException("Definido informações de tomador estrangeiro para tomador diferente de 'E'");
            }
            $dom->addChild(
                $tomador,
                'identificador',
                $rps->infTomadorEstrangeiro['identificador'],
                true,
                "Numero do cartao de identificacao  estrangeira ou passaporte",
                true
            );

            $dom->addChild(
                $tomador,
                'estado',
                $rps->infTomadorEstrangeiro['estado'],
                true,
                "Estado de origem do tomador estrangeiro",
                true
            );

            $dom->addChild(
                $tomador,
                'pais',
                $rps->infTomadorEstrangeiro['pais'],
                true,
                "Pais de origem do tomador estrangeiro",
                true
            );
        }

        $dom->addChild(
            $tomador,
            'cpfcnpj',
            $rps->infTomador['cpfcnpj'],
            true,
            "CPF/Cnpj do tomador",
            true
        );

        $dom->addChild(
            $tomador,
            'ie',
            $rps->infTomador['ie'],
            true,
            "Inscrição Estadual do tomador",
            true
        );

        $dom->addChild(
            $tomador,
            'nome_razao_social',
            $rps->infTomador['nome_razao_social'],
            true,
            "Nome do tomador",
            true
        );

        $dom->addChild(
            $tomador,
            'sobrenome_nome_fantasia',
            $rps->infTomador['sobrenome_nome_fantasia'],
            true,
            "Sobrenome ou nome fantasia do tomador",
            true
        );

        $dom->addChild(
            $tomador,
            'logradouro',
            $rps->infTomadorEndereco['logradouro'],
            true,
            "Logradouro do endereço do tomador",
            true
        );

        $dom->addChild(
            $tomador,
            'email',
            $rps->infTomador['email'],
            true,
            "Emails do tomador, quando necessário informar os emals separados por ;",
            true
        );

        $dom->addChild(
            $tomador,
            'numero_residencia',
            $rps->infTomadorEndereco['numero_residencia'],
            true,
            "Número do endereço do tomador",
            true
        );

        $dom->addChild(
            $tomador,
            'complemento',
            $rps->infTomadorEndereco['complemento'],
            true,
            "Complemento do endereço do tomador",
            true
        );

        $dom->addChild(
            $tomador,
            'ponto_referencia',
            $rps->infTomadorEndereco['ponto_referencia'],
            true,
            "Ponto de referência do endereço do tomador",
            true
        );

        $dom->addChild(
            $tomador,
            'bairro',
            $rps->infTomadorEndereco['bairro'],
            true,
            "Bairro do endereço do tomador",
            true
        );

        $dom->addChild(
            $tomador,
            'cidade',
            $rps->infTomadorEndereco['cidade'],
            true,
            "Código tom ou nome da cidade, se estrangeiro, do endereço do tomador",
            true
        );

        $dom->addChild(
            $tomador,
            'cep',
            $rps->infTomadorEndereco['cep'],
            true,
            "Cep do endereço do tomador",
            true
        );

        $dom->addChild(
            $tomador,
            'ddd_fone_comercial',
            $rps->infTomadorTelefone['ddd_fone_comercial'],
            true,
            "Código de área do telefone do estabelecimento do Tomador",
            true
        );

        $dom->addChild(
            $tomador,
            'fone_comercial',
            $rps->infTomadorTelefone['fone_comercial'],
            true,
            "Telefone do estabelecimento do Tomador",
            true
        );

        $dom->addChild(
            $tomador,
            'ddd_fone_residencial',
            $rps->infTomadorTelefone['ddd_fone_residencial'],
            true,
            "Código de área do telefone residencial do Tomador",
            true
        );

        $dom->addChild(
            $tomador,
            'fone_residencial',
            $rps->infTomadorTelefone['fone_residencial'],
            true,
            "Telefone residencial do Tomador",
            true
        );

        $dom->addChild(
            $tomador,
            'ddd_fax',
            $rps->infTomadorTelefone['ddd_fax'],
            true,
            "Código de área do fax do Tomador",
            true
        );

        $dom->addChild(
            $tomador,
            'fone_fax',
            $rps->infTomadorTelefone['fone_fax'],
            true,
            "Fax do Tomador",
            true
        );


        //Adiciona as tags ao DOM
        $root->appendChild($tomador);

        //Cria o elemento itens
        $itens = $dom->createElement('itens');

        /** @var $item ItensRps[] */
        foreach ($rps->infItens as $item) {
            //Cria o elemento itens
            $lista = $dom->createElement('itens');

            $dom->addChild(
                $lista,
                'tributa_municipio_prestador',
                $item->infTributaMunicipioPrestador,
                true,
                "Informa onde sera recolhido o imposto",
                true
            );

            $dom->addChild(
                $lista,
                'codigo_local_prestacao_servico',
                $item->infCodigoLocalPrestacaoServico,
                true,
                "Codigo tom da cidade onde o serviço foi prestado",
                true
            );

            $dom->addChild(
                $lista,
                'unidade_codigo',
                $item->infUnidadeCodigo,
                true,
                "Codigo das unidades de serviços já cadastradas",
                true
            );

            $dom->addChild(
                $lista,
                'unidade_quantidade',
                $item->infUnidadeQuantidade,
                true,
                "Quantidade dos serviços prestados relativo à unidade informada",
                true
            );

            $dom->addChild(
                $lista,
                'unidade_valor_unitario',
                $item->infUnidadeValorUnitario,
                true,
                "Valor unitario dos serviços prestados relativo à unidade informada",
                true
            );

            $dom->addChild(
                $lista,
                'codigo_item_lista_servico',
                $item->infCodigoItemListaServico,
                true,
                "Código do subitem da lista de serviços",
                true
            );

            $dom->addChild(
                $lista,
                'descritivo',
                $item->infDescritivo,
                true,
                "Descritivo coloquial do serviço prestado",
                true
            );

            $dom->addChild(
                $lista,
                'aliquota_item_lista_servico',
                $item->infAliquotaItemListaServico,
                true,
                "Alíquota que irá incidir sobre a base de cálculo. Cuidado com esse campo",
                true
            );

            $dom->addChild(
                $lista,
                'situacao_tributaria',
                $item->infSituacaoTributaria,
                true,
                "Código da Situação Tributária",
                true
            );

            $dom->addChild(
                $lista,
                'valor_tributavel',
                $item->infValorTributavel,
                true,
                "Valor do item que servirá de base de cálculo para o imposto,com a dedução aplicada, se a situação tributária permitir",
                true
            );

            $dom->addChild(
                $lista,
                'valor_deducao',
                $item->infValorDeducao,
                true,
                "Valor da dedução, quando houver e se a situação tributária permitir",
                true
            );

            $dom->addChild(
                $lista,
                'valor_issrf',
                $item->infValorDeducao,
                true,
                "Valor do ISS Retido na Fonte, quando houver e se a situação tributária permitir",
                true
            );

            //Adiciona as tags ao DOM
            $itens->appendChild($lista);
        }

        //Adiciona as tags ao DOM
        $root->appendChild($itens);

        if (!empty($rps->infGenericos)) {
            //Cria o elemento genericos
            $genericos = $dom->createElement('genericos');

            foreach ($rps->infGenericos as $generico) {
                //Cria o elemento linha
                $linha = $dom->createElement('linha');

                $dom->addChild(
                    $linha,
                    'titulo',
                    $generico['titulo'],
                    true,
                    "Título do campo livre.",
                    true
                );

                $dom->addChild(
                    $linha,
                    'descricao',
                    $generico['descricao'],
                    true,
                    "Contéudo do campo livre.",
                    true
                );

                //Adiciona as tags ao DOM
                $root->appendChild($linha);
            }
            //Adiciona as tags ao DOM
            $root->appendChild($genericos);
        }

        if ($rps->infProdutos) {
            //Cria o elemento produtos
            $produtos = $dom->createElement('produtos');

            $dom->addChild(
                $produtos,
                'descricao',
                $rps->infProdutos['descricao'],
                true,
                "Tudo que se quer que saia na nota a respeito dos produtos (quantidade, desconto, etc.) de forma agurpada",
                true
            );

            $dom->addChild(
                $produtos,
                'valor',
                $rps->infProdutos['valor'],
                true,
                "Soma do valor dos produtos da NFS-e.",
                true
            );

            //Adiciona as tags ao DOM
            $root->appendChild($produtos);
        }

        if ($rps->infFormasPagamentos) {
            if (empty($rps->infFormasPagamentos['parcelas'])) {
                throw new InvalidArgumentException("Definição de tipo de pagamentos sem adicionar as parcelas");
            }

            //Cria o elemento forma_pagamento
            $forma_pagamento = $dom->createElement('forma_pagamento');

            $valorTotal  = 0;
            foreach ($rps->infFormasPagamentos['parcelas'] as $parcela) {
                $dom->addChild(
                    $forma_pagamento,
                    'numero',
                    $parcela['numero'],
                    true,
                    "Numero da parcela",
                    true
                );

                $dom->addChild(
                    $forma_pagamento,
                    'valor',
                    $parcela['valor'],
                    true,
                    "valor da parcela",
                    true
                );
                $valorTotal += $parcela['valor'];

                $dom->addChild(
                    $forma_pagamento,
                    'data_vencimento',
                    $parcela['data_vencimento']->format('d/m/Y'),
                    true,
                    "Data Vencimento da parcela",
                    true
                );
            }

            if ($rps->infValorTotal != $valorTotal) {
                throw new InvalidArgumentException("A soma do valor das parcelas deve ser igual ao da tag <valor_total>");
            }
            //Adiciona as tags ao DOM
            $root->appendChild($forma_pagamento);
        }

        $body = str_replace('<?xml version="1.0" encoding="utf-8"?>', '', $dom->saveXML());

        if ($this->certificate) {

            $body = Signer::sign(
                $this->certificate,
                $body,
                'nfse',
                'id',
                $this->algorithm,
                [false, false, null, null],
                '',
                true
            );
        }

        $body = $this->clear($body);
        #echo '<pre>'.print_r($body).'</pre>';die;
        return '<?xml version="1.0" encoding="ISO-8859-1"?>' . $body;
    }
}
