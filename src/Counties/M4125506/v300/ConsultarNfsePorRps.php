<?php

namespace NFePHP\NFSe\Counties\M4125506\v300;

use NFePHP\Common\DOMImproved as Dom;
use NFePHP\NFSe\Models\Abrasf\Factories\Factory;

class ConsultarNfsePorRps extends Factory
{
    protected $xmlns = 'http://www.abrasf.org.br/nfse.xsd';

    /**
     * Método usado para gerar o XML do Soap Request
     * @param $cnpj
     * @param $im
     * @param $numero
     * @param $serie
     * @param $tipo
     * @return mixed
     */
    public function render(
        $versao,
        $cnpj,
        $im,
        $numero,
        $serie,
        $tipo
    ) {
        $xsd = "servico_consultar_nfse_rps_envio_v03";
        $dom = new Dom('1.0', 'utf-8');
        //Cria o elemento pai
        $root = $dom->createElement('p:ConsultarNfseRpsEnvio');
        //Atribui o namespace
        $root->setAttribute('xmlns:p', "http://nfe.sjp.pr.gov.br/$xsd.xsd");
        $root->setAttribute('xmlns:p1',"http://nfe.sjp.pr.gov.br/tipos_v03.xsd");
        $root->setAttribute('xmlns:xsi', "http://www.w3.org/2001/XMLSchema-instance");
        $root->setAttribute('Id', "consultar");

        //Cria os dados da IdentificacaoRps
        $identificacaoRps = $dom->createElement('p:p:IdentificacaoRps');
        
        //Adiciona o Numero do rps na tag Numero
        $dom->addChild(
            $identificacaoRps,
            'p1:Numero',
            $numero,
            true,
            "p1:Numero",
            true
        );

        //Adiciona a serie do rps na tag Serie
        $dom->addChild(
            $identificacaoRps,
            'p1:Serie',
            $serie,
            true,
            "p1:Serie",
            true
        );

        //Adiciona o tipo do rps na tag Tipo
        $dom->addChild(
            $identificacaoRps,
            'p1:Tipo',
            $tipo,
            true,
            "p1:Tipo",
            true
        );
        
        //Adiciona a tag Prestador a consulta
        $dom->appChild($root, $identificacaoRps, 'Adicionando tag IdentificacaoRps');

        //Cria os dados do prestador
        $prestador = $dom->createElement('p:Prestador');
        
        //Adiciona o Cnpj na tag Cnpj
        $dom->addChild(
            $prestador,
            'p1:Cnpj',
            $cnpj,
            true,
            "p1:Cnpj",
            true
        );

        //Adiciona a InscricaoMunicipal na tag InscricaoMunicipal
        $dom->addChild(
            $prestador,
            'p1:InscricaoMunicipal',
            $im,
            true,
            "p1:InscricaoMunicipal",
            true
        );
        
        //Adiciona a tag Prestador a consulta
        $dom->appChild($root, $prestador, 'Adicionando tag Prestador');

        //Adiciona as tags ao DOM
        $dom->appendChild($root);
        //Parse para XML
        $body = str_replace('<?xml version="1.0" encoding="utf-8"?>', '', $dom->saveXML());
        $this->validar($versao, $body, "Abrasf/SJP", $xsd, '');
        return $body;
    }
}
