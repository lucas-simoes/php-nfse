<?php

namespace NFePHP\NFSe\Counties\M4125506\v300;

use NFePHP\Common\DOMImproved as Dom;
use NFePHP\NFSe\Models\Abrasf\Factories\Factory;

class ConsultarNfse extends Factory
{
    protected $xmlns = 'http://www.abrasf.org.br/nfse.xsd';

    /**
     * Método usado para gerar o XML do Soap Request
     * @param $versao
     * @param $cnpj
     * @param $im
     * @param $numeroNfse
     * @param $dataInicial
     * @param $dataFinal
     * @return mixed
     */
    public function render(
        $versao,
        $cnpj,
        $im,
        $numeroNfse,
        $dataInicial,
        $dataFinal
    ) {
        $xsd = "servico_consultar_nfse_envio_v03";
        $dom = new Dom('1.0', 'utf-8');
        //Cria o elemento pai
        $root = $dom->createElement('ConsultarNfseEnvio');
        //Atribui o namespace
        $root->setAttribute('xmlns', "http://nfe.sjp.pr.gov.br/$xsd.xsd");
        $root->setAttribute('xmlns:tipos', "http://nfe.sjp.pr.gov.br/tipos_v03.xsd");
        $root->setAttribute('xmlns:xsi', "http://www.w3.org/2001/XMLSchema-instance");

        //Cria os dados do prestador
        $prestador = $dom->createElement('Prestador');
        
        //Adiciona o Cnpj na tag tipos:Cnpj
        $dom->addChild(
            $prestador,
            'tipos:Cnpj',
            $cnpj,
            true,
            "tipos:Cnpj",
            true
        );

        //Adiciona a InscricaoMunicipal na tag tipos:InscricaoMunicipal
        $dom->addChild(
            $prestador,
            'tipos:InscricaoMunicipal',
            $im,
            true,
            "tipos:InscricaoMunicipal",
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
        
        //Adiciona a tag PeriodoEmissao a consulta
        $dom->appChild($root, $periodoEmissao, 'Adicionando tag PeriodoEmissao');

        //Adiciona as tags ao DOM
        $dom->appendChild($root);
        //Parse para XML
        $body = str_replace('<?xml version="1.0" encoding="utf-8"?>', '', $dom->saveXML());
        $this->validar($versao, $body, "Abrasf/SJP", $xsd, '');
        return $body;
    }
}
