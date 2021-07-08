<?php

namespace NFePHP\NFSe\Models\Simpliss\Factories\v100;

use NFePHP\Common\DOMImproved as Dom;
use NFePHP\NFSe\Models\Simpliss\Factories\Factory;

class ConsultarSituacaoLoteRps extends Factory
{
    /**
     * Método usado para gerar o XML do Soap Request
     * @param $versao
     * @param $remetenteCNPJCPF
     * @param $im
     * @param $protocolo
     * @return mixed
     */
    public function render(
        $versao,
        $remetenteCNPJCPF,
        $im,
        $protocolo
    ) {
        $dom = new Dom('1.0', 'utf-8');
        //Cria o elemento pai
        $root = $dom->createElement('ConsultarSituacaoLoteRpsEnvio');
        
        //Cria os dados do prestador
        $prestador = $dom->createElement('Prestador');

        //Adiciona o Cnpj na tag Prestador
        $dom->addChild(
            $prestador,
            'Cnpj',
            $remetenteCNPJCPF,
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
        
        //Parse para XML
        $body = $dom->saveXML();
        $body = $this->clear($body);
        
        return '<?xml version="1.0" encoding="utf-8"?>' . $body;
    }
}
