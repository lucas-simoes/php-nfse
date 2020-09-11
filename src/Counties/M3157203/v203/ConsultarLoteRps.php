<?php

namespace NFePHP\NFSe\Counties\M3157203\v203;

use NFePHP\Common\DOMImproved as Dom;
use NFePHP\NFSe\Counties\M3157203\SignerRps as Signer;
use NFePHP\NFSe\Models\Abrasf\Factories\v203\ConsultarLoteRps as ConsultarLoteRps203;
use NFePHP\NFSe\Counties\M3157203\Tools\prefixos;

class ConsultarLoteRps extends ConsultarLoteRps203
{
    protected $xmlns = 'http://www.abrasf.org.br/nfse.xsd';

    public function render(
        $cnpj,
        $im,
        $protocolo
    ) {
        $dom = new Dom('1.0', 'utf-8');
        //Cria o elemento pai
        $root = $dom->createElement('sis:ConsultarLoteRpsEnvio');
        $root->setAttribute('xmlns:sis', prefixos['xmlns:sis']);        
        $root->setAttribute('xmlns:nfs', prefixos['xmlns:nfs']);

        //Cria os dados do prestador
        $prestador = $dom->createElement('nfs:Prestador');
        //Cria a tag de CpfCnpj do prestador
        $cpfCnpj = $dom->createElement('nfs:CpfCnpj');
        //Adiciona o Cnpj na tag CpfCnpj
        $dom->addChild(
            $cpfCnpj,
            'nfs:Cnpj',
            $cnpj,
            true,
            "CNPJ",
            true
        );

        $dom->addChild(
            $prestador,
            'nfs:InscricaoMunicipal',
            $im,
            true,
            'InscricaoMunicipal',
            true
        );

        //Adiciona a tag CpfCnpj na tag Prestador
        $dom->appChild($prestador, $cpfCnpj, 'Adicionando tag CpfCnpj ao Prestador');
        //Adiciona a tag Prestador a consulta
        $dom->appChild($root, $prestador, 'Adicionando tag Prestador');

        //Adiciona a tag protoclo na consulta
        $dom->addChild(
            $root,
            'nfs:Protocolo',
            $protocolo,
            true,
            "Numero do Protocolo",
            true
        );

        //Adiciona as tags ao DOM

        $dom->appendChild($root);

        $rootNode = $root;

        $signatureNode = Signer::signDoc(
            $this->certificate,
            'nfs:Prestador',
            'Id',
            $this->algorithm,
            [false, false, null, null],
            $dom,
            $rootNode
        );

        $xml = $dom->saveXML();

        $xml = str_replace('<?xml version="1.0" encoding="utf-8"?>', '', $xml);

        return $xml;
    }
}
