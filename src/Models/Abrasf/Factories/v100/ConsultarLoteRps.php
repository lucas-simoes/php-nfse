<?php

namespace NFePHP\NFSe\Models\Abrasf\Factories\v100;

use NFePHP\Common\DOMImproved as Dom;
use NFePHP\NFSe\Models\Abrasf\Factories\ConsultarLoteRps as ConsultarLoteRpsBase;

class ConsultarLoteRps extends ConsultarLoteRpsBase
{
    protected $xmlns = 'http://www.abrasf.org.br/nfse.xsd';

    /**
     * Método usado para gerar o XML do Soap Request
     * @param $cnpj
     * @param $im
     * @param $protocolo
     * @return mixed
     */
    public function render(
        $cnpj,
        $im,
        $protocolo
    ) {
        $dom = new Dom('1.0', 'utf-8');
        //Cria o elemento pai
        $root = $dom->createElement('ConsultarLoteRpsEnvio');
        //Atribui o namespace
        $root->setAttribute('xmlns', $this->xmlns);

        //Cria os dados do prestador
        $prestador = $dom->createElement('Prestador');

        //Adiciona o Cnpj na tag Prestador
        $dom->addChild(
            $prestador,
            'Cnpj',
            $cnpj,
            true,
            "CNPJ",
            true
        );
        // //Adiciona a Inscrição Municipal na tag Prestador
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
        $xml = str_replace('<?xml version="1.0" encoding="utf-8"?>', '', $dom->saveXML());

        return $xml;
    }
}
