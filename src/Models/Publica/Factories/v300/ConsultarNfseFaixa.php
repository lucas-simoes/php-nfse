<?php

namespace NFePHP\NFSe\Models\Publica\Factories\v300;

use NFePHP\Common\DOMImproved as Dom;
use NFePHP\NFSe\Models\Publica\Factories\SignerRps;
use NFePHP\NFSe\Models\Publica\Factories\Factory;

class ConsultarNfseFaixa extends Factory
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
        $numeroNfseInicial,
        $numeroNfseFinal
    ) {
        $dom = new Dom('1.0', 'utf-8');
        //Cria o elemento pai
        $root = $dom->createElement('ConsultarNfseFaixaEnvio');
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
        
         //Cria o elemento Faixa
         $faixa = $dom->createElement('Faixa');
        //Adiciona a tag NumeroNfseInicial na Faixa
        $dom->addChild(
            $faixa,
            'NumeroNfseInicial',
            $numeroNfseInicial,
            true,
            "NumeroNfseInicial",
            true
        );
        //Adiciona a tag NumeroNfseInicial na Faixa
        $dom->addChild(
            $faixa,
            'NumeroNfseFinal',
            $numeroNfseFinal,
            true,
            "NumeroNfseFinal",
            true
        );

        //Adiciona a tag Faixa a consulta
        $dom->appChild($root, $faixa, 'Adicionando tag Faixa');

        $body = $dom->saveXML();
        $body = $this->clear($body);
        $this->validar($versao, $body, 'Publica', 'schema_nfse_v300', '');
        
        return $body;
    }
}
