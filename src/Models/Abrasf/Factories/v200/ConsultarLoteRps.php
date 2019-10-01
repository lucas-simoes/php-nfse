<?php

namespace NFePHP\NFSe\Models\Abrasf\Factories\v200;

use NFePHP\Common\DOMImproved as Dom;
use NFePHP\NFSe\Models\Abrasf\Factories\ConsultarLoteRps as ConsultarLoteRpsBase;

class ConsultarLoteRps extends ConsultarLoteRpsBase
{
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
        //Cria a tag de CpfCnpj do prestador
        $cpfCnpj = $dom->createElement('CpfCnpj');
        //Adiciona o Cnpj na tag CpfCnpj
        $dom->addChild(
            $cpfCnpj,
            'Cnpj',
            $cnpj,
            true,
            "CNPJ",
            true
        );
        //Adiciona a tag CpfCnpj na tag Prestador
        $dom->appChild($prestador, $cpfCnpj, 'Adicionando tag CpfCnpj ao Prestador');
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
