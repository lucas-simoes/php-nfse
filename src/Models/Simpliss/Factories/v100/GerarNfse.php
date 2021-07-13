<?php

namespace NFePHP\NFSe\Models\Simpliss\Factories\v100;

use NFePHP\Common\DOMImproved as Dom;
use NFePHP\NFSe\Models\Simpliss\Factories\Signer;
use NFePHP\NFSe\Models\Simpliss\Factories\Factory;

class GerarNfse extends Factory
{
    protected $xmlns = 'http://www.abrasf.org.br/nfse.xsd';

    /**
     * Método usado para gerar o XML do Soap Request
     * @param $versao
     * @param $remetenteCNPJCPF
     * @param $im
     * @param $rps
     * @return mixed
     */
    public function render(
        $versao,
        $remetenteCNPJCPF,
        $im,
        $rps
    ) {
        $xsd = "servico_consultar_nfse_envio_v03";
        $dom = new Dom('1.0', 'utf-8');
        //Cria o elemento pai
        $root = $dom->createElement('GerarNovaNfseEnvio');
        $root->setAttribute('Id', "Nfse");

        //Cria os dados do prestador
        $prestador = $dom->createElement('Prestador');

        //Adiciona o Cnpj na tag Cnpj
        $dom->addChild(
            $prestador,
            'Cnpj',
            $remetenteCNPJCPF,
            true,
            "Cnpj",
            true
        );

        //Adiciona a InscricaoMunicipal na tag InscricaoMunicipal
        $dom->addChild(
            $prestador,
            'InscricaoMunicipal',
            $im,
            true,
            "InscricaoMunicipal",
            true
        );

        //Adiciona a tag Prestador a consulta
        $dom->appChild($root, $prestador, 'Adicionando tag Prestador');

        //Cria os dados do InformacaoNfse
        $informacaoNfse = $dom->createElement('InformacaoNfse');
        $informacaoNfse->setAttribute('Id', "lote");

        $dom->addChild(
            $informacaoNfse,
            'NaturezaOperacao',
            $rps->infNaturezaOperacao,
            true,
            'Natureza da operação',
            false
        );
        $dom->addChild(
            $informacaoNfse,
            'RegimeEspecialTributacao',
            $rps->infRegimeEspecialTributacao,
            false,
            'RegimeEspecialTributacao',
            false
        );
        $dom->addChild(
            $informacaoNfse,
            'OptanteSimplesNacional',
            $rps->infOptanteSimplesNacional,
            true,
            'OptanteSimplesNacional',
            false
        );
        $dom->addChild(
            $informacaoNfse,
            'IncentivadorCultural',
            $rps->infIncentivadorCultural,
            true,
            'IncentivadorCultural',
            false
        );
        $dom->addChild(
            $informacaoNfse,
            'Status',
            $rps->infStatus,
            true,
            'Status',
            false
        );

        $dom->addChild(
            $informacaoNfse,
            'NfseSubstituida',
            $rps->infNfseSubstituida,
            true,
            'Número da nota fiscal de serviço a ser substituida',
            false
        );

        $dom->addChild(
            $informacaoNfse,
            'OutrasInformacoes',
            $rps->infOutrasInformacoes,
            true,
            'Informações adicionais ao documento',
            false
        );

        //Cria os dados do Servico
        $servico = $dom->createElement('Servico');

        $dom->addChild(
            $servico,
            'ItemListaServico',
            $rps->infItemListaServico,
            true,
            "Código do item da lista de servicos",
            true
        );

        $dom->addChild(
            $servico,
            'CodigoCnae',
            $rps->infCodigoCnae,
            true,
            "Código CNAE",
            true
        );

        $dom->addChild(
            $servico,
            'CodigoTributacaoMunicipio',
            $rps->infCodigoTributacaoMunicipio,
            true,
            "Código da Tributacao",
            true
        );

        $dom->addChild(
            $servico,
            'Discriminacao',
            $rps->infDiscriminacao,
            true,
            "Discriminao do nota de servico",
            true
        );

        $dom->addChild(
            $servico,
            'CodigoMunicipio',
            $rps->infCodigoMunicipio,
            true,
            "Código IBGE do municipio",
            true
        );

        //Cria os dados do ItemServico
        $itemServico = $dom->createElement('ItemServico');
        
        $dom->addChild(
            $itemServico,
            'Descricao',
            $rps->infDescricao,
            true,
            "Descrição do servico",
            true
        );

        $dom->addChild(
            $itemServico,
            'Quantidade',
            $rps->infQuantidade,
            true,
            "Quantidade de Itenss",
            true
        );

        $dom->addChild(
            $itemServico,
            'ValorUnitario',
            $rps->infValorUnitario,
            true,
            "Valor unitário de cada servico",
            true
        );

        //Adiciona as tags ao DOM
        $servico->appendChild($itemServico);

        //Adiciona as tags ao DOM
        $informacaoNfse->appendChild($servico);

        //Cria os dados do Tomador
        $tomador = $dom->createElement('Tomador');

        //Adiciona a Cpf Cnpj do tomador
        $dom->addChild(
            $tomador,
            'CpfCnpj',
            $rps->infTomador['cnpjcpf'],
            true,
            "Cpf / Cnpj",
            true
        );

        if ($rps->infTomador['im']) {
            //Adiciona o InscricaoMunicipal do tomador
            $dom->addChild(
                $tomador,
                'InscricaoMunicipal',
                $rps->infTomador['im'],
                true,
                "Inscricao Municipal",
                true
            );
        }
        if ($rps->infTomador['ie']) {
            //Adiciona a inscricaoEstadual do tomador
            $dom->addChild(
                $tomador,
                'InscricaoEstadual',
                $rps->infTomador['ie'],
                true,
                "Inscricao Estadual",
                true
            );
        }

        //Adiciona as tags ao DOM
        $informacaoNfse->appendChild($tomador);

        if ($rps->infIntermediario['razao']) {
            //Cria os dados do IntermediarioServico
            $intermediarioServico = $dom->createElement('IntermediarioServico');

            //Adiciona a inscricaoEstadual do tomador
            $dom->addChild(
                $intermediarioServico,
                'RazaoSocial',
                $rps->infIntermediario['razao'],
                true,
                "Inscricao Estadual",
                true
            );

            //Adiciona a Cpf Cnpj do tomador
            $dom->addChild(
                $intermediarioServico,
                'CpfCnpj',
                $rps->infIntermediario['cnpjcpf'],
                true,
                "Cpf / Cnpj",
                true
            );

            if ($rps->infIntermediario['im']) {
                //Adiciona o InscricaoMunicipal do tomador
                $dom->addChild(
                    $intermediarioServico,
                    'InscricaoMunicipal',
                    $rps->infIntermediario['im'],
                    true,
                    "Inscricao Municipal",
                    true
                );
            }
            //Adiciona as tags ao DOM
            $informacaoNfse->appendChild($intermediarioServico);

        }
        
        /** Construção Civil **/
        if (!empty($rps->infConstrucaoCivil['obra'])) {
            $construcao = $dom->createElement('ContrucaoCivil');
            $dom->addChild(
                $construcao,
                'CodigoObra',
                $rps->infConstrucaoCivil['obra'],
                false,
                'Codigo da Obra',
                false
            );
            $dom->addChild(
                $construcao,
                'Art',
                $rps->infConstrucaoCivil['art'],
                true,
                'Art da Obra',
                false
            );
            
            //Adiciona a tag construcao a consulta
            $informacaoNfse->appendChild($informacaoNfse);
        }
        /** FIM Construção Civil **/

        //Adiciona a tag informacaoNfse a consulta
        $root->appendChild($informacaoNfse);

        if ($this->certificate) {
            //Parse para XML
            $xml = str_replace('<?xml version="1.0" encoding="utf-8"?>', '', $dom->saveXML());

            $body = Signer::sign(
                $this->certificate,
                $xml,
                'GerarNovaNfseEnvio',
                'Id',
                $this->algorithm,
                [false, false, null, null],
                '',
                true
            );
        }
        
        //Parse para XML
        $body = $dom->saveXML();
        $body = $this->clear($body);

        return '<?xml version="1.0" encoding="utf-8"?>' . $body;
    }
}
