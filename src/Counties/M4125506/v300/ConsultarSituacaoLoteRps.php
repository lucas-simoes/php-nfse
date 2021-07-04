<?php

namespace NFePHP\NFSe\Counties\M4125506\v300;

use NFePHP\Common\DOMImproved as Dom;
use NFePHP\NFSe\Models\Abrasf\Factories\Factory;

class ConsultarSituacaoLoteRps extends Factory
{
    protected $xmlns = 'http://www.abrasf.org.br/nfse.xsd';

    /**
     * Método usado para gerar o XML do Soap Request
     * @param $versao
     * @param $cnpj
     * @param $im
     * @param $protocolo
     * @return mixed
     */
    public function render(
        $versao,
        $cnpj,
        $im,
        $protocolo
    ) {
        $xsd = 'servico_consultar_situacao_lote_rps_envio_v03';
        $dom = new Dom('1.0', 'utf-8');
        //Cria o elemento pai
        $root = $dom->createElement('ConsultarSituacaoLoteRpsEnvio');
        //Atribui o namespace
        $root->setAttribute('xmlns', "http://nfe.sjp.pr.gov.br/$xsd.xsd");
        $root->setAttribute('Id', "consultar");

        //Cria os dados do prestador
        $prestador = $dom->createElement('Prestador');
        $prestador->setAttribute('xmlns:tipos', "http://nfe.sjp.pr.gov.br/tipos_v03.xsd");
        
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

        //Adiciona a tag protoclo na consulta
        $dom->addChild(
            $root,
            'Protocolo',
            $protocolo,
            true,
            "Numero do Protocolo",
            true
        );

        //Adiciona as tags ao DOM
        $dom->appendChild($root);
        //Parse para XML
        $body = str_replace('<?xml version="1.0" encoding="utf-8"?>', '', $dom->saveXML());
        #echo '<pre>'.print_r($body).'</pre>';die;
        $this->validar($versao, $body, 'Abrasf/SJP', $xsd, '');
        return $body;
    }
}
