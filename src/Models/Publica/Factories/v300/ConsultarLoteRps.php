<?php

namespace NFePHP\NFSe\Models\Publica\Factories\v300;

use NFePHP\Common\DOMImproved as Dom;
use NFePHP\NFSe\Models\Publica\Factories\SignerRps;
use NFePHP\NFSe\Models\Publica\Factories\ConsultarLoteRps as ConsultarLoteRpsBase;

class ConsultarLoteRps extends ConsultarLoteRpsBase
{
    protected $xmlns = 'http://www.publica.inf.br';

    /**
     * Método usado para gerar o XML do Soap Request
     * @param $cnpj
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
        $root = $dom->createElement('ConsultarLoteRpsEnvio');
        $root->setAttribute('xmlns', 'http://www.publica.inf.br');

        //Adiciona as tags ao DOM
        $dom->appendChild($root);
        
        //Cria os dados do prestador
        $prestador = $dom->createElement('Prestador');
        $prestador->setAttribute('id', 'assinar');

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
        
        //Parse para XML
        $body = str_replace('<?xml version="1.0" encoding="utf-8"?>', '', $dom->saveXML());
        $body = SignerRps::sign(
            $this->certificate,            
            'Prestador',
            'id',
            $this->algorithm,
            [true, false, null, null],
            $dom,
            $root
        );
 
        //Adiciona a tag protoclo na consulta
        $dom->addChild(
            $root,
            'Protocolo',
            $protocolo,
            true,
            "Numero do Protocolo",
            true
        );

        $body = $dom->saveXML();
        $body = $this->clear($body);
        $this->validar($versao, $body, 'Publica', 'schema_nfse_v300', '');

        #echo '<pre>'.print_r($body).'</pre>';die;
        
        return $body;
    }
}
