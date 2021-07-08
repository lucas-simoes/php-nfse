<?php

namespace NFePHP\NFSe\Models\Simpliss\Factories\v100;

use NFePHP\Common\DOMImproved as Dom;
use NFePHP\NFSe\Models\Simpliss\Factories\Factory;

class ConsultarNfse extends Factory
{
    protected $xmlns = 'http://www.abrasf.org.br/nfse.xsd';

    /**
     * Método usado para gerar o XML do Soap Request
     * @param $versao
     * @param $remetenteCNPJCPF
     * @param $im
     * @param $numeroNfse
     * @param $dataInicial
     * @param $dataFinal
     * @param $rps
     * @return mixed
     */
    public function render(
        $versao,
        $remetenteCNPJCPF,
        $im,
        $numeroNfse,
        $dataInicial,
        $dataFinal,
        $rps
    ) {
        $xsd = "servico_consultar_nfse_envio";
        $dom = new Dom('1.0', 'utf-8');
        //Cria o elemento pai
        $root = $dom->createElement('ConsultarNfseEnvio');

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

        //Adiciona a tag NumeroNfse na consulta
        $dom->addChild(
            $root,
            'NumeroNfse',
            $numeroNfse,
            true,
            "Numero da nfse",
            true
        );

        //Cria os dados do PeriodoEmissao
        $periodoEmissao = $dom->createElement('PeriodoEmissao');

        //Adiciona a Data Inicial na tag DataInicial
        $dom->addChild(
            $periodoEmissao,
            'DataInicial',
            $dataInicial,
            true,
            "DataInicial",
            true
        );

        //Adiciona a Data Final na tag DataFinal
        $dom->addChild(
            $periodoEmissao,
            'DataFinal',
            $dataFinal,
            true,
            "DataFinal",
            true
        );

        //Cria os dados do Tomador
        $tomador = $dom->createElement('Tomador');

        //Adiciona a Cpf Cnpj do tomador
        $dom->addChild(
            $tomador,
            'CpfCnpj',
            $rps->infTomador['cpfcnpj'],
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
        $periodoEmissao->appendChild($tomador);

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
                $rps->infIntermediario['cpfcnpj'],
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
            $periodoEmissao->appendChild($intermediarioServico);
        }        

        //Adiciona a tag PeriodoEmissao a consulta
        $dom->appChild($root, $periodoEmissao, 'Adicionando tag PeriodoEmissao');

        //Parse para XML
        $body = $dom->saveXML();
        $body = $this->clear($body);
        
        return '<?xml version="1.0" encoding="utf-8"?>' . $body;
    }
}
